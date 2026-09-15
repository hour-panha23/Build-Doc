# Signal Service - Hardware Device Scan & Attendance Integration Guide

This guide details how to integrate biometric, RFID, and facial recognition terminals (e.g. AiFace hardware devices) with the **Signal Service** and **Laravel** application to enable automated access verification and instant real-time attendance broadcasting.

---

## 1. Architecture & Workflow Overview

The hardware integration operates via the dedicated **`HardwareGateway`** in `signal-service-api`, which communicates concurrently with the physical terminal, Redis cache, the Laravel backend, and frontend display monitors.

```
+---------------------------+
| Biometric / RFID Terminal |
|    (Hardware Device)      |
+---------------------------+
              |
              | 1. Scan Record via Raw WebSocket (Port 8088 /pub/chat)
              v
+-----------------------------------------------------------------------+
| SIGNAL HARDWARE GATEWAY (signal-service-api)                          |
|   - Deduplication (Redis 3s window)                                   |
|   - Lookup Device Route: DeviceService.getProjectForDevice(sn)        |
|     -> { projectId, appId, roomId, event }                            |
+-----------------------------------------------------------------------+
              |                                        ^
              | 2. Check Permission (If Cache Miss)   | 3. Access Decision
              v                                        |    { access: 1/0 }
+-----------------------------------------------------------------------+
| LARAVEL BACKEND (routes/api.php)                                      |
|   POST /api/student/attendance/access-scan                            |
|   Headers: X-Internal-Secret                                          |
|   - Validates student enrollment & attendance schedule                |
+-----------------------------------------------------------------------+
              |
              | 4. Broadcast Real-Time Scan Event to Room
              v
+-----------------------------------------------------------------------+
| FRONTEND ATTENDANCE CLIENT (signal.js & ScanAttendanceComponent.js)   |
|   - Subscribes via /api/scan-attendance-signal-ticket                 |
|   - Renders instant avatar, check-in sound & status badge             |
+-----------------------------------------------------------------------+
```

### Key Workflow Stages

1. **Terminal Scan Ingestion**: The physical device opens a raw WebSocket connection to Signal on port `8088` (`/pub/chat`) and transmits attendance logs (`cmd: "sendlog"`).
2. **Deduplication**: Signal checks Redis (`scan:dedupe:...`) using a 3-second window (`DUPLICATE_SCAN_WINDOW_SEC = 3`) to eliminate double-scanning.
3. **Route Lookup**: Signal checks the device serial number (`sn`) against registered routes to resolve `{ projectId, appId, roomId, event }`.
4. **Access Verification (Laravel)**:
   - Signal checks its local in-memory cache and Redis (`device:user:{sn}:{userId}`).
   - On a cache miss, Signal issues a high-priority HTTP POST request to Laravel (`/api/student/attendance/access-scan`).
   - Laravel evaluates student/employee status and returns `access: 1` (allowed) or `access: 0` (denied).
5. **Terminal Response**: Signal sends an immediate acknowledgement back to the hardware device containing the decision (`access: 1` or `0`, with audible pass/deny prompt on the terminal).
6. **Real-Time UI Broadcast**: Signal emits the scan event to the connected frontend room, allowing live kiosks, gates, or dashboards to display the student card instantly.

---

## 2. Laravel Backend Verification API

When a user scans at a terminal, `signal-service-api` makes a direct HTTP POST request to Laravel to verify user access.

### 2.1 Route Definition (`routes/api.php`)

```php
use App\Http\Controllers\StudentAttendanceController;

Route::post('/student/attendance/access-scan', [StudentAttendanceController::class, 'checkAccessScan']);
```

### 2.2 Request Contract (Signal $\rightarrow$ Laravel)

- **Method**: `POST`
- **Endpoint**: `/api/student/attendance/access-scan`
- **Headers**:
  - `Content-Type: application/json`
  - `X-Internal-Secret: {INTERNAL_API_SECRET}`
- **Payload**:
```json
{
  "current_date": "2026-09-14",
  "present_time": "08:30",
  "user_id": "1052",
  "user_name": "Sokha Chan"
}
```

### 2.3 Response Contract (Laravel $\rightarrow$ Signal)

Laravel must return a JSON response with HTTP status 200, containing the `access` flag (`1` for allowed, `0` for denied) and an optional `reason`:

#### Approved Check-In Response:
```json
{
  "status_code": 200,
  "data": {
    "access": 1,
    "reason": "On-Time Check-In"
  }
}
```

#### Denied Access Response:
```json
{
  "status_code": 200,
  "data": {
    "access": 0,
    "reason": "Access Denied: Unenrolled or Suspended"
  }
}
```

---

## 3. Dedicated Kiosk Scan Ticket Route

Attendance display boards or walk-through kiosk monitors operating without standard user session logins authenticate through a dedicated scan ticket endpoint:

### 3.1 Route Definition (`routes/api.php`)

```php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/scan-attendance-signal-ticket', function (Request $request) {
    // Dedicated app ID assigned for attendance display kiosks
    $appId     = '8AE496F4C88EB47721B5B202EBDBC546';
    $secret    = config('signal.signal_secret');
    $timestamp = (string) time();
    $projectId = config('signal.signal_project_id');
    $userId    = '1'; // Dedicated Kiosk / System User ID

    $signature = hash_hmac(
        'sha256',
        "{$appId}.{$timestamp}.{$projectId}.{$userId}",
        $secret
    );

    return response()->json(compact('appId', 'timestamp', 'projectId', 'userId', 'signature'));
});
```

---

## 4. Signal Broadcast Payload to Frontend

Once access is evaluated, Signal broadcasts the scan event to the mapped attendance room:

- **Target Room**: `project:{projectId}:app:{appId}:room:{roomId}`
- **Event Name**: As configured in device routing (e.g. `student_scanned` or `scan_attendance`)
- **Payload**:

```json
{
  "user_id": "1052",
  "user_name": "Sokha Chan",
  "device_sn": "AF8923019283",
  "status": "CHECK_IN",
  "reason": "On-Time Check-In",
  "verify_mode": 1,
  "current_date": "2026-09-14",
  "present_time": "08:30:15"
}
```

### Status Reference
| Status Value | Meaning | Action on Frontend |
|---|---|---|
| `CHECK_IN` | Verified entry scan | Show green check card, play success chime |
| `CHECK_OUT` | Verified exit scan (`record.inout === 1`) | Show departure confirmation |
| `ACCESS_DENIED` | Permission denied or user lookup failed | Show red alert with `reason`, play error buzzer |

---

## 5. Frontend Client Implementation

### 5.1 Script Bundling (`config/script_bundles.php`)

For attendance monitor screens, bundle the attendance dependencies and include `signal.js`:

```php
// config/script_bundles.php
'attendance-script' => [
    'attr'        => 'defer',
    'single_file' => 1,
    'output_file' => '/dist/js/attendance.js',
    'files'       => [
        'https://cdn.socket.io/4.8.1/socket.io.min.js',
        '/js/signal.js',
        '/assets/js/sweetalert2.all.min.js',
        'https://cdn.vectoraclouds.com/vsel/components/quicktoast/QuickToast.js',
        'https://cdn.vectoraclouds.com/vsel/utils/DateHelper.js',
        'https://cdn.vectoraclouds.com/vsel/components/date_time_picker/DateTimePicker.js',
        'https://cdn.vectoraclouds.com/vsel/utils/vsapi.js',
        '/js/components/formal/ScanAttendanceComponent.js'
    ]
]
```

### 5.2 Component Listener (`ScanAttendanceComponent.js`)

In your attendance component, initialize Signal passing `isScan = true`. This instructs `signal.js` to retrieve the kiosk credentials from `/api/scan-attendance-signal-ticket`:

```javascript
// ScanAttendanceComponent.js
async function initAttendanceMonitor(roomName) {
    // 1. Initialize Signal with isScan = true
    await window.Signal.init([roomName], ["student_scanned"], true);

    // 2. Listen for incoming hardware scans
    window.Signal.addListener("student_scanned", roomName, (scanData) => {
        console.log("New scan received from terminal:", scanData);

        if (scanData.status === "CHECK_IN") {
            showSuccessCard({
                name: scanData.user_name,
                time: scanData.present_time,
                device: scanData.device_sn
            });
            playSuccessChime();
        } else if (scanData.status === "ACCESS_DENIED") {
            showDeniedAlert({
                name: scanData.user_name,
                reason: scanData.reason
            });
            playDeniedBuzzer();
        }
    });
}
```

---

## 6. Device Scan Integration Checklist

- [ ] **1. Port Availability**: Ensure port `8088` is open and accessible by terminals on the local/VPN network.
- [ ] **2. Device Registration**: Register the hardware device serial number (`sn`) in Signal Service (`POST /devices/create`).
- [ ] **3. Internal Secret**: Configure matching `INTERNAL_API_SECRET` in both Signal Service (`.env`) and Laravel.
- [ ] **4. Verification Endpoint**: Implement and test `POST /api/student/attendance/access-scan` in Laravel.
- [ ] **5. Kiosk Ticket Route**: Ensure `GET /api/scan-attendance-signal-ticket` is active in `routes/api.php`.
- [ ] **6. Client Initialization**: Ensure attendance frontend scripts call `window.Signal.init(..., true)` with `isScan = true`.

# Signal Service - Frontend Dashboard Guide

This guide explains what each module in the **Signal Service Dashboard** (`signal-service`) is used for and step-by-step how to use it.

---

## 1. Environment Configuration

The dashboard connects to the Signal Service backend through a single environment variable configured in `.env` or `.env.local`:

```env
BACKEND_URL=http://localhost:4000
```

- **`BACKEND_URL`**: The base URL of the Signal Service backend (running on port 4000 by default). All dashboard API requests and WebSocket handshakes route through this endpoint.

---

## 2. Real-Time Monitoring Module (`/monitoring`)

### 2.1 What It Is For
The **Real-Time Monitoring** module is an operational and debugging cockpit for developers and administrators. It allows you to:
- **Test WebSocket Connections**: Connect directly to the `/notifications` gateway with custom credentials and observe live connection states, reconnect attempts, and disconnect reasons.
- **Track Latency in Real Time**: Monitor active round-trip time (RTT latency in ms) via real-time engine pings and scheduled health probes.
- **Manage Channels & Rooms**: Join or leave subscription rooms, track active room memberships, and automatically rejoin rooms upon connection dropouts.
- **Test Server-to-Server (S2S) Broadcasts**: Emit test events across any scope (`broadcast`, `project`, `app`, `room`, `user`) directly from the browser using pre-built payload templates or custom JSON.
- **Generate Signed cURL Commands**: Copy ready-to-run, cryptographically signed (`HMAC-SHA256`) cURL commands to test your API from terminals or backend scripts.
- **Inspect Live Event & Terminal Logs**: View incoming events received by joined rooms, monitor client connect/disconnect activity, review outbound emit logs, and filter logs by severity level (`ALL`, `INFO`, `WARN`, `ERROR`, `EMIT`).

---

### 2.2 How to Use It

#### Step 1: Connect to the WebSocket Gateway
1. Navigate to **Monitoring** (`/monitoring`) in the sidebar.
2. In the **Connection Control** card:
   - Enter your **Project ID** and **App ID**.
   - Provide your **Secret Key**.
   - Choose **Mode**:
     - `Client Mode`: Simulates a standard client subscriber.
     - `Admin Mode`: Subscribes to the admin telemetry feed to receive server-wide client connect/disconnect events and emit logs.
   - (Optional) Toggle **Auto-Reconnect** if you want the socket to continuously retry upon server restarts.
3. Click **Connect**.
4. Once connected:
   - The status badge turns green (**CONNECTED**).
   - Your active **Socket ID** is displayed (click it to copy).
   - Live **Latency** is displayed in milliseconds (e.g., `12ms`).

#### Step 2: Sync Credentials & Join Rooms
1. Click **"Sync to Sections"** at the top right of the Auth panel. This automatically fills your Project ID and App ID into the Channel Manager and S2S Broadcaster.
2. In the **Channel & Presence Manager** card:
   - Enter a **Room Name** (e.g., `invoices` or `attendance_kiosk_gate1`).
   - Check **"Auto-rejoin on reconnect"** to ensure your subscription is automatically restored if the network drops.
   - Click **Join Room**.
3. Your active rooms will appear as pill badges under **Joined Rooms**:
   - Click **"Target"** on any room badge to immediately load that room name into the S2S Broadcaster target field.
   - Click **Leave** (or the red cross) to unsubscribe from a room.

#### Step 3: Test Broadcasts (Server-to-Server Emitter)
1. In the **Server-to-Server (S2S) Broadcaster** card:
   - Select the broadcast **Scope**:
     - `broadcast`: Broadcasts to all connected clients across the entire service.
     - `project`: Emits to all clients belonging to the specified Project ID.
     - `app`: Emits to all clients within a specific App ID.
     - `room`: Emits only to clients subscribed to a specific Room Name.
     - `user`: Emits directly to a specific User ID.
   - Enter the **Target** (e.g. the Room Name or User ID, depending on selected scope).
   - Enter the **Event Name** (e.g., `notification`, `invoice_created`, `student_scanned`).
2. Prepare the payload:
   - Select a pre-built template from the **Payload Preset** dropdown (`Notification`, `Invoice Created`, `Order Status`, or `Heartbeat Ping`), OR
   - Enter custom JSON in the payload editor.
   - Click **"Format JSON"** to format and validate syntax.
3. Dispatch or Copy:
   - Click **"Dispatch S2S"** to send the event immediately through the Signal API.
   - Click **"Copy as cURL"** to copy a signed cURL command with `x-project-id`, `x-timestamp`, and `x-signature` headers ready to paste into your terminal.

#### Step 4: Monitor Telemetry & Terminal Logs
1. **Telemetry Tabs**:
   - **Client Events**: View connected client sessions, disconnections, and active client count in real time (requires Admin Mode).
   - **Emit Logs**: View history of outgoing broadcast events with event names, scopes, and target details.
2. **Client Terminal**:
   - Displays all incoming room events received by the socket in real time (e.g., `[Inbound "student_scanned"] { ... }`).
   - Filter logs using the category pills: `ALL`, `INFO`, `WARN`, `ERROR`, `EMIT`.
   - Click **"Copy Logs"** to copy the terminal output to your clipboard.
   - Toggle **"Auto-scroll"** to lock terminal view to the latest incoming messages.
   - Click **"Clear"** to wipe terminal output.

---

## 3. Project Management Module (`/project`)

### 3.1 What It Is For
The **Project Management** module provides multi-tenant project administration for the Signal Service. It is used for:
- **Tenant Management**: Creating and organizing independent projects/applications that utilize real-time messaging.
- **API Credential Issuance**: Generating unique `project_id` identifiers and cryptographic `secret_key` HMAC tokens required by external backends (such as Laravel, Node.js, or mobile apps).
- **Key Rotation**: Securely regenerating secret keys when credentials need rotation or have been compromised.
- **Webhook Configuration**: Registering project-level webhook URLs to receive asynchronous delivery notifications or event triggers.
- **Access Control**: Enabling or disabling project access on demand to immediately accept or halt event processing.

---

### 3.2 How to Use It

#### Step 1: Create a Project
1. Navigate to **Projects** (`/project`) in the sidebar.
2. Click **"Create Project"** in the top-right corner.
3. In the modal:
   - **Project Name**: Enter a descriptive name (e.g., `Learning Hub` or `HR Portal`).
   - **Webhook URL**: (Optional) Enter your backend endpoint that receives event webhooks (e.g., `https://api.example.com/webhooks/signal`).
   - **Description**: Add notes about the project's purpose.
4. Click **Save**. The project will be created and displayed in the projects table.

#### Step 2: Retrieve Credentials for Backend Integration
1. Locate the newly created project in the list.
2. Click the **Key icon** (or "View Secret") to open the credentials modal.
3. Reveal and copy the unmasked **Secret Key** (`secret_key`).
4. Add the credentials to your backend environment file (e.g. Laravel `.env`):
   ```env
   SIGNAL_HOST=http://localhost:4000
   SIGNAL_PROJECT_ID=proj_your_project_id
   SIGNAL_SECRET=your_project_secret_key
   ```

#### Step 3: Regenerate / Rotate Secret Keys
1. If credentials are leaked or require periodic security rotation, click the **Rotate / Refresh icon** on the project row.
2. Read the confirmation dialog carefully: *Regenerating a secret immediately invalidates the previous secret, and any backend using the old key will be rejected.*
3. Confirm rotation.
4. Copy the new secret key and update your backend `.env` immediately.

#### Step 4: Edit or Suspend Projects
- **Edit Details**: Click the **Edit (Pencil)** icon to update the project name, description, or webhook URL.
- **Toggle Status**: Use the **Active / Inactive** toggle switch. Deactivating a project immediately blocks all inbound emissions and socket authentications for that project without deleting historical data.
- **Delete Project**: Click the **Trash** icon to permanently remove an obsolete project.

---

## 4. Device Management Module (`/device`)

### 4.1 What It Is For
The **Device Management** module registers and configures physical hardware devices—such as facial recognition kiosks, RFID turnstiles, and biometric scanners (e.g., AiFace terminals connecting on raw WebSocket port `8088`).

It is used for:
- **Terminal Registration**: Associating physical device hardware serial numbers (`sn`) with the Signal Service.
- **Dynamic Route Mapping**: Defining which `project_id`, `app_id`, and real-time `room` a physical device's scans are routed to in Redis cache.
- **Event Naming**: Setting the event name emitted to clients when a scan occurs (e.g., `student_scanned`, `attendance_logged`).
- **Webhook Verification Routing**: Specifying the backend verification webhook URL that Signal Service calls to resolve user profiles when not found in Redis cache.

---

### 4.2 How to Use It

#### Step 1: Register a New Device
1. Navigate to **Devices** (`/device`) in the sidebar.
2. Click **"Create Device"** in the top-right corner.
3. Fill in the device registration form:
   - **Device Name**: A human-readable name for the device (e.g., `Gate 1 Attendance Terminal`).
   - **Device ID**: An internal numeric or alphanumeric ID (e.g., `1001`).
   - **Device Serial (`device_serial`)**: The exact hardware serial number (`sn`) configured on the physical device (e.g., `SN-99882-Y`).
   - **Project ID**: The tenant project ID the device belongs to (e.g., `proj_school`).
   - **App ID**: The application scope (e.g., `app_attendance`).
   - **Room (`room`)**: The room channel clients subscribe to in order to receive live scans (e.g., `attendance_kiosk_gate1`).
   - **Event Name (`event`)**: The WebSocket event name emitted when someone scans (e.g., `student_scanned`).
   - **Webhook URL (`webhook`)**: The backend verification URL used by Signal Service to fetch scan user metadata if not yet cached in Redis.
4. Click **Save**. The routing rule is immediately saved and stored in Redis.

#### Step 2: Dynamically Re-Route Device Scans
If physical terminal hardware is relocated (e.g., moved from `Gate 1` to `Library Entrance`):
1. Click the **Edit (Pencil)** icon next to the device.
2. Change the **Room** (e.g. from `attendance_kiosk_gate1` to `attendance_library`) or **Event Name**.
3. Click **Update**.
4. The Redis route mapping is updated instantly. The physical device continues sending scans without needing any reboot or firmware reconfiguration.

#### Step 3: Delete or Retire a Device
1. When hardware is decommissioned or replaced, click the **Delete (Trash)** icon next to the device.
2. Confirm deletion to remove the device and its Redis routing record.

---

## 5. Quick Reference Summary

| Module | Primary Purpose | Key User Actions |
| :--- | :--- | :--- |
| **`/monitoring`** | Operational cockpit for WebSocket debugging, latency tracking & testing broadcasts | Connect with HMAC auth; join rooms with auto-rejoin; test S2S emissions with presets; copy signed cURL; inspect live inbound terminal logs & telemetry. |
| **`/project`** | Multi-tenant administration, credential management & status control | Create projects; reveal & copy `secret_key` for backend `.env`; rotate secrets; configure webhook URLs; toggle active/inactive status. |
| **`/device`** | Hardware terminal registration & Redis routing configuration | Register terminals by serial number (`sn`); map devices to Project, App, Room & Event; set verification webhooks; dynamically re-route physical hardware. |

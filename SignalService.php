<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Throwable;

enum EmitTarget: string
{
    case ALL = 'all';
    case SOMEONE = 'someone';
    case SELF_EXCEPT = 'self_except';
}

enum EmitType: string
{
    case REALTIME = 'realtime';
    case NOTIFY = 'notify';
}

class SignalService
{
    private static string $baseUrl;
    private static string $projectId;
    private static string $secret;

    private static function init(): void
    {
        if (isset(self::$projectId)) {
            return;
        }

        self::$baseUrl = config('signal.signal_url') ?? '';
        self::$projectId = config('signal.signal_project_id') ?? '';
        self::$secret = config('signal.signal_secret') ?? '';
    }

    private static function sign(string $timestamp, string $rawBody): string
    {
        return hash_hmac(
            'sha256',
            self::$projectId . "." . $timestamp,
            self::$secret
        );
    }

    public static function get(string $path, array $query = [], int $timeout = 5): array
    {
        self::init();

        $timestamp = (string) time();

        $response = Http::timeout($timeout)
            ->withHeaders([
                'x-project-id' => self::$projectId,
                'x-timestamp' => $timestamp,
                'x-signature' => self::sign($timestamp, ''),
            ])->get(self::$baseUrl . "/notifications/{$path}", $query);

        return $response->json() ?? [];
    }

    /**
     * Non-blocking status check with strict timeout, caching, and fallback error handling.
     */
    public static function getServiceStatus(int $ttlSeconds = 30): array
    {
        // Cache status for $ttlSeconds to avoid hitting the external service on every request
        return Cache::remember('signal_service_status', $ttlSeconds, function () {
            try {
                // Set a strict 2-second timeout so slow responses don't delay other processes
                $status = self::get('status', [], timeout: 2);

                return [
                    'status_code' => 200,
                    'status' => true,
                    'data' => $status,
                ];
            } catch (Throwable $e) {
                // Log the exception without throwing it to keep process flow intact
                Log::warning('SignalService status check failed: ' . $e->getMessage());

                return [
                    'status_code' => 503,
                    'status' => false,
                    'data' => [
                        'error' => 'Service unreachable',
                    ],
                ];
            }
        });
    }

    public static function post(string $path, array $body): array
    {
        self::init();

        $timestamp = (string) time();
        $rawBody = json_encode($body);

        $response = Http::withHeaders([
            'x-project-id' => self::$projectId,
            'x-timestamp' => $timestamp,
            'x-signature' => self::sign($timestamp, $rawBody),
            'Content-Type' => 'application/json',
        ])
            ->withBody($rawBody, 'application/json')
            ->post(self::$baseUrl . "/notifications/{$path}");

        return $response->json() ?? [];
    }

    public static function taskEmit(
        string $event,
        array $payload,
        string $socketId = null,
        string $user_class = null,
        ?string $userId = null,
        ?string $room = null,
        ?bool $selfEmit = false,
    ): array {
        if (!$socketId)
            return [];
        self::init();

        $data = array_filter([
            'project_id' => self::$projectId,
            'app_id' => getAppIdByUserClass($user_class),
            'user_id' => $userId,
            'room' => $room,
            'event' => $event,
            'payload' => $payload,
            'sender_socket_id' => $socketId,
            'self_emit' => $selfEmit,
        ], fn($value) => $value !== null);

        return self::post('emit', $data);
    }

    public static function eventEmit(
        string $event,
        array $payload,
        EmitTarget|string $target = EmitTarget::ALL,
        EmitType|string $type = EmitType::REALTIME,
        ?string $appId = null,
        ?string $userId = null,
        ?string $room = null,
    ): array {
        self::init();

        Log::info(self::$projectId . ' ' . $event . ' ' . json_encode($payload));

        $targetVal = $target instanceof EmitTarget ? $target->value : $target;
        $typeVal = $type instanceof EmitType ? $type->value : $type;
        \Log::info( ' payload' . json_encode($payload));

        $data = array_filter([
            'project_id' => self::$projectId,
            'app_id' => $appId,
            'user_id' => $userId,
            'room' => $room,
            'event' => $event,
            'payload' => $payload,
            'self_emit' => $target !== EmitTarget::SELF_EXCEPT,
        ], fn($value) => $value !== null);

        return self::post('emit', $data);
    }

    // public static function notificationEmit(
    //     string $event,
    //     array $payload,
    //     ?string $appId = null,
    //     ?string $userId = null,
    //     ?string $room = null,
    // ): array {
    //     self::init();

    //     Log::info(self::$projectId . '' . $event . '' . json_encode($payload));

    //     $data = array_filter([
    //         'project_id' => self::$projectId,
    //         'app_id' => $appId,
    //         'user_id' => $userId,
    //         'room' => $room,
    //         'event' => $event,
    //         'payload' => json_encode($payload),
    //         'self_emit' => false
    //     ], fn($value) => $value !== null);

    //     return self::post('emit', $data);
    // }
}

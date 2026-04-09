<?php

namespace App\Services;

use App\Models\AssistanceRequest;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\RequestEvent;
use App\Models\RequestHistory;

class AssistanceRequestLifecycleService
{
    public function createTimelineEntry(AssistanceRequest $assistanceRequest, string $status, string $eventType, array $eventData = []): void
    {
        RequestHistory::create([
            'request_id' => $assistanceRequest->id,
            'status' => $status,
        ]);

        RequestEvent::create([
            'request_id' => $assistanceRequest->id,
            'event_type' => $eventType,
            'event_data' => $eventData,
        ]);
    }

    public function notifyUser(int $userId, string $type, string $message): void
    {
        Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'message' => $message,
            'is_read' => false,
        ]);
    }

    public function audit(?int $userId, string $action, array $description = []): void
    {
        if (is_null($userId)) {
            return;
        }

        AuditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'description' => json_encode($description, JSON_UNESCAPED_UNICODE),
        ]);
    }
}

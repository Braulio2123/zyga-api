<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $notifications = Notification::query()
            ->where('user_id', $request->user()->id)
            ->orderByDesc('id')
            ->paginate(15);

        return response()->json($notifications);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $notification = Notification::query()
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        $history = NotificationHistory::query()
            ->where('notification_id', $notification->id)
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'data' => [
                'notification' => $notification,
                'history' => $history,
            ],
        ]);
    }

    public function markAsRead(Request $request, int $id): JsonResponse
    {
        $notification = Notification::query()
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        if (!$notification->is_read) {
            $notification->update([
                'is_read' => true,
            ]);

            NotificationHistory::create([
                'notification_id' => $notification->id,
                'status' => 'read',
                'read_at' => now(),
            ]);
        }

        return response()->json([
            'message' => 'Notificación marcada como leída.',
            'data' => $notification->fresh(),
        ]);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $notifications = Notification::query()
            ->where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->get();

        foreach ($notifications as $notification) {
            $notification->update([
                'is_read' => true,
            ]);

            NotificationHistory::create([
                'notification_id' => $notification->id,
                'status' => 'read',
                'read_at' => now(),
            ]);
        }

        return response()->json([
            'message' => 'Todas las notificaciones fueron marcadas como leídas.',
            'count' => $notifications->count(),
        ]);
    }
}

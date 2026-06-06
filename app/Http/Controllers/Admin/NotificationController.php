<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(): JsonResponse
    {
        $admin = Auth::guard('admin')->user();

        $notifications = $admin->unreadNotifications()
            ->latest()
            ->take(10)
            ->get()
            ->map(fn ($n) => [
                'id'         => $n->id,
                'message'    => $n->data['message'] ?? '',
                'type'       => $n->data['type'] ?? '',
                'created_at' => $n->created_at->diffForHumans(),
            ]);

        return response()->json([
            'notifications' => $notifications,
            'unread_count'  => $admin->unreadNotifications()->count(),
        ]);
    }

    public function markAllRead(): JsonResponse
    {
        Auth::guard('admin')->user()->unreadNotifications()->update(['read_at' => now()]);

        return response()->json(['ok' => true]);
    }
}

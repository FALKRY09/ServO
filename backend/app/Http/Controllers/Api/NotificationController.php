<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request) { return Notification::where('user_id', $request->user()->id)->latest()->paginate(20); }

    public function unreadCount(Request $request) { return response()->json(['count' => Notification::where('user_id', $request->user()->id)->whereNull('read_at')->count()]); }

    public function read(Request $request, Notification $notification)
    {
        abort_unless($notification->user_id === $request->user()->id, 403);
        $notification->update(['read_at' => now()]);
        return $notification->fresh();
    }
}

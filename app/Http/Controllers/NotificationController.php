<?php

namespace App\Http\Controllers;

use App\Models\NotificationMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        return view('notifications.index', [
            'messages' => NotificationMessage::latest()->paginate(12),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        NotificationMessage::create($request->validate([
            'channel' => ['required', 'in:system,email,whatsapp,internal'],
            'recipient' => ['required', 'string', 'max:120'],
            'subject' => ['required', 'string', 'max:160'],
            'message' => ['required', 'string', 'max:1000'],
        ]) + ['status' => 'pending']);

        return redirect()->route('admin.notifications.index')->with('status', 'Pesan komunikasi dibuat.');
    }

    public function markAsRead(NotificationMessage $message): RedirectResponse
    {
        $message->update([
            'status' => 'read',
            'read_at' => now(),
        ]);

        return redirect()->route('admin.notifications.index')->with('status', 'Notifikasi ditandai dibaca.');
    }
}

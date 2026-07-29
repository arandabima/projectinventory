<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChatMessageRequest;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use App\Notifications\NewChatMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function userIndex(Request $request): View
    {
        $chat = Chat::firstOrCreate([
            'user_id' => $request->user()->id,
        ]);
        Gate::authorize('view', $chat);

        return view('user.chat.index', [
            'chat' => $chat->load(['messages.sender', 'messages.receiver']),
        ]);
    }

    public function adminIndex(): View
    {
        Gate::authorize('viewAny', Chat::class);

        return view('admin.chat.index', [
            'chats' => Chat::with(['user', 'messages' => fn ($q) => $q->latest()])->latest('last_message_at')->paginate(12),
        ]);
    }

    public function store(StoreChatMessageRequest $request, Chat $chat): RedirectResponse
    {
        Gate::authorize('reply', $chat);
        $data = $request->validated();

        $receiver = $request->user()->isAdmin()
            ? User::findOrFail($chat->user_id)
            : User::where('role', 'admin')->firstOrFail();

        $message = $chat->messages()->create([
            'sender_id' => $request->user()->id,
            'receiver_id' => $receiver->id,
            'message' => $data['message'],
            'read_status' => false,
        ]);

        $chat->update(['last_message_at' => now()]);
        $receiver->notify(new NewChatMessage($message));

        return back()->with('status', 'Pesan terkirim.');
    }

    public function read(Message $message, Request $request): RedirectResponse
    {
        Gate::authorize('reply', $message->chat);
        $message->update(['read_status' => true, 'read_at' => now()]);

        return back();
    }
}

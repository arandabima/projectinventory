@extends('layouts.admin')

@section('title', 'Chat Admin')

@section('content')
    <h1>Chat User</h1>
    <section class="grid cols-2">
        @forelse ($chats as $chat)
            <div class="panel">
                <h2>{{ $chat->user?->name }}</h2>
                <div class="muted">{{ $chat->user?->email }}</div>
                <div class="stack" style="margin-top:12px;">
                    @foreach ($chat->messages->take(5) as $message)
                        <div style="border:1px solid var(--line); border-radius:12px; padding:10px;">
                            <strong>{{ $message->sender?->name }}</strong>
                            <div class="muted">{{ $message->created_at->format('d M Y H:i') }}</div>
                            <p>{{ $message->message }}</p>
                        </div>
                    @endforeach
                </div>
                <form method="post" action="{{ route('admin.chat.store', $chat) }}" style="margin-top:12px;">
                    @csrf
                    <textarea name="message" placeholder="Balas chat..." required></textarea>
                    <button type="submit" style="margin-top:8px;">Kirim</button>
                </form>
            </div>
        @empty
            <div class="panel">Belum ada chat.</div>
        @endforelse
    </section>
    <div class="pagination">{{ $chats->links() }}</div>
@endsection

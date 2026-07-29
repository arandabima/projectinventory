@extends('layouts.user')

@section('title', 'Chat Admin')

@section('content')
    <div class="topbar">
        <div>
            <h1>Chat dengan Admin</h1>
            <div class="muted">Kirim pesan untuk bertanya tentang barang atau pengajuan.</div>
        </div>
    </div>

    <section class="grid cols-2">
        <div class="panel">
            <h2>Pesan Baru</h2>
            <form method="post" action="{{ route('user.chat.store', $chat) }}" class="stack">
                @csrf
                <textarea name="message" placeholder="Tulis pesan..." required></textarea>
                <button type="submit">Kirim</button>
            </form>
        </div>

        <div class="panel">
            <h2>Riwayat</h2>
            <div class="stack">
                @foreach ($chat->messages as $message)
                    <div style="padding:12px; border:1px solid var(--line); border-radius:12px;">
                        <strong>{{ $message->sender?->name }}</strong>
                        <div class="muted">{{ $message->created_at->format('d M Y H:i') }}</div>
                        <p>{{ $message->message }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection

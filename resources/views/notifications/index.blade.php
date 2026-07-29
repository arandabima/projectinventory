@extends('layouts.app')

@section('content')
    <div class="topbar">
        <div>
            <h1>Notif & Komunikasi</h1>
            <div class="muted">Kelola pesan internal, email/WhatsApp placeholder, dan notifikasi stok menipis.</div>
        </div>
    </div>

    <section class="grid cols-2">
        <div class="panel">
            <h2>Buat Pesan</h2>
            <form method="post" action="{{ route('admin.notifications.store') }}" class="form-grid">
                @csrf
                <div class="field">
                    <label for="channel">Channel</label>
                    <select id="channel" name="channel" required>
                        <option value="internal">Internal</option>
                        <option value="system">System</option>
                        <option value="email">Email</option>
                        <option value="whatsapp">WhatsApp</option>
                    </select>
                </div>
                <div class="field">
                    <label for="recipient">Penerima</label>
                    <input id="recipient" name="recipient" value="{{ old('recipient', 'gudang') }}" required>
                </div>
                <div class="field full">
                    <label for="subject">Subjek</label>
                    <input id="subject" name="subject" value="{{ old('subject') }}" required>
                </div>
                <div class="field full">
                    <label for="message">Pesan</label>
                    <textarea id="message" name="message" required>{{ old('message') }}</textarea>
                </div>
                <div class="field full">
                    <button type="submit">Kirim ke Antrian</button>
                </div>
            </form>
        </div>

        <div class="panel">
            <h2>Daftar Notifikasi</h2>
            <div class="stack">
                @forelse ($messages as $message)
                    <article style="border-bottom: 1px solid var(--line); padding-bottom: 12px;">
                        <div class="toolbar">
                            <h3>{{ $message->subject }}</h3>
                            <span @class(['badge', 'warn' => $message->status === 'pending', 'ok' => $message->status === 'read'])>{{ $message->status }}</span>
                        </div>
                        <div class="muted">{{ strtoupper($message->channel) }} ke {{ $message->recipient }} · {{ $message->created_at->format('d M Y H:i') }}</div>
                        <p>{{ $message->message }}</p>
                        @if ($message->status !== 'read')
                            <form method="post" action="{{ route('admin.notifications.read', $message) }}">
                                @csrf
                                @method('patch')
                                <button type="submit" class="button secondary">Tandai Dibaca</button>
                            </form>
                        @endif
                    </article>
                @empty
                    <p class="muted">Belum ada notifikasi.</p>
                @endforelse
            </div>
            <div class="pagination">{{ $messages->links() }}</div>
        </div>
    </section>
@endsection

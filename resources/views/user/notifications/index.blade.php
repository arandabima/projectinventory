@extends('layouts.user')

@section('title', 'Notifikasi')

@section('content')
    <div class="topbar">
        <div>
            <h1>Notifikasi</h1>
            <div class="muted">Status pengajuan dan pesan sistem.</div>
        </div>
    </div>

    <section class="panel">
        <div class="stack">
            @forelse ($notifications as $notification)
                <article style="border-bottom:1px solid var(--line); padding-bottom:12px;">
                    <strong>{{ $notification->data['message'] ?? class_basename($notification->type) }}</strong>
                    <div class="muted">{{ $notification->created_at->format('d M Y H:i') }}</div>
                </article>
            @empty
                <p class="muted">Belum ada notifikasi.</p>
            @endforelse
        </div>
        <div class="pagination">{{ $notifications->links() }}</div>
    </section>
@endsection

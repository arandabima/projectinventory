<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') - {{ config('app.name', 'Inventory Laravel') }}</title>
    <style>
        :root {
            --bg: #07111f;
            --panel: rgba(15, 23, 42, .82);
            --line: rgba(148, 163, 184, .18);
            --text: #e2e8f0;
            --muted: #94a3b8;
            --accent: #22c55e;
            --accent-2: #38bdf8;
            --danger: #ef4444;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: Inter, ui-sans-serif, system-ui, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(56, 189, 248, .12), transparent 28%),
                radial-gradient(circle at bottom right, rgba(34, 197, 94, .10), transparent 24%),
                #f8fafc;
            color: #0f172a;
        }
        a { color: inherit; text-decoration: none; }
        .shell { display: grid; grid-template-columns: 280px 1fr; min-height: 100vh; }
        aside {
            background: linear-gradient(180deg, #0f172a 0%, #111827 100%);
            color: var(--text);
            padding: 24px 18px;
            border-right: 1px solid rgba(255,255,255,.06);
            position: sticky;
            top: 0;
            height: 100vh;
            overflow: auto;
        }
        main { padding: 28px; min-width: 0; }
        .brand {
            font-size: 20px;
            font-weight: 900;
            letter-spacing: .2px;
            margin-bottom: 6px;
        }
        .muted { color: var(--muted); font-size: 13px; }
        .nav { display: grid; gap: 8px; margin-top: 22px; }
        .nav a, .nav button {
            border: 1px solid transparent;
            border-radius: 14px;
            padding: 11px 12px;
            text-align: left;
            font: inherit;
            background: transparent;
            color: var(--text);
            cursor: pointer;
        }
        .nav a:hover, .nav button:hover { background: rgba(255,255,255,.06); border-color: rgba(255,255,255,.08); }
        .nav a.active { background: rgba(34, 197, 94, .14); border-color: rgba(34, 197, 94, .24); }
        .sidebar-card {
            margin-top: 22px;
            padding: 14px;
            border: 1px solid var(--line);
            border-radius: 16px;
            background: rgba(255,255,255,.04);
        }
        .sidebar-card strong { display: block; margin-bottom: 4px; }
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 20px;
        }
        h1 { margin: 0; font-size: 30px; }
        h2 { margin: 0 0 14px; font-size: 18px; }
        .kpi-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; }
        .card {
            background: rgba(255,255,255,.88);
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, .06);
        }
        .panel { background: rgba(255,255,255,.92); border: 1px solid #e2e8f0; border-radius: 18px; padding: 20px; box-shadow: 0 10px 30px rgba(15, 23, 42, .06); }
        .grid { display: grid; gap: 16px; }
        .grid.cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .grid.cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .stack { display: grid; gap: 12px; }
        .stat { display: grid; gap: 6px; }
        .stat strong { color: #0f172a; font-size: 32px; line-height: 1.1; }
        form { margin: 0; }
        .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
        .field.full { grid-column: 1 / -1; }
        label { display: block; margin-bottom: 6px; color: #334155; font-size: 13px; font-weight: 750; }
        input, select, textarea { width: 100%; min-height: 42px; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 10px; background: #fff; color: #0f172a; font: inherit; transition: border-color .15s, box-shadow .15s; }
        input[type="file"] { padding: 8px; }
        textarea { min-height: 102px; resize: vertical; }
        input:focus, select:focus, textarea:focus { outline: 0; border-color: #22c55e; box-shadow: 0 0 0 3px rgba(34, 197, 94, .15); }
        button, .button { display: inline-flex; align-items: center; justify-content: center; min-height: 42px; padding: 0 14px; border: 0; border-radius: 10px; background: linear-gradient(135deg, #22c55e, #16a34a); color: #fff; cursor: pointer; font: inherit; font-weight: 750; transition: transform .15s, filter .15s; }
        button:hover, .button:hover { filter: brightness(.96); transform: translateY(-1px); }
        .button.ghost { background: #e2e8f0; color: #0f172a; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th, td { padding: 13px 10px; border-bottom: 1px solid #e2e8f0; text-align: left; vertical-align: middle; }
        th { color: #64748b; font-size: 11px; font-weight: 800; letter-spacing: .04em; text-transform: uppercase; }
        tbody tr:hover { background: #f8fafc; }
        .alert { margin-bottom: 16px; padding: 13px 15px; border: 1px solid #bbf7d0; border-radius: 12px; background: #f0fdf4; color: #166534; }
        .alert.danger { border-color: #fecaca; background: #fff1f2; color: #b91c1c; }
        .pagination { margin-top: 16px; }
        .pagination nav { display: flex; flex-wrap: wrap; gap: 4px; }
        .metric { display: grid; gap: 6px; }
        .metric strong { font-size: 32px; line-height: 1; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 12px 10px; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
        th { font-size: 12px; text-transform: uppercase; color: #64748b; }
        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 4px 10px;
            border-radius: 999px;
            background: #e2e8f0;
            color: #334155;
            font-size: 12px;
            font-weight: 800;
        }
        .actions { display: flex; gap: 8px; flex-wrap: wrap; }
        .btn {
            border: 0;
            border-radius: 12px;
            padding: 10px 14px;
            font: inherit;
            font-weight: 800;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn.primary { background: linear-gradient(135deg, #22c55e, #16a34a); color: #fff; }
        .btn.dark { background: #0f172a; color: #fff; }
        .btn.ghost { background: #e2e8f0; color: #0f172a; }
        @media (max-width: 980px) {
            .shell { grid-template-columns: 1fr; }
            aside { position: static; height: auto; }
            .kpi-grid { grid-template-columns: 1fr; }
            .topbar { align-items: flex-start; flex-direction: column; }
            main { padding: 18px; }
            .grid.cols-2, .grid.cols-3, .form-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="shell">
    <aside>
        <div class="brand">Admin Console</div>
        <div class="muted">Kelola inventory, borrowing, chat, dan laporan.</div>

        <div class="nav">
            <a href="{{ route('admin.dashboard') }}" @class(['active' => request()->routeIs('admin.dashboard')])>Dashboard</a>
            <a href="{{ route('admin.items.index') }}" @class(['active' => request()->routeIs('admin.items.*')])>Barang</a>
            <a href="{{ route('admin.categories.index') }}" @class(['active' => request()->routeIs('admin.categories.*')])>Kategori</a>
            <a href="{{ route('admin.borrowings.index') }}" @class(['active' => request()->routeIs('admin.borrowings.*')])>Borrowing</a>
            <a href="{{ route('admin.chat.index') }}" @class(['active' => request()->routeIs('admin.chat.*')])>Chat</a>
            <a href="{{ route('admin.notifications.index') }}" @class(['active' => request()->routeIs('admin.notifications.*')])>Notifikasi</a>
            <a href="{{ route('admin.reports.index') }}" @class(['active' => request()->routeIs('admin.reports.*')])>Laporan</a>
            <a href="{{ route('admin.transactions.index') }}" @class(['active' => request()->routeIs('admin.transactions.*') || request()->routeIs('admin.orders.*')])>Order Lama</a>
            <form id="admin-logout" method="post" action="{{ route('admin.logout') }}">
                @csrf
            </form>
            <button type="button" class="btn ghost" onclick="document.getElementById('admin-logout').submit()">Logout</button>
        </div>

        <div class="sidebar-card">
            <strong>{{ auth()->user()?->name }}</strong>
            <div class="muted">{{ auth()->user()?->username ?? auth()->user()?->email }}</div>
            <div style="margin-top:8px;"><span class="badge">ADMIN</span></div>
        </div>
    </aside>

    <main>
        @if (session('status'))
            <div class="card" style="border-color:#bbf7d0; background:#f0fdf4; color:#166534; margin-bottom:16px;">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="card" style="border-color:#fecaca; background:#fff1f2; color:#991b1b; margin-bottom:16px;">
                <strong>Periksa kembali input:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</div>
</body>
</html>

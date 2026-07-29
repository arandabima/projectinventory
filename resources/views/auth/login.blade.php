<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login User - {{ config('app.name', 'Inventory Laravel') }}</title>
    <style>
        :root {
            color-scheme: light;
            --ink: #17202a;
            --muted: #667085;
            --line: #d8dee8;
            --brand: #166534;
            --brand-strong: #14532d;
            --surface: #f4f7fb;
            --danger: #b42318;
        }
        * { box-sizing: border-box; }
        body {
            align-items: center;
            background: var(--surface);
            color: var(--ink);
            display: flex;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            justify-content: center;
            line-height: 1.5;
            margin: 0;
            min-height: 100vh;
            padding: 20px;
        }
        .login {
            background: #ffffff;
            border: 1px solid var(--line);
            border-radius: 8px;
            max-width: 420px;
            padding: 24px;
            width: 100%;
        }
        h1 { font-size: 26px; margin: 0 0 4px; }
        .muted { color: var(--muted); font-size: 14px; margin-bottom: 20px; }
        .google {
            align-items: center;
            background: var(--brand);
            border-radius: 8px;
            color: #ffffff;
            display: flex;
            font-weight: 800;
            justify-content: center;
            min-height: 42px;
            text-decoration: none;
            width: 100%;
        }
        .google:hover { background: var(--brand-strong); }
        .errors {
            background: #fff1f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            color: var(--danger);
            font-size: 14px;
            margin-bottom: 16px;
            padding: 10px 12px;
        }
    </style>
</head>
<body>
    <main class="login">
        <h1>Login User</h1>
        <div class="muted">Gunakan Google untuk masuk sebagai user.</div>

        @if ($errors->any())
            <div class="errors">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <a class="google" href="{{ route('auth.google.redirect') }}">Masuk dengan Google</a>
        <div style="margin-top: 14px; font-size: 13px; color: var(--muted); text-align: center;">
            Admin login tersedia di halaman terpisah.
        </div>
    </main>
</body>
</html>

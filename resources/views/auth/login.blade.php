<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - {{ config('app.name', 'Inventory Laravel') }}</title>
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
        .field { margin-bottom: 14px; }
        label { display: block; font-size: 13px; font-weight: 800; margin-bottom: 6px; }
        input {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            color: var(--ink);
            font: inherit;
            padding: 10px 11px;
            width: 100%;
        }
        .remember {
            align-items: center;
            display: flex;
            gap: 8px;
            margin-bottom: 16px;
        }
        .remember input { width: auto; }
        button {
            background: var(--brand);
            border: 0;
            border-radius: 8px;
            color: #ffffff;
            cursor: pointer;
            font: inherit;
            font-weight: 800;
            min-height: 42px;
            width: 100%;
        }
        button:hover { background: var(--brand-strong); }
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
        <h1>Login</h1>
        <div class="muted">Masuk untuk mengelola inventory.</div>

        @if ($errors->any())
            <div class="errors">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="post" action="{{ route('login.store') }}">
            @csrf
            <div class="field">
                <label for="username">Username</label>
                <input id="username" name="username" value="{{ old('username') }}" autocomplete="username" required autofocus>
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" autocomplete="current-password" required>
            </div>
            <label class="remember">
                <input type="checkbox" name="remember" value="1">
                <span>Ingat saya</span>
            </label>
            <button type="submit">Masuk</button>
        </form>
    </main>
</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name', 'Laravel API UI'))</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap');

        :root {
            --bg: #f8fafc;
            --surface: #ffffff;
            --surface-soft: #f9fcff;
            --text: #0f172a;
            --muted: #475569;
            --primary: #0f766e;
            --primary-hover: #115e59;
            --danger: #be123c;
            --danger-hover: #9f1239;
            --border: #dbe7f0;
            --ring: #14b8a6;
            --shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
            --success-bg: #ecfdf5;
            --success-text: #065f46;
            --error-bg: #fff1f2;
            --error-text: #9f1239;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Manrope", "Segoe UI", sans-serif;
            background:
                radial-gradient(900px 420px at 0% 0%, #dbeafe 0%, transparent 70%),
                radial-gradient(800px 380px at 100% 100%, #ccfbf1 0%, transparent 65%),
                var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        .shell {
            width: min(1080px, 94%);
            margin: 26px auto 34px;
        }

        .topbar {
            position: sticky;
            top: 14px;
            z-index: 50;
            background: color-mix(in oklab, var(--surface) 86%, #e0f2fe);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 14px 16px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            box-shadow: 0 14px 30px rgba(15, 23, 42, 0.08);
            backdrop-filter: blur(8px);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            letter-spacing: 0.1px;
            font-family: "Space Grotesk", "Manrope", sans-serif;
        }

        .brand::before {
            content: "API";
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.7px;
            color: #0c4a6e;
            background: linear-gradient(90deg, #bae6fd, #99f6e4);
            border: 1px solid #a5f3fc;
            border-radius: 999px;
            padding: 4px 8px;
        }

        .nav {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
        }

        .nav a {
            text-decoration: none;
            color: var(--text);
            padding: 9px 12px;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: #ffffffb0;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.18s ease;
        }

        .nav a:hover {
            border-color: #b6d8cd;
            background: #f0fdfa;
            transform: translateY(-1px);
        }

        .panel {
            margin-top: 18px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 24px;
            box-shadow: var(--shadow);
        }

        h1 {
            margin: 0 0 16px;
            font-size: clamp(1.55rem, 2vw, 2rem);
            line-height: 1.15;
            font-family: "Space Grotesk", "Manrope", sans-serif;
            letter-spacing: -0.3px;
        }

        h2 {
            margin: 0 0 10px;
            font-size: 1.1rem;
            line-height: 1.25;
            font-family: "Space Grotesk", "Manrope", sans-serif;
        }

        p {
            margin: 0 0 12px;
            color: var(--muted);
            line-height: 1.55;
        }

        code {
            font-family: "JetBrains Mono", "Fira Code", Consolas, monospace;
            font-size: 0.9em;
            background: #e2e8f0;
            color: #0f172a;
            padding: 2px 7px;
            border-radius: 7px;
        }

        .grid {
            display: grid;
            gap: 14px;
        }

        .grid-cards {
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }

        .products-grid {
            grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
        }

        .card {
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 14px;
            background: var(--surface-soft);
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.04);
            transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            border-color: #b8d9ce;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
        }

        .card-link {
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .card img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: #f3f4f6;
        }

        .field {
            display: grid;
            gap: 6px;
            margin-bottom: 12px;
        }

        label {
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.35px;
            color: #334155;
        }

        input,
        textarea,
        button {
            font: inherit;
        }

        input,
        textarea {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 10px 12px;
            background: #fff;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        input:focus,
        textarea:focus {
            border-color: var(--ring);
            box-shadow: 0 0 0 3px color-mix(in oklab, var(--ring) 25%, transparent);
            outline: none;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        .actions {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .btn {
            border: 0;
            border-radius: 11px;
            padding: 10px 14px;
            cursor: pointer;
            color: #fff;
            background: linear-gradient(120deg, #0f766e, #0e7490);
            font-weight: 700;
            letter-spacing: 0.15px;
            transition: transform 0.15s ease, filter 0.15s ease;
            box-shadow: 0 10px 20px rgba(15, 118, 110, 0.25);
        }

        .btn:hover {
            transform: translateY(-1px);
            filter: saturate(1.06);
        }

        .btn:focus-visible {
            outline: 3px solid color-mix(in oklab, var(--ring) 45%, transparent);
            outline-offset: 2px;
        }

        .btn-secondary {
            background: #334155;
            box-shadow: 0 10px 20px rgba(51, 65, 85, 0.2);
        }

        .btn-danger {
            background: linear-gradient(120deg, var(--danger), var(--danger-hover));
            box-shadow: 0 10px 20px rgba(190, 18, 60, 0.25);
        }

        .btn[disabled] {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        a.btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #fff;
        }

        a.btn-secondary {
            color: #fff;
        }

        .alert {
            margin: 12px 0;
            padding: 10px 12px;
            border-radius: 10px;
            font-size: 14px;
            display: none;
            font-weight: 600;
        }

        .alert.show {
            display: block;
        }

        .alert.success {
            background: var(--success-bg);
            color: var(--success-text);
            border: 1px solid #a7f3d0;
        }

        .alert.error {
            background: var(--error-bg);
            color: var(--error-text);
            border: 1px solid #fecaca;
        }

        .muted {
            color: var(--muted);
            font-size: 14px;
        }

        .empty {
            border: 1px dashed var(--border);
            border-radius: 12px;
            padding: 16px;
            color: var(--muted);
            text-align: center;
            background: #f8fafc;
        }

        .toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 14px;
        }

        .spaced-bottom {
            margin-bottom: 12px;
        }

        .spaced-top {
            margin-top: 16px;
        }

        .page-subtitle {
            max-width: 78ch;
            margin-top: -4px;
            margin-bottom: 16px;
        }

        @media (max-width: 640px) {
            .shell {
                width: min(96%, 1080px);
                margin-top: 14px;
            }

            .topbar {
                top: 8px;
            }

            .panel {
                padding: 16px;
            }

            h1 {
                font-size: 22px;
            }

            .btn {
                width: 100%;
            }

            .actions a.btn {
                width: 100%;
            }

            .toolbar .btn {
                width: auto;
            }
        }
    </style>
    @stack('head')
</head>

<body>
    <div class="shell">
        <div class="topbar">
            <div class="brand">{{ config('app.name', 'Laravel') }} API UI</div>
            <div class="nav">
                <a href="{{ route('ui.login') }}">Login</a>
                <a href="{{ route('ui.register') }}">Registrati</a>
                <a href="{{ route('ui.products.index') }}">Prodotti</a>
                <a href="{{ route('ui.products.create') }}">Nuovo prodotto</a>
                <a href="{{ route('ui.orders.index') }}">Ordini</a>
                <a href="{{ route('ui.orders.create') }}">Nuovo ordine</a>
                <a href="{{ route('ui.logout') }}">Logout</a>
            </div>
        </div>

        <div class="panel">
            @yield('content')
        </div>
    </div>

    @stack('scripts')
</body>

</html>

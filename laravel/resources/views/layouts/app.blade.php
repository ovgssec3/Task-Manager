<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Personal Task Manager')</title>
    <style>
        :root {
            --bg: #f4f6f9;
            --card: #ffffff;
            --ink: #1f2430;
            --muted: #6b7280;
            --border: #e5e7eb;
            --brand: #4f46e5;
            --brand-dark: #4338ca;
            --green: #16a34a;
            --green-bg: #dcfce7;
            --amber: #b45309;
            --amber-bg: #fef3c7;
            --red: #dc2626;
            --red-bg: #fee2e2;
            --radius: 10px;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: var(--bg);
            color: var(--ink);
        }
        header.site {
            background: linear-gradient(135deg, var(--brand), var(--brand-dark));
            color: #fff;
            padding: 22px 24px;
        }
        header.site .wrap {
            max-width: 960px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }
        header.site h1 {
            margin: 0;
            font-size: 1.4rem;
        }
        header.site p {
            margin: 2px 0 0;
            font-size: 0.85rem;
            opacity: 0.85;
        }
        main {
            max-width: 960px;
            margin: 24px auto 60px;
            padding: 0 20px;
        }
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        }
        .btn {
            display: inline-block;
            padding: 9px 16px;
            border-radius: 8px;
            border: none;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: transform .05s ease, opacity .15s ease;
        }
        .btn:active { transform: scale(0.97); }
        .btn-primary { background: var(--brand); color: #fff; }
        .btn-primary:hover { background: var(--brand-dark); }
        .btn-outline { background: #fff; color: var(--ink); border: 1px solid var(--border); }
        .btn-outline:hover { background: #f3f4f6; }
        .btn-danger { background: var(--red-bg); color: var(--red); border: 1px solid #fecaca; }
        .btn-danger:hover { background: #fecaca; }
        .btn-success { background: var(--green-bg); color: var(--green); border: 1px solid #bbf7d0; }
        .btn-sm { padding: 6px 10px; font-size: 0.8rem; }
        label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 6px;
            color: var(--ink);
        }
        input[type=text], input[type=date], textarea, select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 0.95rem;
            font-family: inherit;
            background: #fff;
            color: var(--ink);
        }
        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: var(--brand);
            box-shadow: 0 0 0 3px rgba(79,70,229,0.12);
        }
        .field { margin-bottom: 16px; }
        .error { color: var(--red); font-size: 0.8rem; margin-top: 4px; }
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 18px;
            font-size: 0.9rem;
        }
        .alert-success { background: var(--green-bg); color: #166534; border: 1px solid #bbf7d0; }
        .stats {
            display: flex;
            gap: 14px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .stat {
            flex: 1;
            min-width: 130px;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 14px 16px;
        }
        .stat .num { font-size: 1.6rem; font-weight: 700; }
        .stat .label { font-size: 0.8rem; color: var(--muted); }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            text-align: left;
            padding: 12px 10px;
            border-bottom: 1px solid var(--border);
            font-size: 0.9rem;
            vertical-align: top;
        }
        th {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--muted);
        }
        tr:last-child td { border-bottom: none; }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-pending { background: var(--amber-bg); color: var(--amber); }
        .badge-completed { background: var(--green-bg); color: var(--green); }
        .badge-overdue { background: var(--red-bg); color: var(--red); }
        .actions { display: flex; gap: 6px; flex-wrap: wrap; }
        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 18px;
        }
        .filters { display: flex; gap: 8px; flex-wrap: wrap; }
        .filters a {
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 0.8rem;
            text-decoration: none;
            color: var(--muted);
            border: 1px solid var(--border);
            background: #fff;
        }
        .filters a.active { background: var(--brand); color: #fff; border-color: var(--brand); }
        .empty { text-align: center; padding: 40px 10px; color: var(--muted); }
        .desc-cell { max-width: 240px; color: var(--muted); }
        form.inline { display: inline; }
        .muted { color: var(--muted); font-size: 0.8rem; }
        @media (max-width: 640px) {
            table, thead, tbody, th, td, tr { display: block; }
            thead { display: none; }
            tr { margin-bottom: 14px; border: 1px solid var(--border); border-radius: 8px; padding: 10px; }
            td { border: none; padding: 6px 4px; }
            td::before {
                content: attr(data-label);
                display: block;
                font-size: 0.7rem;
                text-transform: uppercase;
                color: var(--muted);
                margin-bottom: 2px;
            }
        }
    </style>
</head>
<body>
    <header class="site">
        <div class="wrap">
            <div>
                <h1>✅ Personal Task Manager</h1>
                <p>Add, track, and complete your daily tasks</p>
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>
</body>
</html>
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin Dashboard' }} — IITC</title>

    <!-- Inter font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --bg-base:    #0f1117;
            --bg-sidebar: #1a1d27;
            --bg-card:    #1e2130;
            --border:     rgba(255,255,255,0.07);
            --accent:     #6366f1;
            --accent-2:   #8b5cf6;
            --text-main:  #e2e8f0;
            --text-muted: #64748b;
        }
        body { font-family: 'Inter', sans-serif; background: var(--bg-base); color: var(--text-main); }

        /* Sidebar */
        .sidebar { background: var(--bg-sidebar); border-right: 1px solid var(--border); }
        .nav-link {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 16px; border-radius: 8px;
            color: var(--text-muted); font-size: 14px; font-weight: 500;
            text-decoration: none; transition: all .2s;
        }
        .nav-link:hover   { background: rgba(99,102,241,.12); color: var(--text-main); }
        .nav-link.active  { background: rgba(99,102,241,.2);  color: #a5b4fc; }

        /* Cards */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 20px 24px;
        }

        /* Badges */
        .badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; letter-spacing: .4px; }
        .badge-pending  { background: rgba(251,191,36,.12); color: #fbbf24; border: 1px solid rgba(251,191,36,.3); }
        .badge-valid    { background: rgba(52,211,153,.12);  color: #34d399; border: 1px solid rgba(52,211,153,.3); }
        .badge-invalid  { background: rgba(248,113,113,.12); color: #f87171; border: 1px solid rgba(248,113,113,.3); }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            color: #fff; border: none; padding: 8px 18px; border-radius: 8px;
            font-size: 14px; font-weight: 600; cursor: pointer; transition: opacity .2s;
        }
        .btn-primary:hover { opacity: .85; }
        .btn-danger  { background: rgba(248,113,113,.15); color: #f87171; border: 1px solid rgba(248,113,113,.3); padding: 8px 18px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all .2s; }
        .btn-danger:hover  { background: rgba(248,113,113,.25); }
        .btn-ghost  { background: rgba(255,255,255,.06); color: var(--text-main); border: 1px solid var(--border); padding: 8px 18px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all .2s; text-decoration: none; }
        .btn-ghost:hover   { background: rgba(255,255,255,.1); }

        /* Table */
        table { width: 100%; border-collapse: collapse; }
        thead th { padding: 12px 16px; text-align: left; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: .6px; color: var(--text-muted); border-bottom: 1px solid var(--border); }
        tbody tr { border-bottom: 1px solid var(--border); transition: background .15s; }
        tbody tr:hover { background: rgba(255,255,255,.03); }
        tbody td { padding: 14px 16px; font-size: 14px; vertical-align: middle; }
        tbody tr:last-child { border-bottom: none; }

        /* Input */
        .form-input {
            background: rgba(255,255,255,.05); border: 1px solid var(--border);
            color: var(--text-main); border-radius: 8px; padding: 9px 14px;
            font-size: 14px; outline: none; transition: border-color .2s;
            width: 100%;
        }
        .form-input:focus { border-color: var(--accent); }
        .form-input::placeholder { color: var(--text-muted); }

        select.form-input option { background: var(--bg-card); }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,.1); border-radius: 4px; }

        /* Stat card gradient accents */
        .stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; }
        .stat-icon-blue   { background: linear-gradient(135deg, #6366f1, #8b5cf6); }
        .stat-icon-amber  { background: linear-gradient(135deg, #f59e0b, #f97316); }
        .stat-icon-green  { background: linear-gradient(135deg, #10b981, #059669); }
        .stat-icon-rose   { background: linear-gradient(135deg, #f43f5e, #e11d48); }

        /* Flash alert */
        .flash-success { background: rgba(52,211,153,.12); border: 1px solid rgba(52,211,153,.3); color: #34d399; padding: 12px 18px; border-radius: 10px; font-size: 14px; }
        .flash-error   { background: rgba(248,113,113,.12); border: 1px solid rgba(248,113,113,.3); color: #f87171; padding: 12px 18px; border-radius: 10px; font-size: 14px; }
    </style>
</head>
<body class="h-full">

<div class="flex h-screen overflow-hidden">

    {{-- SIDEBAR --}}
    <aside class="sidebar w-60 flex-shrink-0 flex flex-col h-full">
        {{-- Logo --}}
        <div class="p-6 border-b" style="border-color: var(--border)">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #6366f1, #8b5cf6)">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-white">IITC Admin</p>
                    <p class="text-xs" style="color: var(--text-muted)">Control Panel</p>
                </div>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            <p class="text-xs font-semibold uppercase tracking-widest mb-3 px-2" style="color: var(--text-muted)">Menu</p>

            <a href="{{ route('admin.dashboard') }}"
               class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>

            <a href="{{ route('admin.payments.index') }}"
               class="nav-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Validasi Payment
            </a>

            <p class="text-xs font-semibold uppercase tracking-widest mt-5 mb-2 px-2" style="color: var(--text-muted)">Export</p>

            <a href="{{ route('admin.export.teams') }}"
               class="nav-link">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export Tim (CSV)
            </a>

            @if(auth()->user()->hasRole('Super Admin'))

            <p class="text-xs font-semibold uppercase tracking-widest mt-5 mb-2 px-2" style="color: var(--text-muted)">Management</p>

            <a href="{{ route('admin.users.index') }}"
               class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                </svg>
                Users
            </a>

            <a href="{{ route('admin.competitions.index') }}"
               class="nav-link {{ request()->routeIs('admin.competitions.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
                Kompetisi
            </a>

            <a href="{{ route('admin.seminars.index') }}"
               class="nav-link {{ request()->routeIs('admin.seminars.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                Kelola Seminar
            </a>

            <a href="{{ route('admin.export.seminars') }}"
               class="nav-link">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Export Seminar (CSV)
            </a>

            @endif
        </nav>

        {{-- User info + logout --}}
        <div class="p-4 border-t" style="border-color: var(--border)">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold text-white"
                     style="background: linear-gradient(135deg, #6366f1, #8b5cf6)">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium truncate text-white">{{ auth()->user()->name }}</p>
                    <p class="text-xs truncate" style="color: var(--text-muted)">Admin</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left nav-link" style="background: none; border: none; cursor: pointer;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <main class="flex-1 overflow-y-auto">
        {{-- Top bar --}}
        <div class="sticky top-0 z-10 flex items-center justify-between px-8 py-4 border-b" style="background: rgba(15,17,23,.8); backdrop-filter: blur(12px); border-color: var(--border)">
            <div>
                <h1 class="text-lg font-semibold text-white">{{ $title ?? 'Dashboard' }}</h1>
                @isset($subtitle)
                    <p class="text-sm mt-0.5" style="color: var(--text-muted)">{{ $subtitle }}</p>
                @endisset
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm" style="color: var(--text-muted)">{{ now()->format('d M Y') }}</span>
            </div>
        </div>

        {{-- Page content --}}
        <div class="p-8">
            {{-- Flash messages --}}
            @if(session('success'))
                <div class="flash-success mb-6 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="flash-error mb-6 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            {{ $slot }}
        </div>
    </main>
</div>

</body>
</html>

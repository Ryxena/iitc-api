@props(['title' => 'Admin Dashboard', 'subtitle' => null])

<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — IITC Admin</title>

    <!-- Inter font for clean minimal look -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --bg-base:    #F9FAFB;
            --bg-sidebar: #FFFFFF;
            --bg-card:    #FFFFFF;
            --border:     #E5E7EB;
            --accent:     #2F2FE4;
            --accent-hover: #1e1e9e;
            --warning:    #F4A261;
            --warning-hover: #d38b4d;
            --text-main:  #111827;
            --text-muted: #6B7280;
            --shadow-sm:  0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md:  0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            --radius-md:  8px;
            --radius-lg:  12px;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-base); color: var(--text-main); }

        /* Sidebar */
        .sidebar { background: var(--bg-sidebar); border-right: 1px solid var(--border); }
        .nav-link {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 16px; border-radius: var(--radius-md);
            color: var(--text-muted); font-size: 14px; font-weight: 500;
            text-decoration: none; transition: all .2s; width: 100%;
            margin-bottom: 4px;
        }
        .nav-link:hover   { background: #F3F4F6; color: var(--text-main); }
        .nav-link.active  { background: #EEF2FF; color: var(--accent); font-weight: 600; }
        .nav-link.active svg { color: var(--accent); }

        /* Cards */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 24px;
            box-shadow: var(--shadow-sm);
        }

        /* Badges */
        .badge { display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 9999px; font-size: 12px; font-weight: 600; letter-spacing: 0.025em; }
        .badge-pending  { background: #FFF7ED; color: #C2410C; border: 1px solid #FFEDD5; }
        .badge-valid    { background: #EEF2FF; color: var(--accent); border: 1px solid #E0E7FF; }
        .badge-invalid  { background: #F3F4F6; color: var(--text-muted); border: 1px solid var(--border); }

        /* Buttons */
        .btn-primary {
            background: var(--accent); color: #fff; border: 1px solid transparent; 
            padding: 8px 16px; border-radius: var(--radius-md); font-size: 14px; font-weight: 500; 
            cursor: pointer; transition: all .2s; text-decoration: none; display: inline-flex; 
            justify-content: center; align-items: center; gap: 6px; line-height: 1; box-shadow: var(--shadow-sm);
        }
        .btn-primary:hover { background: var(--accent-hover); box-shadow: var(--shadow-md); }
        
        .btn-danger  { 
            background: #fff; color: #DC2626; border: 1px solid #FECACA; 
            padding: 8px 16px; border-radius: var(--radius-md); font-size: 14px; font-weight: 500; 
            cursor: pointer; transition: all .2s; display: inline-flex; justify-content: center; align-items: center; gap: 6px; line-height: 1; box-shadow: var(--shadow-sm);
        }
        .btn-danger:hover  { background: #FEF2F2; }
        
        .btn-ghost  { 
            background: #fff; color: var(--text-main); border: 1px solid var(--border); 
            padding: 8px 16px; border-radius: var(--radius-md); font-size: 14px; font-weight: 500; 
            cursor: pointer; transition: all .2s; text-decoration: none; display: inline-flex; 
            justify-content: center; align-items: center; gap: 6px; line-height: 1; box-shadow: var(--shadow-sm);
        }
        .btn-ghost:hover   { background: #F9FAFB; border-color: #D1D5DB; }

        .btn-action {
            display: inline-flex; align-items: center; justify-content: center;
            width: 32px; height: 32px; border-radius: var(--radius-md);
            background: #fff; border: 1px solid var(--border); color: var(--text-muted);
            transition: all 0.2s; box-shadow: var(--shadow-sm); text-decoration: none;
        }
        .btn-action:hover {
            background: var(--bg-base); color: var(--accent); border-color: var(--accent);
        }
        /* Table */
        table { width: 100%; border-collapse: collapse; }
        thead th { padding: 12px 16px; text-align: left; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); border-bottom: 1px solid var(--border); }
        tbody tr { border-bottom: 1px solid var(--border); transition: background .15s; }
        tbody tr:hover { background: #F9FAFB; }
        tbody td { padding: 16px; font-size: 14px; color: var(--text-main); vertical-align: middle; }
        tbody tr:last-child { border-bottom: none; }

        /* Inputs */
        .form-input {
            background: #fff; border: 1px solid var(--border);
            color: var(--text-main); border-radius: var(--radius-md); padding: 10px 14px;
            font-size: 14px; font-weight: 400; outline: none; transition: all .2s;
            width: 100%; font-family: 'Inter', sans-serif; box-shadow: var(--shadow-sm);
        }
        .form-input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(47, 47, 228, 0.1); }
        .form-input::placeholder { color: var(--text-muted); }

        /* Stat icons */
        .stat-icon { width: 48px; height: 48px; border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center; }
        .stat-icon-blue   { background: #EEF2FF; color: var(--accent); }
        .stat-icon-amber  { background: #FFF7ED; color: #EA580C; }
        .stat-icon-green  { background: #ECFDF5; color: #059669; }
        .stat-icon-rose   { background: #FEF2F2; color: #E11D48; }
        .stat-icon svg    { width: 24px; height: 24px; }

        /* Flash */
        .flash-success { background: #ECFDF5; border: 1px solid #A7F3D0; color: #065F46; padding: 12px 16px; border-radius: var(--radius-md); font-size: 14px; font-weight: 500; }
        .flash-error   { background: #FEF2F2; border: 1px solid #FECACA; color: #991B1B; padding: 12px 16px; border-radius: var(--radius-md); font-size: 14px; font-weight: 500; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #D1D5DB; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #9CA3AF; }

        /* Utilities */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .space-y-1 > * + * { margin-top: 4px; }
        .space-y-2 > * + * { margin-top: 8px; }
        .space-y-3 > * + * { margin-top: 12px; }
        .space-y-4 > * + * { margin-top: 16px; }
        .space-y-6 > * + * { margin-top: 24px; }
        .gap-2 { gap: 8px; }
        .gap-3 { gap: 12px; }
        .gap-4 { gap: 16px; }
        .gap-5 { gap: 20px; }
        .gap-6 { gap: 24px; }
        .grid { display: grid; }
        .flex { display: flex; }
        .inline-flex { display: inline-flex; }
        .flex-col { flex-direction: column; }
        .items-center { align-items: center; }
        .items-start { align-items: flex-start; }
        .items-end { align-items: flex-end; }
        .justify-between { justify-content: space-between; }
        .justify-center { justify-content: center; }
        .flex-1 { flex: 1; }
        .flex-shrink-0 { flex-shrink: 0; }
        .flex-wrap { flex-wrap: wrap; }
        .w-full { width: 100%; }
        .min-w-0 { min-width: 0; }
        .truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .font-medium { font-weight: 500; }
        .font-semibold { font-weight: 600; }
        .font-bold { font-weight: 700; }
        .text-white { color: #fff; }
        .text-muted { color: var(--text-muted); }
        .text-xs { font-size: 12px; }
        .text-sm { font-size: 14px; }
        .text-lg { font-size: 18px; }
        .text-xl { font-size: 20px; }
        .text-2xl { font-size: 24px; letter-spacing: -0.025em; }
        .tracking-wider { letter-spacing: .05em; }
        .rounded-lg { border-radius: var(--radius-lg); }
        .rounded-md { border-radius: var(--radius-md); }
        .overflow-hidden { overflow: hidden; }
        .overflow-y-auto { overflow-y: auto; }
        .object-contain { object-fit: contain; }
        .object-cover { object-fit: cover; }
        .cursor-pointer { cursor: pointer; }
        .cursor-zoom-in  { cursor: zoom-in; }
        .cursor-zoom-out { cursor: zoom-out; }
        .resize-none { resize: none; }
        .fixed { position: fixed; }
        .sticky { position: sticky; }
        .absolute { position: absolute; }
        .relative { position: relative; }
        .inset-0 { top:0; left:0; right:0; bottom:0; }
        .top-0 { top: 0; }
        .top-4 { top: 16px; }
        .right-4 { right: 16px; }
        .left-3 { left: 12px; }
        .z-10 { z-index: 10; }
        .z-50 { z-index: 50; }
        .mt-1 { margin-top: 4px; }
        .mt-2 { margin-top: 8px; }
        .mt-4 { margin-top: 16px; }
        .mt-6 { margin-top: 24px; }
        .mb-2 { margin-bottom: 8px; }
        .mb-4 { margin-bottom: 16px; }
        .mb-6 { margin-bottom: 24px; }
        .p-4 { padding: 16px; }
        .p-6 { padding: 24px; }
        .p-8 { padding: 32px; }
        .px-4 { padding-left: 16px; padding-right: 16px; }
        .px-6 { padding-left: 24px; padding-right: 24px; }
        .py-2 { padding-top: 8px; padding-bottom: 8px; }
        .py-4 { padding-top: 16px; padding-bottom: 16px; }
        .py-6 { padding-top: 24px; padding-bottom: 24px; }
        .w-4 { width: 16px; }
        .w-5 { width: 20px; }
        .w-8 { width: 32px; }
        .w-16 { width: 64px; }
        .h-4 { height: 16px; }
        .h-5 { height: 20px; }
        .h-8 { height: 32px; }
        .h-16 { height: 64px; }
        .h-full { height: 100%; }
        .border-t { border-top: 1px solid var(--border); }
        .transition-all { transition: all .2s; }
        .max-w-full { max-width: 100%; }
        .mx-auto { margin-left: auto; margin-right: auto; }
        .block { display: block; }
        .hidden { display: none; }
        .grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        code { font-family: 'Fira Code', 'Consolas', monospace; }

        /* Selectable Cards */
        .sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border-width: 0; }
        .selectable-card { border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 16px; text-align: center; transition: all 0.2s; background: #fff; cursor: pointer; display: block; }
        .selectable-card:hover { background: #F9FAFB; }
        .selectable-card .icon-ring { width: 24px; height: 24px; margin: 0 auto 8px; border-radius: 50%; border: 1px solid #D1D5DB; display: flex; align-items: center; justify-content: center; transition: all 0.2s; background: #fff; }
        .selectable-card .icon-check { opacity: 0; transform: scale(0.5); transition: all 0.2s; color: #fff; width: 14px; height: 14px; }
        
        input[type="radio"]#radio-approve:checked ~ .selectable-card { border-color: var(--accent); background: #EEF2FF; box-shadow: 0 0 0 1px var(--accent); }
        input[type="radio"]#radio-approve:checked ~ .selectable-card .icon-ring { background: var(--accent); border-color: var(--accent); }
        input[type="radio"]#radio-approve:checked ~ .selectable-card .icon-check { opacity: 1; transform: scale(1); }
        
        input[type="radio"]#radio-reject:checked ~ .selectable-card { border-color: #DC2626; background: #FEF2F2; box-shadow: 0 0 0 1px #DC2626; }
        input[type="radio"]#radio-reject:checked ~ .selectable-card .icon-ring { background: #DC2626; border-color: #DC2626; }
        input[type="radio"]#radio-reject:checked ~ .selectable-card .icon-check { opacity: 1; transform: scale(1); }
    </style>
</head>
<body style="height: 100%; overflow: hidden;">

<div style="display: flex; height: 100vh; overflow: hidden;">

    {{-- SIDEBAR --}}
    <aside class="sidebar" style="width: 260px; flex-shrink: 0; display: flex; flex-direction: column; height: 100%;">
        {{-- Logo --}}
        <div style="padding: 24px; border-bottom: 1px solid var(--border);">
            <div style="display: flex; align-items: center; gap: 12px">
                <div style="width: 40px; height: 40px; border-radius: var(--radius-md); background: var(--accent); display: flex; align-items: center; justify-content: center; box-shadow: var(--shadow-sm);">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px;color:#fff" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                    </svg>
                </div>
                <div>
                    <p style="font-size:18px; font-weight:700; color:var(--text-main); line-height:1; letter-spacing: -0.025em;">IITC Admin</p>
                    <p style="font-size:12px; font-weight:500; color:var(--text-muted); margin-top: 4px;">Control Panel</p>
                </div>
            </div>
        </div>

        {{-- Nav --}}
        <nav style="flex: 1; padding: 24px 16px; overflow-y: auto;">
            <p style="font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 16px; padding: 0 8px;">Menu Utama</p>

            <a href="{{ route('admin.dashboard') }}"
               class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>

            <a href="{{ route('admin.payments.index') }}"
               class="nav-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Validasi Payment
            </a>

            <a href="{{ route('admin.teams.recap') }}"
               class="nav-link {{ request()->routeIs('admin.teams.recap*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Recap Team
            </a>

            <a href="{{ route('admin.participants.recap') }}"
               class="nav-link {{ request()->routeIs('admin.participants.recap*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Rekap Individu
            </a>

            <a href="{{ route('admin.export.teams') }}"
               class="nav-link">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export CSV
            </a>

            @if(auth()->user()->hasRole('Super Admin'))

            <p style="font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-top: 24px; margin-bottom: 12px; padding: 0 8px;">Management</p>

            <a href="{{ route('admin.winners.index') }}"
               class="nav-link {{ request()->routeIs('admin.winners.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                </svg>
                Daftar Juara
            </a>

            <a href="{{ route('admin.users.index') }}"
               class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                </svg>
                Users
            </a>

            <a href="{{ route('admin.teams-management.index') }}"
               class="nav-link {{ request()->routeIs('admin.teams-management.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                Kelola Tim
            </a>

            <a href="{{ route('admin.competitions.index') }}"
               class="nav-link {{ request()->routeIs('admin.competitions.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
                Kompetisi
            </a>

            <a href="{{ route('admin.seminars.index') }}"
               class="nav-link {{ request()->routeIs('admin.seminars.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                Kelola Seminar
            </a>

            <!-- <a href="{{ route('admin.export.seminars') }}"
               class="nav-link">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Export Seminar (CSV)
            </a> -->

            <a href="{{ route('admin.media-partners.index') }}"
               class="nav-link {{ request()->routeIs('admin.media-partners.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 12h6m-6-4h3"/>
                </svg>
                Media Partner
            </a>

            <a href="{{ route('admin.sponsors.index') }}"
               class="nav-link {{ request()->routeIs('admin.sponsors.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Sponsor
            </a>

            @endif
        </nav>

        {{-- User + Logout --}}
        <div style="padding: 24px; border-top: 1px solid var(--border); background: #fff;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                <div style="width: 36px; height: 36px; border-radius: 50%; background: #EEF2FF; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600; color: var(--accent); flex-shrink: 0;">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <div style="flex: 1; min-width: 0;">
                    <p style="font-size: 14px; font-weight: 500; color: var(--text-main); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ auth()->user()->name ?? 'Administrator' }}</p>
                    <p style="font-size: 12px; font-weight: 400; color: var(--text-muted);">Admin</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link" style="width: 100%; background: none; border: none; cursor: pointer; text-align: left; margin: 0; padding: 10px 16px;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- MAIN --}}
    <main style="flex: 1; overflow-y: auto; display: flex; flex-direction: column;">
        {{-- Topbar --}}
        <div style="position: sticky; top: 0; z-index: 10; display: flex; align-items: center; justify-content: space-between; padding: 24px 40px; background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(12px); border-bottom: 1px solid var(--border);">
            <div>
                <h1 style="font-size: 24px; font-weight: 600; color: var(--text-main); letter-spacing: -0.025em;">{{ $title }}</h1>
                @if($subtitle)
                    <p style="font-size: 14px; font-weight: 400; color: var(--text-muted); margin-top: 4px;">{{ $subtitle }}</p>
                @endif
            </div>
            <div style="font-size: 13px; font-weight: 500; color: var(--text-muted); display: flex; align-items: center; gap: 8px;">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ now()->format('d M Y') }}
            </div>
        </div>

        {{-- Content --}}
        <div style="padding: 40px; flex: 1;">
            {{-- Flash --}}
            @if(session('success'))
                <div class="flash-success mb-6 flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px;flex-shrink:0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="flash-error mb-6 flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px;flex-shrink:0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    {{ session('error') }}
                </div>
            @endif

            {{ $slot }}
        </div>
    </main>
</div>

</body>
</html>

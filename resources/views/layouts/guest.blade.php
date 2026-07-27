<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Inter font for clean minimal look -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --bg-base:    #F9FAFB;
            --bg-card:    #FFFFFF;
            --border:     #E5E7EB;
            --accent:     #2F2FE4;
            --accent-hover: #1e1e9e;
            --warning:    #F4A261;
            --text-main:  #111827;
            --text-muted: #6B7280;
            --shadow-sm:  0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md:  0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            --radius-md:  8px;
            --radius-lg:  16px;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-base); color: var(--text-main); }
        
        .brutal-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 40px;
            box-shadow: var(--shadow-md);
        }

        .brutal-input {
            background: #fff; border: 1px solid var(--border);
            color: var(--text-main); border-radius: var(--radius-md); padding: 12px 16px;
            font-size: 14px; font-weight: 400; outline: none; transition: all .2s;
            width: 100%; font-family: 'Inter', sans-serif; box-shadow: var(--shadow-sm);
        }
        .brutal-input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(47, 47, 228, 0.1); }
        .brutal-input::placeholder { color: var(--text-muted); }

        .brutal-btn {
            background: var(--accent); color: #fff; border: 1px solid transparent; 
            padding: 12px 16px; border-radius: var(--radius-md); font-size: 14px; font-weight: 500; 
            cursor: pointer; transition: all .2s; text-decoration: none; display: flex; 
            justify-content: center; align-items: center; box-shadow: var(--shadow-sm); width: 100%;
        }
        .brutal-btn:hover { background: var(--accent-hover); box-shadow: var(--shadow-md); }

        .brutal-logo {
            width: 56px; height: 56px; border-radius: var(--radius-md); background: var(--accent); 
            display: flex; align-items: center; justify-content: center; box-shadow: var(--shadow-sm);
            margin: 0 auto;
        }

        .brutal-link {
            color: var(--text-muted); font-size: 14px; font-weight: 500; text-decoration: none; transition: all 0.2s;
        }
        .brutal-link:hover { color: var(--accent); }
    </style>
</head>
<body class="antialiased" style="height: 100%; display: flex; flex-direction: column;">
    
    <div style="flex: 1; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 24px; background: var(--bg-base);">
        
        <div style="margin-bottom: 32px; text-align: center;">
            <a href="/" style="display: block; text-decoration: none;">
                <div class="brutal-logo">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:28px;height:28px;color:#fff" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                    </svg>
                </div>
                <h1 style="margin-top: 20px; font-size: 24px; font-weight: 700; letter-spacing: -0.025em; color: var(--text-main);">IITC Admin</h1>
            </a>
        </div>

        <div class="brutal-card" style="width: 100%; max-width: 420px;">
            {{ $slot }}
        </div>

    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Distribution of Electric Power for Agriculture | Ministry of Power</title>
    <!-- Vite for Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Libre+Baskerville:wght@400;700&display=swap');

        :root {
            --paper: #f8f4e9;
            --paper-deep: #eee5d3;
            --ink: #1e241d;
            --muted: #667060;
            --leaf: #234817;
            --leaf-soft: #4f6f31;
            --wheat: #d8bd78;
            --copper: #9a5933;
            --clay: #b9764e;
            --line: rgba(47, 59, 39, 0.16);
            --glass: rgba(255, 253, 246, 0.76);
            --shadow: 0 24px 70px rgba(56, 48, 33, 0.14);
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--muted);
            background:
                radial-gradient(circle at 16% 0%, rgba(216, 189, 120, 0.25), transparent 29rem),
                linear-gradient(135deg, rgba(248, 244, 233, 0.96), rgba(238, 229, 211, 0.88));
            background-color: var(--paper);
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            opacity: 0.36;
            background-image:
                linear-gradient(rgba(35, 72, 23, 0.035) 1px, transparent 1px),
                linear-gradient(90deg, rgba(35, 72, 23, 0.035) 1px, transparent 1px);
            background-size: 46px 46px;
            mask-image: linear-gradient(to bottom, black, transparent 82%);
        }

        .utilitarian-card {
            position: relative;
            overflow: hidden;
            background: var(--glass);
            border: 1px solid rgba(255, 255, 255, 0.72);
            border-radius: 1.5rem;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.78), 0 18px 45px rgba(54, 45, 29, 0.12);
            backdrop-filter: blur(18px) saturate(1.08);
            -webkit-backdrop-filter: blur(18px) saturate(1.08);
            transition: transform 180ms ease, box-shadow 180ms ease, border-color 180ms ease;
        }

        .utilitarian-card:hover {
            transform: translateY(-3px);
            border-color: rgba(35, 72, 23, 0.24);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.82), 0 24px 58px rgba(54, 45, 29, 0.16);
        }

        .utilitarian-card::after {
            content: "";
            position: absolute;
            width: 96px;
            height: 96px;
            right: -34px;
            top: -34px;
            border-radius: 999px;
            background: rgba(216, 189, 120, 0.18);
            pointer-events: none;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.625rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            font-size: 0.875rem;
            color: #5f685b;
            transition: all 0.18s ease;
        }

        .sidebar-link:hover {
            background-color: rgba(35, 72, 23, 0.08);
            color: var(--leaf);
            transform: translateX(2px);
        }

        .sidebar-link.active {
            background-color: rgba(35, 72, 23, 0.11);
            color: var(--leaf);
            border: 1px solid rgba(35, 72, 23, 0.16);
        }

        aside {
            background: rgba(255, 253, 246, 0.64);
            box-shadow: 18px 0 55px rgba(64, 54, 35, 0.08);
            backdrop-filter: blur(22px);
            -webkit-backdrop-filter: blur(22px);
        }

        header {
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        main {
            position: relative;
            z-index: 1;
        }

        h1, h2 {
            font-family: "Libre Baskerville", Georgia, serif;
            letter-spacing: 0;
        }

        input,
        select,
        textarea {
            background: rgba(255, 253, 246, 0.88) !important;
            color: var(--ink) !important;
            border-color: rgba(35, 72, 23, 0.18) !important;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.78);
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: var(--leaf) !important;
            box-shadow: 0 0 0 3px rgba(35, 72, 23, 0.12);
        }

        [class*="bg-[#121C16]"] {
            background: rgba(255, 253, 246, 0.86) !important;
        }

        [class*="bg-[#1F2F24]"] {
            background: rgba(35, 72, 23, 0.18) !important;
        }

        [class*="bg-[#0A110D]/80"] {
            background: rgba(30, 36, 29, 0.48) !important;
        }

        [class*="bg-[#0A110D]/50"] {
            background: rgba(238, 229, 211, 0.62) !important;
        }

        table thead tr {
            background: rgba(229, 218, 174, 0.24);
        }

        table tbody tr:hover {
            background: rgba(35, 72, 23, 0.06) !important;
        }

        /* Custom pagination styling */
        .pagination {
            display: flex;
            gap: 0.25rem;
            list-style: none;
            padding: 0;
        }

        .pagination .page-item .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2rem;
            height: 2rem;
            padding: 0 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--muted);
            background: rgba(255, 253, 246, 0.72);
            border: 1px solid rgba(35, 72, 23, 0.14);
            transition: all 0.15s;
        }

        .pagination .page-item .page-link:hover {
            background: rgba(35, 72, 23, 0.10);
            color: var(--leaf);
        }

        .pagination .page-item.active .page-link {
            background: var(--leaf);
            color: white;
            border-color: var(--leaf);
        }

        .pagination .page-item.disabled .page-link {
            opacity: 0.4;
            pointer-events: none;
        }

        @media (max-width: 900px) {
            body {
                flex-direction: column;
            }

            aside {
                width: 100% !important;
                height: auto !important;
                position: relative !important;
                border-right: 0 !important;
                border-bottom: 1px solid var(--line);
                padding-top: 1rem !important;
            }

            aside .overflow-y-auto {
                overflow-x: auto;
            }

            header {
                position: relative !important;
                align-items: flex-start;
                flex-direction: column;
                padding: 1rem !important;
            }

            header > div:first-child {
                max-width: 100%;
                overflow-x: auto;
                padding-bottom: 0.25rem;
            }

            main {
                padding: 1rem !important;
            }
        }
    </style>
</head>

<body class="text-theme-text antialiased min-h-screen flex">

    @auth
        <!-- Fixed Sidebar -->
        <aside class="w-64 flex-shrink-0 border-r border-theme-border h-screen sticky top-0 flex flex-col pt-6">
            <div class="px-6 mb-8 flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-theme-panel border border-theme-border flex items-center justify-center text-theme-accent font-bold text-sm shadow-sm">
                    <i class="fa-solid fa-landmark"></i>
                </div>
                <div>
                    <h1 class="text-[11px] font-bold text-theme-heading leading-tight tracking-wider uppercase">
                        Ministry of Power
                        <span class="block text-theme-text font-medium">Agriculture</span>
                    </h1>
                </div>
            </div>

            <div class="px-6 flex-grow overflow-y-auto">
                @if(Auth::user()->role === 'farmer')
                    <p class="text-[10px] font-bold text-theme-text tracking-widest uppercase mb-4">Farmer Portal</p>
                    <ul class="space-y-1 mb-8">
                        <li>
                            <a href="{{ route('farmer.dashboard') }}"
                                class="sidebar-link {{ request()->routeIs('farmer.dashboard') ? 'active' : '' }}">
                                <i class="fa-solid fa-border-all w-4"></i> Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('farmer.connections') }}"
                                class="sidebar-link {{ request()->routeIs('farmer.connections') ? 'active' : '' }}">
                                <i class="fa-solid fa-plug w-4"></i> Connections
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('farmer.bills') }}"
                                class="sidebar-link {{ request()->routeIs('farmer.bills') ? 'active' : '' }} justify-between">
                                <span class="flex items-center gap-3"><i class="fa-solid fa-file-invoice w-4"></i> Bills &
                                    Payments</span>
                                @if(isset($pendingBillsCount) && $pendingBillsCount > 0)
                                    <span class="w-5 h-5 rounded-full bg-red-500/20 text-red-500 text-[10px] flex items-center justify-center font-bold">{{ $pendingBillsCount }}</span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('farmer.usage') }}"
                                class="sidebar-link {{ request()->routeIs('farmer.usage') ? 'active' : '' }}">
                                <i class="fa-solid fa-chart-line w-4"></i> Usage
                            </a>
                        </li>
                    </ul>

                    <p class="text-[10px] font-bold text-theme-text tracking-widest uppercase mb-4">Support</p>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('farmer.complaints') }}"
                                class="sidebar-link {{ request()->routeIs('farmer.complaints') ? 'active' : '' }}">
                                <i class="fa-regular fa-comment-dots w-4"></i> Complaints
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('farmer.subsidies') }}"
                                class="sidebar-link {{ request()->routeIs('farmer.subsidies') ? 'active' : '' }}">
                                <i class="fa-solid fa-percent w-4"></i> Subsidies
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('farmer.profile') }}"
                                class="sidebar-link {{ request()->routeIs('farmer.profile') ? 'active' : '' }}">
                                <i class="fa-solid fa-user w-4"></i> Profile
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('farmer.help') }}"
                                class="sidebar-link {{ request()->routeIs('farmer.help') ? 'active' : '' }}">
                                <i class="fa-regular fa-circle-question w-4"></i> Help
                            </a>
                        </li>
                    </ul>
                @elseif(Auth::user()->role === 'sdo')
                    <p class="text-[10px] font-bold text-theme-text tracking-widest uppercase mb-4">SDO Portal</p>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('officer.dashboard') }}"
                                class="sidebar-link {{ request()->routeIs('officer.dashboard') ? 'active' : '' }}">
                                <i class="fa-solid fa-border-all w-4"></i> Dashboard
                            </a>
                        </li>
                    </ul>
                @elseif(Auth::user()->role === 'admin')
                    <p class="text-[10px] font-bold text-theme-text tracking-widest uppercase mb-4">Admin Portal</p>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('admin.dashboard') }}"
                                class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                <i class="fa-solid fa-border-all w-4"></i> Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.users') }}"
                                class="sidebar-link {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                                <i class="fa-solid fa-users w-4"></i> Users
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.tariffs') }}"
                                class="sidebar-link {{ request()->routeIs('admin.tariffs') ? 'active' : '' }}">
                                <i class="fa-solid fa-money-bill w-4"></i> Tariffs
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.subsidies') }}"
                                class="sidebar-link {{ request()->routeIs('admin.subsidies') ? 'active' : '' }}">
                                <i class="fa-solid fa-percent w-4"></i> Subsidies
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.zones') }}"
                                class="sidebar-link {{ request()->routeIs('admin.zones') ? 'active' : '' }}">
                                <i class="fa-solid fa-map-location-dot w-4"></i> Zones
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.audit_logs') }}"
                                class="sidebar-link {{ request()->routeIs('admin.audit_logs') ? 'active' : '' }}">
                                <i class="fa-solid fa-clock-rotate-left w-4"></i> Audit Logs
                            </a>
                        </li>
                    </ul>
                @endif
            </div>

            <div class="p-6 border-t border-theme-border">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-red-500/10 text-theme-text hover:text-red-400 font-medium text-sm transition-colors">
                        <i class="fa-solid fa-arrow-right-from-bracket w-4"></i> Logout
                    </button>
                </form>
            </div>
        </aside>
    @endauth

    <!-- Main Content Area -->
    <div class="flex-grow flex flex-col min-h-screen">
        @auth
            <!-- Top Bar -->
            <header
                class="min-h-16 border-b border-theme-border flex items-center justify-between gap-4 px-8 bg-theme-bg/80 sticky top-0 z-40">
                <div class="flex items-center gap-2">
                    @if(Auth::user()->role === 'farmer')
                        @php
                            $currentRoute = request()->route()->getName();
                            $tabs = [
                                'farmer.dashboard' => 'Overview',
                                'farmer.connections' => 'Connections',
                                'farmer.bills' => 'Billing',
                                'farmer.subsidies' => 'Subsidies',
                                'farmer.usage' => 'Usage',
                            ];
                        @endphp
                        @foreach($tabs as $route => $label)
                            <a href="{{ route($route) }}"
                                class="px-4 py-1.5 rounded-lg text-sm font-medium transition-colors {{ $currentRoute === $route ? 'bg-theme-panel border border-theme-border text-theme-heading' : 'hover:bg-theme-panel border border-transparent hover:border-theme-border text-theme-text' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    @else
                        <span class="text-sm font-medium text-theme-heading">
                            {{ ucfirst(Auth::user()->role) }} Portal
                        </span>
                    @endif
                </div>

                <div class="flex items-center gap-3 pl-4 border-l border-theme-border">
                    @if(Auth::user()->avatar)
                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="w-8 h-8 rounded-full object-cover border border-emerald-500/30">
                    @else
                        <div class="w-8 h-8 rounded-full bg-emerald-500/20 border border-emerald-500/30 text-emerald-500 flex items-center justify-center text-xs font-bold uppercase">
                            {{ substr(Auth::user()->name, 0, 2) }}
                        </div>
                    @endif
                    <div class="text-sm">
                        <p class="font-medium text-theme-heading leading-tight">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-theme-text uppercase tracking-wider">{{ Auth::user()->role }}
                            @if(Auth::user()->farmer_id_number)
                                · {{ Auth::user()->farmer_id_number }}
                            @endif
                        </p>
                    </div>
                </div>
            </header>
        @endauth

        <main class="flex-grow p-8 max-w-6xl w-full mx-auto">
            @if(session('success'))
                <div
                    class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-4 py-3 rounded-xl mb-6 flex items-center gap-3 text-sm">
                    <i class="fa-solid fa-circle-check"></i>
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div
                    class="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-xl mb-6 flex items-center gap-3 text-sm">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <ul class="list-disc list-inside">
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

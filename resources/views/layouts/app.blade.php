<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Distribution of Electric Power for Agriculture | Ministry of Power</title>
    <!-- Vite for Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background-color: #0A110D;
        }

        .utilitarian-card {
            background-color: #121C16;
            border: 1px solid #1F2F24;
            border-radius: 0.75rem;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.625rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            font-size: 0.875rem;
            color: #9AA8A0;
            transition: all 0.15s ease;
        }

        .sidebar-link:hover {
            background-color: #121C16;
            color: #E5EDE8;
        }

        .sidebar-link.active {
            background-color: #1F2F24;
            color: #E5EDE8;
            border: 1px solid #1F2F24;
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
            color: #9AA8A0;
            background: #121C16;
            border: 1px solid #1F2F24;
            transition: all 0.15s;
        }

        .pagination .page-item .page-link:hover {
            background: #1F2F24;
            color: #E5EDE8;
        }

        .pagination .page-item.active .page-link {
            background: #15803d;
            color: white;
            border-color: #15803d;
        }

        .pagination .page-item.disabled .page-link {
            opacity: 0.4;
            pointer-events: none;
        }
    </style>
</head>

<body class="text-theme-text antialiased min-h-screen flex">

    @auth
        <!-- Fixed Sidebar -->
        <aside class="w-64 flex-shrink-0 border-r border-theme-border h-screen sticky top-0 flex flex-col pt-6">
            <div class="px-6 mb-8 flex items-center gap-3">
                <div class="w-8 h-8 rounded bg-theme-accent flex items-center justify-center text-white font-bold text-sm">
                    <i class="fa-solid fa-bolt"></i>
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
                                @php
                                    $pendingBills = \App\Models\Bill::whereIn('connection_id',
                                        \App\Models\Connection::where('consumer_id', Auth::id())->pluck('id')
                                    )->where('status', 'pending')->count();
                                @endphp
                                @if($pendingBills > 0)
                                    <span class="w-5 h-5 rounded-full bg-red-500/20 text-red-500 text-[10px] flex items-center justify-center font-bold">{{ $pendingBills }}</span>
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
                class="h-16 border-b border-theme-border flex items-center justify-between px-8 bg-theme-bg/95 sticky top-0 z-40">
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
                    <div
                        class="w-8 h-8 rounded-full bg-emerald-500/20 border border-emerald-500/30 text-emerald-500 flex items-center justify-center text-xs font-bold uppercase">
                        {{ substr(Auth::user()->name, 0, 2) }}
                    </div>
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
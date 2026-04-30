<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Distribution of Electric Power for Agriculture | Ministry of Power</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        theme: {
                            bg: '#0A110D',       // Darkest background
                            panel: '#121C16',    // Card background
                            border: '#1F2F24',   // Borders
                            text: '#9AA8A0',     // Muted text
                            heading: '#E5EDE8',  // Bright text
                            accent: '#15803d',   // Primary accent (solid green)
                            hover: '#166534',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #0A110D; }
        .utilitarian-card {
            background-color: #121C16;
            border: 1px solid #1F2F24;
            border-radius: 0.75rem;
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

            <div class="px-6 flex-grow">
                <p class="text-[10px] font-bold text-theme-text tracking-widest uppercase mb-4">Farmer</p>
                <ul class="space-y-1 mb-8">
                    <li>
                        <a href="{{ route('farmer.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg bg-theme-border text-theme-heading font-medium text-sm border border-theme-border">
                            <i class="fa-solid fa-border-all w-4"></i> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-theme-panel text-theme-text hover:text-theme-heading font-medium text-sm transition-colors">
                            <i class="fa-solid fa-plug w-4"></i> Connections
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center justify-between px-4 py-2.5 rounded-lg hover:bg-theme-panel text-theme-text hover:text-theme-heading font-medium text-sm transition-colors">
                            <span class="flex items-center gap-3"><i class="fa-solid fa-file-invoice w-4"></i> Bills & Payments</span>
                            <span class="w-5 h-5 rounded-full bg-red-500/20 text-red-500 text-[10px] flex items-center justify-center font-bold">2</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-theme-panel text-theme-text hover:text-theme-heading font-medium text-sm transition-colors">
                            <i class="fa-solid fa-chart-line w-4"></i> Usage
                        </a>
                    </li>
                </ul>

                <p class="text-[10px] font-bold text-theme-text tracking-widest uppercase mb-4">Support</p>
                <ul class="space-y-1">
                    <li>
                        <a href="#" class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-theme-panel text-theme-text hover:text-theme-heading font-medium text-sm transition-colors">
                            <i class="fa-regular fa-comment-dots w-4"></i> Complaints
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-theme-panel text-theme-text hover:text-theme-heading font-medium text-sm transition-colors">
                            <i class="fa-regular fa-circle-question w-4"></i> Help
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="p-6 border-t border-theme-border">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-red-500/10 text-theme-text hover:text-red-400 font-medium text-sm transition-colors">
                        <i class="fa-solid fa-arrow-right-from-bracket w-4"></i> Logout
                    </button>
                </form>
            </div>
        </aside>
    @endauth

    <!-- Main Content Area -->
    <div class="flex-grow flex flex-col min-h-screen">
        @auth
            <!-- Top Tabs Bar -->
            <header class="h-16 border-b border-theme-border flex items-center justify-between px-8 bg-theme-bg/95 sticky top-0 z-40">
                <div class="flex items-center gap-2">
                    <button class="px-4 py-1.5 rounded-lg bg-theme-panel border border-theme-border text-theme-heading text-sm font-medium">Overview</button>
                    <button class="px-4 py-1.5 rounded-lg hover:bg-theme-panel border border-transparent hover:border-theme-border text-theme-text text-sm font-medium transition-colors">Connections</button>
                    <button class="px-4 py-1.5 rounded-lg hover:bg-theme-panel border border-transparent hover:border-theme-border text-theme-text text-sm font-medium transition-colors">Billing</button>
                    <button class="px-4 py-1.5 rounded-lg hover:bg-theme-panel border border-transparent hover:border-theme-border text-theme-text text-sm font-medium transition-colors">Subsidies</button>
                    <button class="px-4 py-1.5 rounded-lg hover:bg-theme-panel border border-transparent hover:border-theme-border text-theme-text text-sm font-medium transition-colors">Reports</button>
                </div>
                
                <div class="flex items-center gap-3 pl-4 border-l border-theme-border">
                    <div class="w-8 h-8 rounded-full bg-emerald-500/20 border border-emerald-500/30 text-emerald-500 flex items-center justify-center text-xs font-bold uppercase">
                        {{ substr(Auth::user()->name, 0, 2) }}
                    </div>
                    <div class="text-sm">
                        <p class="font-medium text-theme-heading leading-tight">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-theme-text uppercase tracking-wider">{{ Auth::user()->role }}</p>
                    </div>
                </div>
            </header>
        @endauth

        <main class="flex-grow p-8 max-w-6xl w-full mx-auto">
            @if(session('success'))
                <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-4 py-3 rounded-xl mb-6 flex items-center gap-3 text-sm">
                    <i class="fa-solid fa-circle-check"></i>
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-xl mb-6 flex items-center gap-3 text-sm">
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

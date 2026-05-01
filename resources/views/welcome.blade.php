<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Distribution of Electric Power for Agriculture | Ministry of Power</title>
    <meta name="description" content="A digital platform by the Ministry of Power for managing agricultural electricity distribution — connections, billing, subsidies, and grievance redressal.">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        theme: { bg: '#0A110D', panel: '#121C16', border: '#1F2F24', text: '#9AA8A0', heading: '#E5EDE8', accent: '#15803d', hover: '#166534' }
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #0A110D; }
    </style>
</head>
<body class="text-theme-text antialiased min-h-screen">

    {{-- Header --}}
    <header class="border-b border-theme-border">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded bg-theme-accent flex items-center justify-center text-white font-bold text-sm">
                    <i class="fa-solid fa-bolt"></i>
                </div>
                <div>
                    <h1 class="text-xs font-bold text-theme-heading tracking-wider uppercase leading-tight">Ministry of Power
                        <span class="block text-theme-text font-medium normal-case tracking-normal">Agriculture Division</span>
                    </h1>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}" class="text-sm font-medium text-theme-text hover:text-theme-heading transition-colors px-4 py-2">Sign In</a>
                <a href="{{ route('register') }}" class="bg-theme-accent hover:bg-theme-hover text-white text-sm font-bold px-5 py-2 rounded-lg transition-colors">Register</a>
            </div>
        </div>
    </header>

    {{-- Hero Section --}}
    <section class="max-w-6xl mx-auto px-6 py-20">
        <div class="text-center max-w-3xl mx-auto">
            <div class="inline-flex items-center gap-2 bg-theme-panel border border-theme-border rounded-full px-4 py-1.5 mb-6">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs text-theme-text font-medium">Government of India Initiative</span>
            </div>

            <h1 class="text-4xl md:text-5xl font-extrabold text-theme-heading mb-6 leading-tight tracking-tight">
                Distribution of Electric Power<br>
                <span class="text-theme-accent">for Agriculture</span>
            </h1>

            <p class="text-theme-text text-lg md:text-xl max-w-2xl mx-auto mb-10 leading-relaxed">
                A streamlined platform for farmers to manage power connections, view subsidies, track usage, and resolve grievances — all in one place.
            </p>

            <div class="flex gap-4 justify-center">
                <a href="{{ route('register') }}"
                    class="bg-theme-accent hover:bg-theme-hover text-white font-bold py-3 px-8 rounded-xl transition-all flex items-center gap-2 text-sm">
                    Get Started <i class="fa-solid fa-arrow-right"></i>
                </a>
                <a href="{{ route('login') }}"
                    class="bg-theme-panel border border-theme-border hover:border-theme-accent/30 text-theme-heading font-semibold py-3 px-8 rounded-xl transition-all text-sm">
                    Sign In
                </a>
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section class="max-w-6xl mx-auto px-6 pb-20">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-theme-panel border border-theme-border rounded-xl p-6">
                <div class="w-10 h-10 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center mb-4">
                    <i class="fa-solid fa-plug text-lg"></i>
                </div>
                <h3 class="text-sm font-bold text-theme-heading mb-2">Connection Management</h3>
                <p class="text-xs text-theme-text leading-relaxed">Apply for new connections, track approval status, and manage all your agricultural power connections.</p>
            </div>
            <div class="bg-theme-panel border border-theme-border rounded-xl p-6">
                <div class="w-10 h-10 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center mb-4">
                    <i class="fa-solid fa-file-invoice text-lg"></i>
                </div>
                <h3 class="text-sm font-bold text-theme-heading mb-2">Billing & Payments</h3>
                <p class="text-xs text-theme-text leading-relaxed">View itemized bills, pay online instantly, and download payment receipts for your records.</p>
            </div>
            <div class="bg-theme-panel border border-theme-border rounded-xl p-6">
                <div class="w-10 h-10 rounded-lg bg-indigo-500/20 text-indigo-400 flex items-center justify-center mb-4">
                    <i class="fa-solid fa-percent text-lg"></i>
                </div>
                <h3 class="text-sm font-bold text-theme-heading mb-2">Subsidy Schemes</h3>
                <p class="text-xs text-theme-text leading-relaxed">Browse and apply for government subsidy schemes like PM-KUSUM and state agricultural power waivers.</p>
            </div>
            <div class="bg-theme-panel border border-theme-border rounded-xl p-6">
                <div class="w-10 h-10 rounded-lg bg-rose-500/20 text-rose-400 flex items-center justify-center mb-4">
                    <i class="fa-regular fa-comment-dots text-lg"></i>
                </div>
                <h3 class="text-sm font-bold text-theme-heading mb-2">Grievance Redressal</h3>
                <p class="text-xs text-theme-text leading-relaxed">File complaints for power issues, track resolution status, and get timely responses from your local office.</p>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="border-t border-theme-border py-6">
        <div class="max-w-6xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-xs text-theme-text">© {{ date('Y') }} Ministry of Power, Government of India. All rights reserved.</p>
            <p class="text-xs text-theme-text">Technology Bucket · Distribution of Electric Power for Agriculture</p>
        </div>
    </footer>

</body>
</html>
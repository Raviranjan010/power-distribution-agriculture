<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Distribution of Electric Power for Agriculture</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        theme: { bg: '#f8f4e9', panel: '#fffdf6', border: '#d8ccad', text: '#667060', heading: '#1e241d', accent: '#234817', hover: '#17310f' }
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; color: #667060; background: radial-gradient(circle at 16% 0%, rgba(216, 189, 120, 0.25), transparent 28rem), linear-gradient(135deg, #f8f4e9, #eee5d3); } input, select, textarea { background: rgba(255,253,246,0.9) !important; color: #1e241d !important; border-color: rgba(35,72,23,0.18) !important; } input:focus, select:focus, textarea:focus { border-color: #234817 !important; box-shadow: 0 0 0 3px rgba(35,72,23,0.12); } .bg-theme-panel { background: rgba(255,253,246,0.78) !important; box-shadow: inset 0 1px 0 rgba(255,255,255,0.78), 0 24px 70px rgba(56,48,33,0.14); backdrop-filter: blur(18px); } .rounded-xl { border-radius: 1.5rem; } .bg-theme-panel { transition: transform 180ms ease, box-shadow 180ms ease; } .bg-theme-panel:hover { transform: translateY(-3px); box-shadow: inset 0 1px 0 rgba(255,255,255,0.82), 0 30px 82px rgba(56,48,33,0.18) !important; } button, a { transition: transform 180ms ease, background-color 180ms ease, color 180ms ease; } button:hover { transform: translateY(-2px); }
    </style>
</head>
<body class="text-theme-text antialiased min-h-screen flex items-center justify-center">

<div class="w-full max-w-md px-6">
    <div class="text-center mb-8">
        <div class="w-12 h-12 rounded-xl bg-theme-accent/20 border border-theme-accent/30 flex items-center justify-center text-theme-accent text-xl mx-auto mb-4">
            <i class="fa-solid fa-landmark"></i>
        </div>
        <h1 class="text-xl font-bold text-theme-heading">Ministry of Power</h1>
        <p class="text-xs text-theme-text">Agriculture Power Distribution</p>
    </div>

    <div class="bg-theme-panel border border-theme-border rounded-xl p-8">
        <h2 class="text-lg font-bold text-theme-heading mb-6">Sign in to your account</h2>

        @if($errors->any())
            <div class="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-lg mb-4 text-sm">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-theme-text tracking-widest uppercase mb-2">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-4 py-3 focus:outline-none focus:border-theme-accent text-white placeholder-theme-text/50 text-sm" placeholder="name@example.com" required>
            </div>

            <div>
                <label class="block text-xs font-bold text-theme-text tracking-widest uppercase mb-2">Password</label>
                <input type="password" name="password" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-4 py-3 focus:outline-none focus:border-theme-accent text-white placeholder-theme-text/50 text-sm" placeholder="••••••••" required>
            </div>

            <button type="submit" class="w-full bg-theme-accent hover:bg-theme-hover text-white font-bold py-3 px-4 rounded-lg transition-all text-sm mt-2">
                Sign In
            </button>
        </form>

        <p class="text-center text-sm text-theme-text mt-4">
            <a href="{{ route('password.request') }}" class="text-theme-accent hover:underline font-medium">Forgot your password?</a>
        </p>

        <p class="text-center text-sm text-theme-text mt-4">
            New farmer? <a href="{{ route('register') }}" class="text-theme-accent hover:underline font-medium">Register here</a>
        </p>
    </div>
</div>

</body>
</html>



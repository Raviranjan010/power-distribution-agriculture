<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | Distribution of Electric Power for Agriculture</title>
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
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #0A110D; }
    </style>
</head>
<body class="text-theme-text antialiased min-h-screen flex items-center justify-center">

<div class="w-full max-w-md px-6">
    <div class="text-center mb-8">
        <div class="w-12 h-12 rounded-xl bg-theme-accent/20 border border-theme-accent/30 flex items-center justify-center text-theme-accent text-xl mx-auto mb-4">
            <i class="fa-solid fa-bolt"></i>
        </div>
        <h1 class="text-xl font-bold text-theme-heading">Ministry of Power</h1>
        <p class="text-xs text-theme-text">Agriculture Power Distribution</p>
    </div>

    <div class="bg-theme-panel border border-theme-border rounded-xl p-8">
        <h2 class="text-lg font-bold text-theme-heading mb-6">Set new password</h2>

        @if($errors->any())
            <div class="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-lg mb-4 text-sm">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label class="block text-xs font-bold text-theme-text tracking-widest uppercase mb-2">Email Address</label>
                <input type="email" name="email" value="{{ $email ?? old('email') }}" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-4 py-3 focus:outline-none focus:border-theme-accent text-white placeholder-theme-text/50 text-sm" required>
            </div>

            <div>
                <label class="block text-xs font-bold text-theme-text tracking-widest uppercase mb-2">New Password</label>
                <input type="password" name="password" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-4 py-3 focus:outline-none focus:border-theme-accent text-white placeholder-theme-text/50 text-sm" placeholder="••••••••" required>
            </div>

            <div>
                <label class="block text-xs font-bold text-theme-text tracking-widest uppercase mb-2">Confirm Password</label>
                <input type="password" name="password_confirmation" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-4 py-3 focus:outline-none focus:border-theme-accent text-white placeholder-theme-text/50 text-sm" placeholder="••••••••" required>
            </div>

            <button type="submit" class="w-full bg-theme-accent hover:bg-theme-hover text-white font-bold py-3 px-4 rounded-lg transition-all text-sm mt-2">
                Reset Password
            </button>
        </form>
    </div>
</div>

</body>
</html>

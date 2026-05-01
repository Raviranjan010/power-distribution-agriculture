<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Ministry of Power</title>
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
<body class="text-theme-text antialiased min-h-screen flex items-center justify-center py-10">

<div class="w-full max-w-2xl px-6">
    <div class="text-center mb-8">
        <div class="w-12 h-12 rounded-xl bg-theme-accent/20 border border-theme-accent/30 flex items-center justify-center text-theme-accent text-xl mx-auto mb-4">
            <i class="fa-solid fa-bolt"></i>
        </div>
        <h1 class="text-xl font-bold text-theme-heading">Farmer Registration</h1>
        <p class="text-xs text-theme-text">Ministry of Power · Agriculture Distribution</p>
    </div>

    <div class="bg-theme-panel border border-theme-border rounded-xl p-8">
        @if($errors->any())
            <div class="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-lg mb-4 text-sm">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-theme-text tracking-widest uppercase mb-2">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-4 py-3 text-white focus:outline-none focus:border-theme-accent text-sm" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-theme-text tracking-widest uppercase mb-2">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-4 py-3 text-white focus:outline-none focus:border-theme-accent text-sm" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-theme-text tracking-widest uppercase mb-2">Password</label>
                    <input type="password" name="password" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-4 py-3 text-white focus:outline-none focus:border-theme-accent text-sm" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-theme-text tracking-widest uppercase mb-2">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-4 py-3 text-white focus:outline-none focus:border-theme-accent text-sm" required>
                </div>
            </div>

            <hr class="border-theme-border my-2">
            <h3 class="text-sm font-bold text-theme-heading">Identity & Location</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-theme-text tracking-widest uppercase mb-2">Aadhaar Number</label>
                    <input type="text" name="aadhar_number" value="{{ old('aadhar_number') }}" maxlength="12" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-4 py-3 text-white focus:outline-none focus:border-theme-accent text-sm" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-theme-text tracking-widest uppercase mb-2">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" maxlength="10" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-4 py-3 text-white focus:outline-none focus:border-theme-accent text-sm" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-theme-text tracking-widest uppercase mb-2">Village</label>
                    <input type="text" name="village" value="{{ old('village') }}" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-4 py-3 text-white focus:outline-none focus:border-theme-accent text-sm" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-theme-text tracking-widest uppercase mb-2">District</label>
                    <select name="district" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-4 py-3 text-white focus:outline-none focus:border-theme-accent text-sm" required>
                        <option value="">Select District</option>
                        <option value="Nawanshahr">Nawanshahr</option>
                        <option value="Phagwara">Phagwara</option>
                        <option value="Hoshiarpur">Hoshiarpur</option>
                        <option value="Jalandhar">Jalandhar</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-theme-text tracking-widest uppercase mb-2">Full Address</label>
                <textarea name="address" rows="2" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-4 py-3 text-white focus:outline-none focus:border-theme-accent text-sm" required>{{ old('address') }}</textarea>
            </div>
            
            <button type="submit" class="w-full bg-theme-accent hover:bg-theme-hover text-white font-bold py-3 px-4 rounded-lg transition-all text-sm mt-2">
                Register as Farmer
            </button>
        </form>
        
        <p class="text-center text-sm text-theme-text mt-6">
            Already registered? <a href="{{ route('login') }}" class="text-theme-accent hover:underline font-medium">Sign in</a>
        </p>
    </div>
</div>

</body>
</html>

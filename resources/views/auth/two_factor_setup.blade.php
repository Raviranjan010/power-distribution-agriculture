@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto my-12">
    <div class="utilitarian-card p-8 border-t border-t-emerald-500">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-emerald-500/10 rounded-full flex items-center justify-center mx-auto mb-4 border border-emerald-500/20">
                <i class="fa-solid fa-qrcode text-2xl text-emerald-400"></i>
            </div>
            <h2 class="text-2xl font-bold text-theme-heading">Setup Two-Factor (TOTP)</h2>
            <p class="text-xs text-theme-text mt-2">Secure your administrative account using Google Authenticator or any standard TOTP verification system.</p>
        </div>

        @if($errors->any())
            <div class="bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs rounded-lg p-3.5 mb-6">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="space-y-6">
            <!-- Step 1: Scan QR Code -->
            <div class="text-center">
                <p class="text-[10px] text-theme-accent font-bold tracking-widest uppercase mb-3">Step 1: Scan QR Code</p>
                <div class="inline-block bg-white p-3.5 rounded-xl mb-3 shadow-md">
                    <img src="{{ $qrUrl }}" alt="Scan with Authenticator" class="w-44 h-44">
                </div>
                <p class="text-[10px] text-theme-text font-bold">Open Google Authenticator and scan this code</p>
            </div>

            <!-- Step 2: Backup Secret Key -->
            <div class="bg-[#0A110D] border border-theme-border rounded-lg p-4">
                <p class="text-[9px] text-theme-text font-bold tracking-widest uppercase mb-1">Step 2: Manual Secret Key Entry</p>
                <code class="block text-sm font-bold text-emerald-400 select-all tracking-wider text-center py-1.5">{{ chunk_split($secret, 4, ' ') }}</code>
                <p class="text-[8px] text-theme-text text-center mt-1">If scanning is unavailable, enter this secret key manually into your app</p>
            </div>

            <!-- Step 3: Enter Code to Confirm -->
            <form method="POST" action="{{ route('auth.two_factor.confirm') }}" class="space-y-4 pt-2">
                @csrf
                <div>
                    <label class="block text-[10px] text-theme-text font-bold tracking-widest uppercase mb-2">Step 3: Verification Code</label>
                    <input type="text" name="code" placeholder="000000" maxlength="6" autocomplete="off" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-4 py-2.5 text-white text-center text-xl font-bold tracking-wider focus:border-emerald-500 focus:outline-none" required>
                </div>

                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3 rounded-lg text-sm transition-colors flex items-center justify-center gap-2">
                    <i class="fa-solid fa-circle-check"></i> Verify & Enable 2FA
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

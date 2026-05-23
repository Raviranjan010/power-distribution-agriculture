@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto my-12">
    <div class="utilitarian-card p-8 border-t border-t-amber-500">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-amber-500/10 rounded-full flex items-center justify-center mx-auto mb-4 border border-amber-500/20">
                <i class="fa-solid fa-shield-halved text-2xl text-amber-500 animate-pulse"></i>
            </div>
            <h2 class="text-2xl font-bold text-theme-heading">Two-Factor Challenge</h2>
            <p class="text-xs text-theme-text mt-2">Open your authenticator app (Google Authenticator) and enter the 6-digit verification code below to authenticate.</p>
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

        <form method="POST" action="{{ route('auth.two_factor.verify') }}" class="space-y-6">
            @csrf
            <div>
                <label class="block text-[10px] text-theme-text font-bold tracking-widest uppercase mb-2">Authenticator Code</label>
                <input type="text" name="code" placeholder="000000" maxlength="6" autofocus autocomplete="one-time-code" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-4 py-3 text-white text-center text-2xl font-bold tracking-widest focus:border-amber-500 focus:outline-none" required>
            </div>

            <button type="submit" class="w-full bg-amber-600 hover:bg-amber-500 text-white font-bold py-3 rounded-lg text-sm transition-colors flex items-center justify-center gap-2">
                <i class="fa-solid fa-lock-open"></i> Complete Sign In
            </button>
        </form>
    </div>
</div>
@endsection

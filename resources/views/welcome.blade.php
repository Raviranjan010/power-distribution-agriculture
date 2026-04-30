@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[60vh] text-center">
    <div class="w-24 h-24 rounded-full bg-gradient-to-br from-brand-500 to-blue-600 flex items-center justify-center text-white font-bold text-5xl shadow-2xl shadow-brand-500/40 mb-8">
        <i class="fa-solid fa-bolt"></i>
    </div>
    <h1 class="text-4xl md:text-5xl font-extrabold mb-4 tracking-tight">
        Empowering Agriculture with <br>
        <span class="bg-clip-text text-transparent bg-gradient-to-r from-brand-400 to-blue-500">Reliable Electric Power</span>
    </h1>
    <p class="text-slate-400 text-lg md:text-xl max-w-2xl mb-10">
        A streamlined platform by the Ministry of Power for farmers to manage connections, view subsidies, and track power usage efficiently.
    </p>
    
    <div class="flex gap-4">
        <a href="{{ route('register') }}" class="bg-brand-600 hover:bg-brand-500 text-white font-semibold py-3 px-8 rounded-xl shadow-lg shadow-brand-500/30 transition-all flex items-center gap-2">
            Get Started <i class="fa-solid fa-arrow-right"></i>
        </a>
        <a href="{{ route('login') }}" class="glass-panel hover:bg-white/5 text-white font-semibold py-3 px-8 rounded-xl transition-all">
            Login
        </a>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto mt-10">
    <div class="glass-panel rounded-2xl p-8 border-t-4 border-t-brand-500 shadow-2xl">
        <h2 class="text-2xl font-bold mb-6 text-center">Login to your account</h2>
        
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-400 mb-1">Email Address</label>
                <input type="email" name="email" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-all text-white placeholder-slate-500" placeholder="farmer@example.com" required>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-slate-400 mb-1">Password</label>
                <input type="password" name="password" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-all text-white placeholder-slate-500" placeholder="••••••••" required>
            </div>
            
            <button type="submit" class="w-full bg-gradient-to-r from-brand-600 to-brand-500 hover:from-brand-500 hover:to-brand-400 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-brand-500/30 transition-all mt-4">
                Sign In
            </button>
        </form>
        
        <p class="text-center text-sm text-slate-400 mt-6">
            Don't have an account? <a href="{{ route('register') }}" class="text-brand-400 hover:text-brand-300 font-medium">Register as Farmer</a>
        </p>
    </div>
</div>
@endsection

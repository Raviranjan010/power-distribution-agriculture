@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto mt-10">
    <div class="glass-panel rounded-2xl p-8 border-t-4 border-t-brand-500 shadow-2xl">
        <h2 class="text-2xl font-bold mb-6 text-center">Farmer Registration</h2>
        
        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1">Full Name</label>
                    <input type="text" name="name" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-all text-white" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1">Email Address</label>
                    <input type="email" name="email" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-all text-white" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1">Password</label>
                    <input type="password" name="password" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-all text-white" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-all text-white" required>
                </div>
            </div>

            <hr class="border-slate-700 my-4">
            <h3 class="text-lg font-semibold text-brand-300">Land & Identity Details</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1">Aadhaar Number</label>
                    <input type="text" name="aadhaar_number" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-all text-white" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1">Phone Number</label>
                    <input type="text" name="phone_number" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-all text-white" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1">Land Area (Acres)</label>
                    <input type="number" step="0.01" name="land_area" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-all text-white" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1">Land Address</label>
                    <input type="text" name="land_address" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-all text-white" required>
                </div>
            </div>
            
            <button type="submit" class="w-full bg-gradient-to-r from-brand-600 to-brand-500 hover:from-brand-500 hover:to-brand-400 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-brand-500/30 transition-all mt-6">
                Register
            </button>
        </form>
        
        <p class="text-center text-sm text-slate-400 mt-6">
            Already have an account? <a href="{{ route('login') }}" class="text-brand-400 hover:text-brand-300 font-medium">Sign in</a>
        </p>
    </div>
</div>
@endsection

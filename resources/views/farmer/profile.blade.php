@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h2 class="text-3xl font-bold text-theme-heading mb-1">Profile Settings</h2>
    <p class="text-sm text-theme-text">Manage your account information</p>
</div>

<div class="utilitarian-card p-6 max-w-2xl mx-auto border-t border-t-emerald-500">
    <div class="flex items-center gap-6 mb-8 pb-8 border-b border-theme-border">
        @if($user->avatar)
            <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-24 h-24 rounded-full object-cover border-2 border-emerald-500/50">
        @else
            <div class="w-24 h-24 rounded-full bg-[#16271c] border-2 border-emerald-500/30 text-emerald-500 flex items-center justify-center text-3xl font-bold uppercase">
                {{ substr($user->name, 0, 2) }}
            </div>
        @endif
        
        <div>
            <h3 class="text-2xl font-bold text-theme-heading">{{ $user->name }}</h3>
            <p class="text-theme-text">{{ $user->email }}</p>
            <p class="text-sm mt-1 text-emerald-400 font-bold bg-[#16271c] inline-block px-2 py-0.5 rounded border border-emerald-500/20">
                Farmer ID: {{ $user->farmer_id_number ?? 'Pending' }}
            </p>
        </div>
    </div>

    <form method="POST" action="{{ route('farmer.profile.update') }}" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-theme-text tracking-widest uppercase mb-2">Profile Photo (Optional)</label>
                <input type="file" name="avatar" accept="image/*" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-4 py-2.5 text-white focus:border-emerald-500 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-emerald-500/20 file:text-emerald-400 hover:file:bg-emerald-500/30 transition-colors">
                <p class="text-[10px] text-theme-text mt-1">JPEG, PNG, JPG up to 2MB.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-theme-text tracking-widest uppercase mb-2">Phone Number</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-4 py-2.5 text-white focus:border-emerald-500 focus:outline-none placeholder-theme-text/40" required>
            </div>

            <div>
                <label class="block text-xs font-bold text-theme-text tracking-widest uppercase mb-2">Village</label>
                <input type="text" name="village" value="{{ old('village', $user->village) }}" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-4 py-2.5 text-white focus:border-emerald-500 focus:outline-none placeholder-theme-text/40" required>
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-theme-text tracking-widest uppercase mb-2">Complete Address</label>
                <textarea name="address" rows="3" class="w-full bg-[#0A110D] border border-theme-border rounded-lg px-4 py-2.5 text-white focus:border-emerald-500 focus:outline-none placeholder-theme-text/40" required>{{ old('address', $user->address) }}</textarea>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-theme-text tracking-widest uppercase mb-2">District</label>
                <input type="text" value="{{ $user->district }}" class="w-full bg-[#0A110D]/50 border border-theme-border rounded-lg px-4 py-2.5 text-theme-text cursor-not-allowed" disabled>
                <p class="text-[10px] text-theme-text mt-1">Contact SDO to change zone/district.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-theme-text tracking-widest uppercase mb-2">Aadhaar Number</label>
                <input type="text" value="********{{ substr($user->aadhar_number, -4) }}" class="w-full bg-[#0A110D]/50 border border-theme-border rounded-lg px-4 py-2.5 text-theme-text cursor-not-allowed" disabled>
            </div>
        </div>

        <div class="flex justify-end pt-6 border-t border-theme-border">
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-2.5 px-6 rounded-lg transition-colors text-sm flex items-center gap-2">
                <i class="fa-solid fa-save"></i> Save Changes
            </button>
        </div>
    </form>
</div>
@endsection

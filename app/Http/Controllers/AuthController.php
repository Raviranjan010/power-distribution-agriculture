<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Farmer;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $role = Auth::user()->role;
            if ($role === 'admin') return redirect()->route('admin.dashboard');
            if ($role === 'officer') return redirect()->route('officer.dashboard');
            return redirect()->route('farmer.dashboard');
        }

        return back()->withErrors(['email' => 'The provided credentials do not match our records.']);
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'aadhaar_number' => 'required|string|unique:farmers',
            'land_area' => 'required|numeric',
            'land_address' => 'required|string',
            'phone_number' => 'required|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'farmer',
        ]);

        Farmer::create([
            'user_id' => $user->id,
            'aadhaar_number' => $request->aadhaar_number,
            'land_area' => $request->land_area,
            'land_address' => $request->land_address,
            'phone_number' => $request->phone_number,
        ]);

        Auth::login($user);
        return redirect()->route('farmer.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}

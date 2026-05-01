<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Zone;

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
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Your account has been deactivated.']);
            }

            return match ($user->role) {
                'admin' => redirect()->route('admin.dashboard'),
                'sdo' => redirect()->route('officer.dashboard'),
                'lineman' => redirect()->route('lineman.dashboard'),
                default => redirect()->route('farmer.dashboard'),
            };
        }

        return back()->withErrors(['email' => 'The provided credentials do not match our records.']);
    }

    public function showRegister()
    {
        $zones = Zone::all();
        return view('auth.register', compact('zones'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'phone' => 'required|string|max:15',
            'aadhar_number' => 'required|string|max:12',
            'village' => 'required|string',
            'district' => 'required|string',
            'address' => 'required|string',
        ]);

        $year = date('Y');
        $lastFarmer = User::where('role', 'farmer')
            ->where('farmer_id_number', 'like', "KV-{$year}-%")
            ->orderByDesc('id')->first();

        if ($lastFarmer && preg_match('/KV-\d{4}-(\d+)/', $lastFarmer->farmer_id_number, $m)) {
            $nextNum = intval($m[1]) + 1;
        } else {
            $nextNum = 1001;
        }
        $farmerId = "KV-{$year}-{$nextNum}";
        $zone = Zone::where('district', $request->district)->first();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'farmer',
            'phone' => $request->phone,
            'farmer_id_number' => $farmerId,
            'village' => $request->village,
            'district' => $request->district,
            'address' => $request->address,
            'state' => 'Punjab',
            'aadhar_number' => $request->aadhar_number,
            'zone_id' => $zone?->id,
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

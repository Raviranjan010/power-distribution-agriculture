<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Zone;

use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

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
            'phone' => 'required|digits:10',
            'aadhar_number' => 'required|digits:12',
            'village' => 'required|string',
            'district' => 'required|exists:zones,district',
            'state' => 'required|string',
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
        $zone = Zone::where('district', $request->district)->where('state', $request->state)->first();

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
            'state' => $request->state,
            'aadhar_number' => $request->aadhar_number,
            'zone_id' => $zone?->id,
        ]);

        Auth::login($user);
        return redirect()->route('farmer.dashboard');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPassword(Request $request, $token)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill(['password' => Hash::make($password)])->setRememberToken(Str::random(60));
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}


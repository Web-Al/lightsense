<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'))->with('success', 'Welcome back.');
        }

        return back()->withErrors(['email' => 'These credentials do not match our records.'])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)->numbers()],
            'terms' => ['accepted'],
        ], [
            'terms.accepted' => 'Please agree to the Terms of Service and Privacy Policy.',
        ]);

        // The very first account becomes the administrator. Everyone else starts as a viewer.
        $isFirst = User::count() === 0;

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'], // hashed by the User model cast
            'role' => $isFirst ? 'admin' : 'viewer',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        $redirect = redirect()->route('dashboard');

        return $isFirst
            ? $redirect->with('success', 'Your account has been created. You are the administrator.')
            : $redirect->with('info', 'Your account can view the dashboard. Ask an administrator for Staff access to change schedules.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'You have signed out.');
    }
}

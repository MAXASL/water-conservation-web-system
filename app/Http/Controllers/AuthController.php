<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    private $adminEmail = 'admin@example.com';  // Set admin email
    private $adminPassword = 'admin1234';  // Set admin password

    // Show the login page
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Handle Registration (only household users can register)
    public function register(Request $request) {
        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'phone' => 'required|string',
            'address' => 'required|string',
        ]);

        $user = User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
            'user_type' => 'household',  // Default to household
        ]);

        return redirect()->route('index')->with('success', 'Household registered successfully');
    }

    // Handle Login
    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Check if the login credentials match the predefined admin account
        if ($request->email === $this->adminEmail && $request->password === $this->adminPassword) {
            return redirect()->route('index'); // Redirect to admin dashboard
        }

        // Authenticate regular users
        if (Auth::attempt($credentials)) {
            return redirect()->route('homee'); // Redirect household users
        }

        return back()->withErrors(['email' => 'Invalid email or password']);
    }

    // Handle Logout
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logged out successfully');
    }
    // Add this method to your existing AuthController
public function showRegistrationForm()
{
    return view('auth.register'); // This will use your existing registration form
}
}


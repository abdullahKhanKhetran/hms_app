<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // --- WEB AUTHENTICATION --- //

    public function showLogin() {
        return view('auth.login');
    }

    public function showRegister() {
        return view('auth.register');
    }

    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Redirect based on role
            $role = Auth::user()->role;
            if ($role == 2) return redirect()->route('admin.dashboard');
            if ($role == 1) return redirect()->route('doctor.dashboard');
            return redirect()->route('patient.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    public function register(Request $request) {
        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 0 // Default to Patient
        ]);

        Auth::login($user);
        return redirect()->route('patient.dashboard');
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    // --- API AUTHENTICATION (JWT) --- //

    public function apiLogin(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid Credentials'], 401);
        }

        // Create Token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login success',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }
    
    public function apiLogout(Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
    public function apiRegister(Request $request) {
        // 1. Validate the input
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|integer' 
        ]);

        // 2. Create the user
        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => Hash::make($fields['password']),
            'role' => $fields['role']
        ]);

        // 3. Generate a token immediately so they don't have to login again
        $token = $user->createToken('auth_token')->plainTextToken;

        // 4. Return JSON response
        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }
}
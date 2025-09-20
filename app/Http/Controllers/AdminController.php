<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function index(){
        return Inertia::render('/Modules/AdminDashBoard/Dashboard');
    }

    public function signIn(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        if (Auth::attempt($credentials)) {

            $request->session()->regenerate();
            $user = Auth::user();
            $roles = $user->getRoleNames();

            if ($roles->intersect(['Super Admin', 'Admin', 'staff', 'Reff'])->isNotEmpty()) {
                $token = $user->createToken('api-token')->plainTextToken;
                return redirect()->to(route('admin.dashboard.login'));
            }
        }
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }
}

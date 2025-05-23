<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login()
    {
        return view('pages.global.auth.login');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'password' => 'required',
        ], [
            'name.required' => 'Nama pengguna tidak boleh kosong',
            'password.required' => 'Kata sandi tidak boleh kosong',
        ]);

        if (Auth::attempt($request->only('name', 'password'))) {
            $request->session()->regenerate();
            if (auth()->user()->hasRole('super_admin')) {
                return redirect()->route('filament.admin.pages.dashboard');
            } elseif (auth()->user()->hasRole('gudang')) {
                return redirect()->route('filament.gudang.pages.dashboard');
            } elseif(auth()->user()->hasRole('kasir')) {
                return redirect()->route('kasir.index');
            }
        }

        return redirect()->back()->with('error', 'Nama pengguna atau kata sandi salah');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Berhasil logout');
    }
}

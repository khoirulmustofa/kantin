<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Socialite;

class GoogleAuthController extends Controller
{
    // Redirect user ke halaman login Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Handle callback dari Google
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Cari user berdasarkan email atau google_id
            $user = User::where('email', $googleUser->email)->first();

            if ($user) {
                // Jika user ada, update google_id-nya
                $user->update(['google_id' => $googleUser->id]);
            } else {
                // Jika user baru, buat akun baru
                $user = User::create([
                    'name' => $googleUser->name,
                    'username' => $googleUser->email,
                    'email' => $googleUser->email,
                    'password' => Str::password(10),
                    'role' => 'user',
                    'google_id' => $googleUser->id,
                ]);
            }



            if ($user->email_verified_at) {
                Auth::login($user);
                return redirect()->route('dashboard')->with('success', 'Login successful! Welcome back.');
            } else {
                return redirect()->route('home')->with('warning', 'Login module has not been opened.');
            }
        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'Gagal login dengan Google: ' . $e->getMessage());
        }
    }
}

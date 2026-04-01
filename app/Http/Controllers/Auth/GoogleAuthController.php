<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Exception;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Check if a user with this email or google_id already exists
            $user = User::where('email', $googleUser->email)->first();

            if ($user) {
                // If user exists, just update their google_id and avatar if missing
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->id,
                        'avatar' => $googleUser->avatar ?? $user->avatar,
                        'email_verified_at' => $user->email_verified_at ?? now(), // Google emails are verified
                    ]);
                }
                Auth::login($user);
            } else {
                // Create a new user
                $newUser = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'password' => null, // No password for social login users
                    'email_verified_at' => now(), // Google emails are verified
                ]);

                Auth::login($newUser);
            }

            return redirect()->intended(route('dashboard'));

        } catch (Exception $e) {
            \Log::error('Google Auth Error: ' . $e->getMessage());
            return redirect('/login')->withErrors(['email' => 'Hi ha hagut un problema iniciant sessió amb Google. Torna-ho a provar.']);
        }
    }
}

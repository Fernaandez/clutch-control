<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
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

            $user = User::where('email', $googleUser->email)->first();

            if ($user) {
                $updates = [];

                if (!$user->google_id) {
                    $updates['google_id'] = $googleUser->id;
                }

                if ($googleUser->avatar && empty($user->avatar)) {
                    $updates['avatar'] = $googleUser->avatar;
                }

                if ($updates !== []) {
                    $user->update($updates);
                }
            } else {
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'password' => null,
                ]);
            }

            $this->markGoogleEmailAsVerified($user);

            Auth::login($user->fresh());

            return redirect()->intended(route('dashboard'));
        } catch (Exception $e) {
            \Log::error('Google Auth Error: ' . $e->getMessage());
            return redirect('/login')->withErrors(['email' => 'Hi ha hagut un problema iniciant sessió amb Google. Torna-ho a provar.']);
        }
    }

    private function markGoogleEmailAsVerified(User $user): void
    {
        if ($user->hasVerifiedEmail()) {
            return;
        }

        $user->forceFill(['email_verified_at' => now()])->save();
    }
}

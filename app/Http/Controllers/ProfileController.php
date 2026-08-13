<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Els comptes creats amb Google no tenen contrasenya, i demanar-los
        // 'current_password' feia impossible esborrar el compte (requisit de
        // Google Play i de l'App Store). En aquest cas confirmem amb el correu.
        if ($user->hasPassword()) {
            $request->validate([
                'password' => ['required', 'current_password'],
            ]);
        } else {
            $request->validate([
                'confirm_email' => ['required', 'string'],
            ]);

            if (strcasecmp(trim((string) $request->input('confirm_email')), (string) $user->email) !== 0) {
                throw ValidationException::withMessages([
                    'confirm_email' => __('El correu no coincideix amb el del teu compte.'),
                ]);
            }
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function publicDestroy(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return back()->withErrors([
                'email' => __('auth.failed'),
            ]);
        }

        $user = Auth::user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Upload and update the user's profile avatar.
     */
    public function updateAvatar(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $user = $request->user();

        // Delete old avatar (only if it's a local file, not a Google URL)
        if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');

        $user->update(['avatar' => $path]);

        return response()->json(['avatar' => $path]);
    }

    /**
     * Store the Firebase Cloud Messaging device token.
     */
    public function updateDeviceToken(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'token' => 'required|string'
        ]);

        $request->user()->update([
            'fcm_token' => $request->token
        ]);

        return response()->json(['success' => true]);
    }
}

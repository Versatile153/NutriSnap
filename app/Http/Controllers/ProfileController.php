<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Profile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Handle the onboarding process to create or update the user's profile.
     */
    public function onboard(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'height' => 'required|numeric|min:100|max:250',
            'weight' => 'required|numeric|min:30|max:200',
            'goal' => 'required|in:weight_loss,maintain,weight_gain',
            'conditions' => 'array',
        ]);

        $calories = $this->calculateCalories($validated);

        $profile = Profile::updateOrCreate(
            ['user_id' => Auth::id()],
            array_merge($validated, ['daily_calories' => $calories])
        );

        return redirect()->route('profile')->with('status', 'Profile updated!');
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

        return Redirect::back()->with([
            'status' => 'profile-updated',
            'message' => __('messages.profile_saved_message'),
        ]);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Calculate daily calorie needs based on user data.
     */
    private function calculateCalories(array $data): int
    {
        // Simplified BMR (Mifflin-St Jeor for males, age hardcoded to 25)
        $bmr = 10 * $data['weight'] + 6.25 * $data['height'] - 5 * 25 + 5;
        // Multiply by activity factor (1.2 for sedentary)
        return round($bmr * 1.2);
    }
}
<?php

namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function users(){
        $users = User::all();
        return response()->json($users);
    }
    public function redirect(){
        return Socialite::driver('google')->redirect();
    }
    public function callback()
{
    try {
        $googleUser = Socialite::driver('google')->user();
        $existingUser = User::where('email', $googleUser->email)->first();

        if ($existingUser) {
            // ✅ Update missing fields, but keep the existing account_type
            $existingUser->google_id = $googleUser->id;
            $existingUser->name = $googleUser->name;
            $existingUser->avatar = $googleUser->avatar;
            $existingUser->email_verified_at = now();
            $existingUser->password = bcrypt('nemsu2024');
            $existingUser->status = $existingUser->status ?? 'active'; // Keep existing status, default to 'active'
            $existingUser->save();

            Auth::login($existingUser);
            return $this->redirectUser($existingUser->account_type);
        } else {
            // Create new user with default values
            $newUser = User::create([
                'google_id' => $googleUser->id,
                'email' => $googleUser->email,
                'name' => $googleUser->name,
                'avatar' => $googleUser->avatar,
                'account_type' => 'student', // Default only for new users
                'email_verified_at' => now(),
                'password' => bcrypt('nemsu2024'),
                'status' => 'active',
            ]);

            Auth::login($newUser);
            return $this->redirectUser($newUser->account_type);
        }

    } catch (Exception $e) {
        return redirect()->route('login')->with('error', 'Something went wrong: ' . $e->getMessage());
    }
}


    /**
     * Redirects user based on account type stored in the database.
     */
    private function redirectUser($accountType)
    {
        return match ($accountType) {
            'admin' => redirect()->intended(route('admin.index')),
            'instructor' => redirect()->intended(route('classlist.index')),
            default => redirect()->intended(route('user.index')), // Default redirect for students
        };
    }
    public function logout()
    {
        Auth::logout();
        return redirect(route('login'))->with('success', 'You have been logged out.');
    }

}

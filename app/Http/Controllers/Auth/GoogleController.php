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
    public function redirect(){
        return Socialite::driver('google')->redirect();
    }
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $finduser = User::where('google_id', $googleUser->id)->first();

            if ($finduser) {
                // ✅ Update user data to reflect changes in Google account
                $finduser->name = $googleUser->name;
                $finduser->avatar = $googleUser->avatar;
                $finduser->save();

                Auth::login($finduser);
                return $this->redirectUser($finduser->account_type);
            } else {
                // Create new user if not exists
                $newUser = User::create([
                    'google_id' => $googleUser->id,
                    'email' =>  $googleUser->email,
                    'name' =>  $googleUser->name,
                    'avatar' =>  $googleUser->avatar,
                    'account_type' => 'student', // Default account type
                    'email_verified_at' => now(),
                    'password' =>  bcrypt('nemsu2024'),
                    'status' => 'active',
                ]);

                Auth::login($newUser);
                return $this->redirectUser($newUser->account_type);
            }

        } catch (Exception $e) {
            return redirect()->route('home')->with('error', 'Something went wrong: ' . $e->getMessage());
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

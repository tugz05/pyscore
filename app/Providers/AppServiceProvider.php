<?php

namespace App\Providers;

use App\Models\Classlist;
use App\Models\JoinedClass;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
{
    View::composer('*', function ($view) {
        $user = Auth::user(); // Get the authenticated user

        // Ensure the user is logged in before trying to access their properties
        if ($user) {
            if ($user->account_type === 'instructor') {
                // Fetch class lists where the instructor is the owner
                $classlists = Classlist::where('user_id', $user->id)->get();
            }
            else if ($user->account_type === 'student'){
                // Fetch class lists the student has joined
                $classlists = JoinedClass::where('user_id', $user->id)
                ->with(('classlist'))
                ->get();
            }
        } else {
            // If user is not logged in, set classlists as an empty collection
            $classlists = collect();
            // Share data with all views
        }
        $view->with('classlists', $classlists);


    });
}
}

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
            $user = Auth::user();

            if ($user->account_type === 'instructor') {
                // Fetch class lists where the instructor is the owner
                $classlists = Classlist::where('user_id', $user->id)->get();
            } else {
                // Fetch class lists the student has joined
                $classlists = JoinedClass::where('user_id', $user->id)->get();
            }

            $view->with('classlists', $classlists);
        });
    }
}

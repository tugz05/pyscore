<?php


use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Instructor\DashboardController as InstructorDashboardController;
use App\Http\Controllers\Instructor\SectionController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use Illuminate\Support\Facades\Route;



Route::get('auth/redirect', action: [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('auth/google/callback', [GoogleController::class, 'callback']);
Route::get('/logout', [GoogleController::class, 'logout'])->name('logout');

Route::get('/', function () {
    return view('auth.login');
})->name('home');

Route::middleware(['account_type:instructor', 'auth'])->prefix('instructor')->group(function(){
    Route::get('/dashboard', [InstructorDashboardController::class, 'index'])->name('instructor.index');
    Route::get('/section', [SectionController::class, 'index'])->name('section');
});

Route::middleware(['account_type:student', 'auth'])->prefix('user')->group(function(){
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.index');
});


<?php


use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\ClasslistController;
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
    Route::resource('sections', SectionController::class);
    Route::get('/data', [SectionController::class, 'getSections'])->name('sections.data');
    Route::put('/sections/{id}', [SectionController::class, 'update'])->name('sections.update');
    Route::delete('/sections/{id}', [SectionController::class, 'destroy'])->name('sections.destroy');
    Route::put('/classlist/{id}', [ClasslistController::class, 'update'])->name('classlist.update');
    Route::delete('/classlist/{id}', [ClasslistController::class, 'destroy'])->name('classlist.destroy');
    Route::resource('classlist', ClasslistController::class);
    Route::get('/classlist_data', [ClasslistController::class, 'getClasslists'])->name('classlist.data');
});

Route::middleware(['account_type:student', 'auth'])->prefix('user')->group(function(){
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.index');
});


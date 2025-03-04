<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\InstructorController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\ClasslistController;
use App\Http\Controllers\Instructor\DashboardController as InstructorDashboardController;
use App\Http\Controllers\Instructor\SectionController;
use App\Http\Controllers\JoinedClassController;
use App\Http\Controllers\PythonEvaluationController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\SettingsController;
use Illuminate\Support\Facades\Route;

Route::get('auth/redirect', action: [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('auth/google/callback', [GoogleController::class, 'callback']);
Route::get('/logout', [GoogleController::class, 'logout'])->name('logout');
Route::get('/', function () {
    return view('auth.login');
})->name('login');

Route::middleware(['account_type:instructor', 'auth'])->prefix('instructor')->group(function () {
    Route::get('/dashboard', [InstructorDashboardController::class, 'index'])->name('instructor.index');
    Route::resource('sections', SectionController::class);
    Route::get('/data', [SectionController::class, 'getSections'])->name('sections.data');
    Route::put('/sections/{id}', [SectionController::class, 'update'])->name('sections.update');
    Route::delete('/sections/{id}', [SectionController::class, 'destroy'])->name('sections.destroy');
    Route::put('/classlist/{id}', [ClasslistController::class, 'update'])->name('classlist.update');
    Route::delete('/classlist/{id}', [ClasslistController::class, 'destroy'])->name('classlist.destroy');
    Route::resource('classlist', ClasslistController::class);
    Route::get('/classlist_data', [ClasslistController::class, 'getClasslists'])->name('classlist.data');
    Route::get('/class', [ClassController::class, 'index'])->name('class');
    Route::get('/class/i/{id}', [ClassController::class, 'viewClass'])->name('class.view');
    Route::get('/activities/list/{classlist_id}', [ClassController::class, 'list'])->name('activity.list');
    Route::post('/class/i/activity/store', [ClassController::class, 'store'])->name('activity.store');
    Route::put('/class/i/activity/update/{id}', [ClassController::class, 'update'])->name('activity.update');
    Route::delete('/class/i/activity/{id}', [ClassController::class, 'destroy'])->name('activity.destroy');
    Route::get('/activity/{id}', [ClassController::class, 'viewActivity'])->name('activity.view');
    Route::get('/get-classes', [ClassController::class, 'getAllClasses']);



        Route::resource('archives', ArchiveController::class);
        Route::post('/archive-data', [ArchiveController::class, 'archiveData'])->name('archive.data');
        Route::get('/archived-classlist', [ArchiveController::class, 'getArchivelists'])->name('archive.list');
        Route::post('/restore-class', [ArchiveController::class, 'restoreClass'])->name('archive.restore');



});

Route::middleware(['account_type:student', 'auth'])->prefix('student')->group(function () {
    Route::get('/join/class/s/{classId}', [JoinedClassController::class, 'joinClass'])->name('student.join.class');
    Route::get('/dashboard', [JoinedClassController::class, 'index'])->name('user.index');
    Route::get('/activities/list/{classlist_id}', [JoinedClassController::class, 'list'])->name('activity.list');
    Route::get('/classlist_data', [JoinedClassController::class, 'getClasslists'])->name('user.classlist.data');
    Route::resource('joinclass', JoinedClassController::class);
    Route::get('/class/i/{id}', [JoinedClassController::class, 'viewClass'])->name('user.class.view');
    Route::get('/activity/{id}', [JoinedClassController::class, 'viewActivity'])->name('user.activity.view');
    Route::post('/submit', [PythonEvaluationController::class, 'evaluate'])->name('submit.python.code');
    Route::get('/submission-status/{userId}/{activityId}', [ClassController::class, 'getSubmissionStatus']);
    Route::get('/check-submission', [PythonEvaluationController::class, 'checkSubmission'])->name('check.submission');
    Route::get('/settings', [SettingsController::class, 'index'])->name('user.settings');
});
Route::middleware(['account_type:admin', 'auth'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.index');
    Route::get('/instructors/data', [InstructorController::class, 'getInstructors'])->name('instructor.data');
    Route::get('/instructors', [InstructorController::class, 'index'])->name('admin.instructor');
    Route::get('/students', [StudentController::class, 'index'])->name('admin.student');
    Route::get('/dashboard', [HomeController::class, 'index'])->name('admin.index');
    Route::post('/admin/students/update-role', [StudentController::class, 'update'])->name('admin.student.update');
    Route::post('/admin/instructors/update-role', [InstructorController::class, 'update'])->name('admin.instructor.update');
});

<?php

use App\Http\Controllers\Admin\ProxiesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web\LinkRedirectionCheckController;
use App\Http\Controllers\Web\LinksTesterController;
use App\Models\Web\CountryName;
use Illuminate\Support\Facades\Route;


//* Admin dashboard
Route::resource('proxy', ProxiesController::class);
// Get proxy bulk action
Route::post('proxy-deleted', [ProxiesController::class, 'bulkAction']);
//* Admin dashboard end


//* home page
// home page
Route::get('/', [LinkRedirectionCheckController::class, 'dashboard']);
// tester logic
Route::resource('tester', LinkRedirectionCheckController::class)->middleware('throttle:only_fifty_time');
// Get share link report
Route::get('link-test/{unique_id?}', [LinkRedirectionCheckController::class, 'dashboard'])->middleware('linktester', 'throttle:only_fifty_time');
//* home page end 



//* login functionality
Route::get('/dashboard', function () {
    return view('Admin.admintemplate');
})->middleware(['auth', 'verified'])->name('dashboard');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
// login functionality end

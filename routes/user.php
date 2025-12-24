<?php

// use App\Http\Controllers\Backend\Admin\UserManagement\UserController;
// use Illuminate\Support\Facades\Route;

// Route::group(['middleware' => ['auth', 'admin', 'verified'], 'prefix' => 'admin'], function () {
//   // User Management
//   Route::group(['as' => 'am.', 'prefix' => 'user-management'], function () {
//     // User Routes
//     Route::resource('user', UserController::class);
//     Route::controller(UserController::class)->name('user.')->prefix('user')->group(function () {
//       Route::post('/show/{user}', 'show')->name('show');
//       Route::get('/status/{user}', 'status')->name('status');
//       Route::get('/trash/bin', 'trash')->name('trash');
//       Route::get('/restore/{user}', 'restore')->name('restore');
//       Route::delete('/permanent-delete/{user}', 'permanentDelete')->name('permanent-delete');
//     });
//   });
// });

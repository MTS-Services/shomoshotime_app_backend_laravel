<?php

use App\Http\Controllers\Backend\Admin\ApplicationSettingController;
use App\Http\Controllers\Backend\Admin\Area\AreaController;
use App\Http\Controllers\Backend\Admin\Category\CategoryController;
use App\Http\Controllers\Backend\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Backend\Admin\ChatManagement\ConversationController;
use App\Http\Controllers\Backend\Admin\ChatManagement\ParticipantController;
use App\Http\Controllers\Backend\Admin\Notification\NotificationController;
use App\Http\Controllers\Backend\Admin\PackageManagement\PackageController;
use App\Http\Controllers\Backend\Admin\PropertyManagement\PropertyController;
use App\Http\Controllers\Backend\Admin\PropertyManagement\PropertyTypeController;
use App\Http\Controllers\Backend\Admin\UserManagement\AdminController;
use App\Http\Controllers\Backend\Admin\UserManagement\UserController;
use App\Http\Controllers\Backend\Admin\UserManagement\UserProfileController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'admin', 'verified'], 'prefix' => 'admin'], function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('/hi', [AdminDashboardController::class, 'sayHi'])->name('admin.sayHi');

    // Admin Management
    Route::group(['as' => 'am.', 'prefix' => 'admin-management'], function () {
        // Admin Routes
        Route::resource('admin', AdminController::class);
        Route::controller(AdminController::class)->name('admin.')->prefix('admin')->group(function () {
            Route::post('/show/{admin}', 'show')->name('show');
            Route::get('/status/{admin}', 'status')->name('status');
            Route::get('/trash/bin', 'trash')->name('trash');
            Route::get('/restore/{admin}', 'restore')->name('restore');
            Route::delete('/permanent-delete/{admin}', 'permanentDelete')->name('permanent-delete');
        });
        Route::resource('user', UserController::class);
        Route::controller(UserController::class)->name('user.')->prefix('user')->group(function () {
            Route::post('/show/{user}', 'show')->name('show');
            Route::get('/status/{user}', 'status')->name('status');
            Route::get('/trash/bin', 'trash')->name('trash');
            Route::get('/restore/{user}', 'restore')->name('restore');
            Route::delete('/permanent-delete/{user}', 'permanentDelete')->name('permanent-delete');
        });
    });
    Route::resource('area', AreaController::class);
    Route::controller(AreaController::class)->name('area.')->prefix('area')->group(function () {
        Route::post('/show/{area}', 'show')->name('show');
        Route::get('/status/{area}', 'status')->name('status');
        Route::get('/trash/bin', 'trash')->name('trash');
        Route::get('/restore/{area}', 'restore')->name('restore');
        Route::delete('/permanent-delete/{area}', 'permanentDelete')->name('permanent-delete');
    });
    Route::resource('category', CategoryController::class);
    Route::controller(CategoryController::class)->name('category.')->prefix('category')->group(function () {
        Route::post('/show/{category}', 'show')->name('show');
        Route::get('/trash/bin', 'trash')->name('trash');
        Route::get('/restore/{category}', 'restore')->name('restore');
        Route::delete('/permanent-delete/{category}', 'permanentDelete')->name('permanent-delete');
    });

    Route::group(['as' => 'pam.', 'prefix' => 'package-management'], function () {
        Route::resource('package', PackageController::class);
        Route::controller(PackageController::class)->name('package.')->prefix('package')->group(function () {
            Route::post('/show/{package}', 'show')->name('show');
            Route::get('/status/{package}', 'status')->name('status');
            Route::get('/trash/bin', 'trash')->name('trash');
            Route::get('/restore/{package}', 'restore')->name('restore');
            Route::delete('/permanent-delete/{package}', 'permanentDelete')->name('permanent-delete');
        });
    });

    Route::group(['as' => 'pm.', 'prefix' => 'property-management'], function () {
        // Property Type
        Route::resource('property-type', PropertyTypeController::class);
        Route::controller(PropertyTypeController::class)->name('property-type.')->prefix('property-type')->group(function () {
            Route::post('/show/{propertyType}', 'show')->name('show');
            Route::get('/status/{propertyType}', 'status')->name('status');
            Route::get('/trash/bin', 'trash')->name('trash');
            Route::get('/restore/{propertyType}', 'restore')->name('restore');
            Route::delete('/permanent-delete/{propertyType}', 'permanentDelete')->name('permanent-delete');
        });
        // property
        Route::resource('property', PropertyController::class);
        Route::controller(PropertyController::class)->name('property.')->prefix('property')->group(function () {
            Route::post('/show/{property}', 'show')->name('show');
            Route::get('/status/{property}', 'status')->name('status');
            Route::get('/trash/bin', 'trash')->name('trash');
            Route::get('/restore/{property}', 'restore')->name('restore');
            Route::delete('/permanent-delete/{property}', 'permanentDelete')->name('permanent-delete');
        });
    });
    Route::controller(ApplicationSettingController::class)->name('app-settings.')->prefix('application-settings')->group(function () {
        Route::post('/update-settings', 'updateSettings')->name('update-settings');
        Route::get('/', 'general')->name('general');
        Route::get('/database', 'database')->name('database');
        Route::get('/smtp', 'smtp')->name('smtp');
    });
    Route::get('profile', [UserProfileController::class, 'UserProfile'])->name('user-profile');
    Route::post('/user-profile/save', [UserProfileController::class, 'storeOrUpdate'])->name('user-profiles.save');
    Route::get('/company-info', [UserProfileController::class, 'companyInfo'])->name('company-info');



    Route::group(['as' => 'cm.', 'prefix' => 'chat-management'], function () {
        Route::resource('conversation', ConversationController::class);
        Route::controller(ConversationController::class)->name('conversation.')->prefix('conversation')->group(function () {
            Route::post('/show/{conversation}', 'show')->name('show');
            Route::get('/trash/bin', 'trash')->name('trash');
            Route::get('/restore/{conversation}', 'restore')->name('restore');
            Route::delete('/permanent-delete/{conversation}', 'permanentDelete')->name('permanent-delete');
        });

        Route::resource('participant', ParticipantController::class);
        Route::controller(ParticipantController::class)->name('participant.')->prefix('participant')->group(function () {
            Route::post('/show/{participant}', 'show')->name('show');
            Route::get('/trash/bin', 'trash')->name('trash');
            Route::get('/restore/{participant}', 'restore')->name('restore');
            Route::delete('/permanent-delete/{participant}', 'permanentDelete')->name('permanent-delete');
        });
    });

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/details', [NotificationController::class, 'details'])->name('notifications.details');
});

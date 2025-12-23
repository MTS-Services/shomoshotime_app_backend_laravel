<?php

use App\Http\Controllers\Backend\DatatableController;
use App\Http\Controllers\Backend\FileManagementController;
use App\Http\Controllers\MultiLangController;
use Illuminate\Support\Facades\Route;

Route::post('update/sort/order', [DatatableController::class, 'updateSortOrder'])->name('update.sort.order');
Route::post('/content-image/upload', [FileManagementController::class, 'contentImageUpload'])->name('file.ci_upload');


Route::post('language',[MultiLangController::class,'langChange'])->name('lang.change');

Route::get('/fcm', function () {
    return view('generate-fcm');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/user.php';
require __DIR__ . '/frontend.php';

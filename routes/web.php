<?php
use App\Http\Controllers\MultiLangController;
use Illuminate\Support\Facades\Route;




Route::post('language', [MultiLangController::class, 'langChange'])->name('lang.change');

Route::get('/fcm', function () {
    return view('generate-fcm');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/frontend.php';

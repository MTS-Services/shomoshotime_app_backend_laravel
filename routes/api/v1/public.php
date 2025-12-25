<?php

use App\Http\Controllers\API\V1\AreaController;
use App\Http\Controllers\API\V1\PackageManagement\SubscriptionPlanController;
use App\Http\Controllers\API\V1\AgentController;
use App\Http\Controllers\API\V1\NotificationController;
use App\Http\Controllers\API\V1\PropertyManagement\PropertyController;
use App\Http\Controllers\API\V1\PropertyManagement\CategoryController;
use App\Http\Controllers\API\V1\PropertyManagement\PropertyTypeController;
use Illuminate\Support\Facades\Route;




Route::post('/send-notification', [NotificationController::class, 'sendNotification']);
Route::post('/send-notification-multiple', [NotificationController::class, 'sendToMultiple']);
Route::post('/send-notification-topic', [NotificationController::class, 'sendToTopic']);

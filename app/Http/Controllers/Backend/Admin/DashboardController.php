<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Services\FirebaseNotificationService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    protected FirebaseNotificationService $firebaseNotificationService;

    public function __construct(FirebaseNotificationService $firebaseNotificationService)
    {
        $this->firebaseNotificationService = $firebaseNotificationService;
    }

    public function index()
    {
        return view('backend.admin.dashboard');
    }

    public function sayHi()
    {
        $this->firebaseNotificationService->sendToDevice(user()->fcm_token, 'Say Hi', 'Hi ' . user()->name . ', How are you?');
        return redirect()->back();
    }
}

<?php

namespace App\Http\Controllers\Backend\Admin\Notification;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class NotificationController extends Controller
{
    /**
     * Display a static page with dummy notifications.
     */
    public function index()
    {
        // Dummy data for notifications

        return view('backend.admin.notification.all-notifications');
    }

    public function details(Request $request)
    {
        return view('backend.admin.notification.notification-details');
    }
}

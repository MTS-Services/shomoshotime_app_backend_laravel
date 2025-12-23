<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SMS\DezSmsService;

class SmsController extends Controller
{
    public function sendSmsViaService(DezSmsService $dezsmsService)
    {
        // $mobileNumber = '96590078005'; // Replace with dynamic number
        $mobileNumber = '1234567890'; // Replace with dynamic number
        $message = 'Hello from Laravel!';

        $result = $dezsmsService->sendSms($mobileNumber, $message);

        if ($result['success']) {
            return response()->json(['status' => 'success', 'message' => $result['message']]);
        } else {
            return response()->json(['status' => 'error', 'message' => $result['error']], 500);
        }
    }

    public function test()
    {
        return response()->json(['status' => 'success']);
    }
}

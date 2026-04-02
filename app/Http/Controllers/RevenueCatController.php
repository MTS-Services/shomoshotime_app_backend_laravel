<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RevenueCatController extends Controller
{
    public function handleWebhook(Request $request)
    {
        // Authorization verify
        $authHeader = $request->header('Authorization');
        if ($authHeader !== 'Bearer '.config('services.revenuecat.webhook_secret')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = $request->all();
        $event = $data['event']['type'] ?? null;
        $userId = $data['event']['app_user_id'] ?? null;
        $expireAt = $data['event']['expiration_at_ms'] ?? null;

        switch ($event) {
            case 'INITIAL_PURCHASE':
            case 'RENEWAL':
                DB::table('users')
                    ->where('id', $userId)
                    ->update(['is_premium' => 1]);
                DB::table('subscriptions')
                    ->updateOrInsert(
                        ['user_id' => $userId],
                        ['status' => 'active', 'expires_at' => $expireAt]
                    );
                break;

            case 'CANCELLATION':
                // Cancel doesn't mean immediate stop – it runs until expiry
                DB::table('subscriptions')
                    ->where('user_id', $userId)
                    ->update(['status' => 'canceled']);
                // is_premium shouldn't be set to 0 immediately, do it on EXPIRATION
                Log::info('User canceled: '.$userId);
                break;

            case 'EXPIRATION':
                // Subscription expired – revoke access now
                DB::table('users')
                    ->where('id', $userId)
                    ->update(['is_premium' => 0]);
                DB::table('subscriptions')
                    ->where('user_id', $userId)
                    ->update(['status' => 'expired']);
                break;

            case 'BILLING_ISSUE':
                Log::warning('Billing issue for: '.$userId);
                break;
        }

        // Always return 200 to prevent retries
        return response()->json(['status' => 'ok'], 200);
    }
}

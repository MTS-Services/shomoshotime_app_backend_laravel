<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UserLoginRequest;
use App\Models\UserDevice;
use App\Services\FirebaseNotificationService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Support\Str;

class AuthenticatedSessionController extends Controller
{

    protected FirebaseNotificationService $firebaseNotificationService;

    public function __construct(FirebaseNotificationService $firebaseNotificationService)
    {
        $this->firebaseNotificationService = $firebaseNotificationService;
    }

    public function create(): View
    {
        return view('frontend.auth.login');
    }

    /**
     * Handle an incoming authentication request and manage device session.
     */
    public function store(UserLoginRequest $request): RedirectResponse
    {
        // Authenticate the user.
        $request->authenticate();
        $request->session()->regenerate();

        // Retrieve the authenticated user instance.
        $admin = Auth::guard('web')->user();

        if (!$admin->is_admin) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->back()->with('error', 'Unauthorized access');
        }

        // Find or create the user's device record. This now applies to all successful logins.
        $adminDevice = UserDevice::firstOrNew(['user_id' => $admin->id]);

        // Update the device record with current session details.
        $adminDevice->fill([
            'device_token' => Str::uuid()->toString(), // Invalidate the old token with a new one
            'user_agent' => $request->header('User-Agent'),
            'ip_address' => $request->ip(),
            'last_login_at' => Carbon::now(),
        ])->save();

        // Update the admin user's last login and FCM token.
        $admin->update([
            'last_login_at' => now(),
            'fcm_token' => $request->fcm_token,
        ]);

        if ($admin->fcm_token != null) {
            $this->firebaseNotificationService->sendToDevice($admin->fcm_token, 'Admin Login', 'Admin has logged in');
        }

        return redirect()->intended(route('admin.dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}

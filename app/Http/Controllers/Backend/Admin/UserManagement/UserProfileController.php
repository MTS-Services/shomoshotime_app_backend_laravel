<?php

namespace App\Http\Controllers\Backend\Admin\UserManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserManagement\UserProfileRequest;
use App\Models\CompanyInformation;
use App\Models\UserProfile;
use App\Services\UserManagement\UserProfileService;

use function PHPUnit\Framework\isArray;

class UserProfileController extends Controller
{


    protected UserProfileService $service;

    public function __construct(UserProfileService $service)
    {
        $this->service = $service;
    }
    public function UserProfile()
    {
        $data['profile'] = UserProfile::with('user')->first();

        return view('backend.admin.user-management.user-profile.profile', $data);
    }


    public function storeOrUpdate(UserProfileRequest $request)
    {
        $validated = $request->validated();

        $allLinks = [];

        // Existing social links handling
        if (isset($validated['social_links']) && is_array($validated['social_links'])) {
            foreach ($validated['social_links'] as $key => $value) {
                if (!empty(trim($value))) { // Only add non-empty social links
                    $allLinks['social'][$key] = trim($value);
                }
            }
        }

        // Handle media links and add them to a 'media' key within $allLinks
        $mediaNames = $request->input('medianames', []);
        $mediaLinksInput = $request->input('medialinks', []);
        $count = max(count($mediaNames), count($mediaLinksInput));

        for ($i = 0; $i < $count; $i++) {
            $name = trim($mediaNames[$i] ?? '');
            $link = trim($mediaLinksInput[$i] ?? '');
            if (!empty($name) && !empty($link)) {
                $allLinks['media'][] = ['name' => $name, 'link' => $link];
            }
        }

        // Assign the combined array to 'social_links'
        $validated['social_links'] = $allLinks;
        // Remove 'media_links' from validated data if it's no longer a separate field
        unset($validated['medianames']);
        unset($validated['medialinks']);

        // Call service
        $this->service->updateUserProfile($validated);
        $message = 'Profile updated successfully!';
        return redirect()->back()->with('success', $message);
    }

    /**
     * Display the company information of the authenticated user.
     *
     * Retrieves the company information associated with the authenticated user's profile
     * and passes it to the 'company-information' view for rendering.
     *
     * @return \Illuminate\Contracts\View\View
     */

    public function companyInfo()
    {
        $companyInfo = CompanyInformation::with('user')->first();

        if ($companyInfo) {
            $companyInfo->social_links_arr = json_decode($companyInfo->social_links, true) ?? [];
        } else {
            $companyInfo = (object)[
                'social_links_arr' => []
            ];
        }

        $data['companyInfo'] = $companyInfo;

        return view('backend.admin.user-management.user-profile.company-information', $data);
    }
}

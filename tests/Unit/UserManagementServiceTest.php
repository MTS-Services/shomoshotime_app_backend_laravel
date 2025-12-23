<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\UserManagement\UserManagementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class UserManagementServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $userManagementService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userManagementService = new UserManagementService();
    }

    public function test_create_user_with_audit_fields()
    {
        $admin = User::factory()->create(['is_admin' => 1]);
        Auth::login($admin);

        $userData = [
            'name' => 'Test',
            'first_name_ar' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $user = $this->userManagementService->createUser($userData);

        $this->assertEquals($admin->id, $user->created_by);
    }

    public function test_update_user_with_audit_fields()
    {
        $admin = User::factory()->create(['is_admin' => 1]);
        $user = User::factory()->create();

        Auth::login($admin);

        $updateData = ['name' => 'Updated'];
        $this->userManagementService->updateUser($user, $updateData);

        $this->assertEquals($admin->id, $user->fresh()->updated_by);
    }
}

<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AuthService $authService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authService = app(AuthService::class);
    }

    public function test_staff_login_success_with_admin()
    {
        $user = User::factory()->create([
            'email' => 'admin@moodbrew.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $result = $this->authService->loginStaff('admin@moodbrew.com', 'password123', false);

        $this->assertTrue($result['success']);
        $this->assertEquals('/admin/dashboard', $result['redirect']);
        $this->assertEquals($user->id, auth()->id());
    }

    public function test_staff_login_success_with_cashier()
    {
        $user = User::factory()->create([
            'email' => 'cashier@moodbrew.com',
            'password' => Hash::make('password123'),
            'role' => 'cashier',
        ]);

        $result = $this->authService->loginStaff('cashier@moodbrew.com', 'password123', false);

        $this->assertTrue($result['success']);
        $this->assertEquals('/cashier/dashboard', $result['redirect']);
    }

    public function test_staff_login_fails_with_wrong_password()
    {
        User::factory()->create([
            'email' => 'admin@moodbrew.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $result = $this->authService->loginStaff('admin@moodbrew.com', 'wrongpassword', false);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('password salah', strtolower($result['message']));
    }

    public function test_staff_login_fails_with_customer_account()
    {
        User::factory()->create([
            'email' => 'customer@example.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
        ]);

        $result = $this->authService->loginStaff('customer@example.com', 'password123', false);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('bukan akun staff', strtolower($result['message']));
    }

    public function test_customer_quick_access_creates_session()
    {
        $result = $this->authService->customerQuickAccess('John Doe', 'john@example.com', '5');

        $this->assertTrue($result['success']);
        $this->assertEquals('John Doe', Session::get('customer_name'));
        $this->assertEquals('john@example.com', Session::get('customer_email'));
        $this->assertEquals('5', Session::get('table_number'));
    }

    public function test_customer_quick_access_with_existing_customer_user()
    {
        $user = User::factory()->create([
            'email' => 'existing@example.com',
            'role' => 'customer',
        ]);

        $result = $this->authService->customerQuickAccess('Jane Doe', 'existing@example.com', null);

        $this->assertTrue($result['success']);
        $this->assertEquals($user->id, auth()->id());
    }

    public function test_customer_quick_access_fails_with_staff_email()
    {
        User::factory()->create([
            'email' => 'admin@moodbrew.com',
            'role' => 'admin',
        ]);

        $result = $this->authService->customerQuickAccess('Admin User', 'admin@moodbrew.com', null);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('staff', strtolower($result['message']));
    }

    public function test_logout_clears_session()
    {
        Session::put('customer_name', 'John Doe');
        Session::put('customer_email', 'john@example.com');
        Session::put('table_number', '5');

        $this->authService->logout();

        $this->assertNull(Session::get('customer_name'));
        $this->assertNull(Session::get('customer_email'));
        $this->assertNull(Session::get('table_number'));
    }

    public function test_get_current_customer_returns_auth_user()
    {
        $user = User::factory()->create(['role' => 'customer']);
        $this->actingAs($user);

        $customer = $this->authService->getCurrentCustomer();

        $this->assertIsArray($customer);
        $this->assertEquals($user->name, $customer['name']);
        $this->assertEquals($user->email, $customer['email']);
    }

    public function test_get_current_customer_returns_session_data()
    {
        Session::put('customer_name', 'Guest User');
        Session::put('customer_email', 'guest@example.com');

        $customer = $this->authService->getCurrentCustomer();

        $this->assertEquals('Guest User', $customer['name']);
        $this->assertEquals('guest@example.com', $customer['email']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerAccessRequest;
use App\Http\Requests\StaffLoginRequest;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * AuthController - Handle authentication untuk semua role
 * 
 * - Admin/Cashier: Login dengan email + password
 * - Customer: Quick-access dengan nama + email (tanpa password)
 */
class AuthController extends Controller
{
    /**
     * @var AuthService
     */
    protected AuthService $authService;

    /**
     * Constructor dengan dependency injection
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Display staff login page (Admin/Cashier)
     */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * Handle staff login (Admin/Cashier)
     */
    public function login(StaffLoginRequest $request): RedirectResponse
    {
        $result = $this->authService->loginStaff(
            $request->validated('email'),
            $request->validated('password'),
            $request->boolean('remember')
        );

        if (!$result['success']) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => $result['message']]);
        }

        return redirect($result['redirect'])->with('success', $result['message']);
    }

    /**
     * Display customer quick-access page
     * Tanpa password, hanya nama dan email
     */
    public function showCustomerAccess(): View
    {
        return view('auth.customer-access');
    }

    /**
     * Handle customer quick-access
     */
    public function customerAccess(CustomerAccessRequest $request): RedirectResponse
    {
        $result = $this->authService->customerQuickAccess(
            $request->validated('name'),
            $request->validated('email'),
            $request->validated('table_number')
        );

        if (!$result['success']) {
            return back()
                ->withInput()
                ->withErrors(['email' => $result['message']]);
        }

        // Redirect ke intended URL jika ada, atau ke customer home
        $intendedUrl = session()->pull('url.intended', route('customer.home'));
        
        return redirect($intendedUrl)->with('success', $result['message']);
    }

    /**
     * Handle logout untuk semua role
     */
    public function logout(Request $request): RedirectResponse
    {
        $this->authService->logout();

        return redirect()->route('landing.home')->with('success', 'Berhasil logout.');
    }
}

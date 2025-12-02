<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StaffRequest;
use App\Services\StaffService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * StaffController - Handle CRUD operasi untuk staff (kasir)
 * 
 * Admin dapat menambah, mengedit, dan menghapus akun kasir
 */
class StaffController extends Controller
{
    /**
     * @var StaffService
     */
    protected StaffService $staffService;

    /**
     * Constructor dengan dependency injection
     */
    public function __construct(StaffService $staffService)
    {
        $this->staffService = $staffService;
    }

    /**
     * Display list of all staff (kasir)
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $staff = $this->staffService->getAllStaff(10, $search);
        $stats = $this->staffService->getStaffStats();

        return view('admin.staff.index', compact('staff', 'stats', 'search'));
    }

    /**
     * Show form untuk create new staff
     */
    public function create(): View
    {
        return view('admin.staff.create');
    }

    /**
     * Store new staff ke database
     */
    public function store(StaffRequest $request): RedirectResponse
    {
        $result = $this->staffService->createStaff($request->validated());

        if (!$result['success']) {
            return back()
                ->withInput()
                ->withErrors(['email' => $result['message']]);
        }

        return redirect()
            ->route('admin.staff.index')
            ->with('success', $result['message']);
    }

    /**
     * Show form untuk edit staff
     */
    public function edit(int $staff): View
    {
        $staffData = $this->staffService->getStaffById($staff);

        if (!$staffData) {
            abort(404, 'Staff tidak ditemukan');
        }

        return view('admin.staff.edit', ['staff' => $staffData]);
    }

    /**
     * Update staff data
     */
    public function update(StaffRequest $request, int $staff): RedirectResponse
    {
        $result = $this->staffService->updateStaff($staff, $request->validated());

        if (!$result['success']) {
            return back()
                ->withInput()
                ->withErrors(['email' => $result['message']]);
        }

        return redirect()
            ->route('admin.staff.index')
            ->with('success', $result['message']);
    }

    /**
     * Delete staff
     */
    public function destroy(int $staff): RedirectResponse
    {
        $result = $this->staffService->deleteStaff($staff);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()
            ->route('admin.staff.index')
            ->with('success', $result['message']);
    }
}

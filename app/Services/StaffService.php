<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * StaffService - Business logic untuk manajemen staff (kasir)
 * 
 * Menangani CRUD operations untuk akun kasir oleh admin
 */
class StaffService
{
    /**
     * Get all staff (cashier) dengan pagination
     * 
     * @param int $perPage
     * @param string|null $search
     * @return LengthAwarePaginator
     */
    public function getAllStaff(int $perPage = 10, ?string $search = null): LengthAwarePaginator
    {
        $query = User::where('role', User::ROLE_CASHIER);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Get staff by ID
     * 
     * @param int $id
     * @return User|null
     */
    public function getStaffById(int $id): ?User
    {
        return User::where('id', $id)
            ->where('role', User::ROLE_CASHIER)
            ->first();
    }

    /**
     * Create new staff (kasir)
     * 
     * @param array $data
     * @return array{success: bool, message: string, staff?: User}
     */
    public function createStaff(array $data): array
    {
        // Check email sudah ada
        if (User::where('email', $data['email'])->exists()) {
            return [
                'success' => false,
                'message' => 'Email sudah terdaftar.',
            ];
        }

        $staff = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => User::ROLE_CASHIER,
        ]);

        return [
            'success' => true,
            'message' => 'Akun kasir berhasil dibuat.',
            'staff' => $staff,
        ];
    }

    /**
     * Update staff data
     * 
     * @param int $id
     * @param array $data
     * @return array{success: bool, message: string, staff?: User}
     */
    public function updateStaff(int $id, array $data): array
    {
        $staff = $this->getStaffById($id);

        if (!$staff) {
            return [
                'success' => false,
                'message' => 'Staff tidak ditemukan.',
            ];
        }

        // Check email sudah ada (exclude current staff)
        if (isset($data['email']) && $data['email'] !== $staff->email) {
            if (User::where('email', $data['email'])->exists()) {
                return [
                    'success' => false,
                    'message' => 'Email sudah digunakan oleh akun lain.',
                ];
            }
        }

        $updateData = [
            'name' => $data['name'] ?? $staff->name,
            'email' => $data['email'] ?? $staff->email,
        ];

        // Update password jika diisi
        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $staff->update($updateData);

        return [
            'success' => true,
            'message' => 'Data kasir berhasil diperbarui.',
            'staff' => $staff->fresh(),
        ];
    }

    /**
     * Delete staff
     * 
     * @param int $id
     * @return array{success: bool, message: string}
     */
    public function deleteStaff(int $id): array
    {
        $staff = $this->getStaffById($id);

        if (!$staff) {
            return [
                'success' => false,
                'message' => 'Staff tidak ditemukan.',
            ];
        }

        // Optional: Check if staff has related data
        // For now, just delete
        $staff->delete();

        return [
            'success' => true,
            'message' => 'Akun kasir berhasil dihapus.',
        ];
    }

    /**
     * Get statistics for staff
     * 
     * @return array
     */
    public function getStaffStats(): array
    {
        return [
            'totalStaff' => User::where('role', User::ROLE_CASHIER)->count(),
            'totalAdmin' => User::where('role', User::ROLE_ADMIN)->count(),
        ];
    }
}

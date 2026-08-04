<?php

namespace App\Domain\UserManagement\Services;

use App\Models\User;
use App\Models\Role;
use App\Models\Branch;
use App\DTOs\User\UserFilterDTO;
use App\Core\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class UserManagementService
{
    public function __construct(
        protected UserRepositoryInterface $repository
    ) {}

    /**
     * List and paginate users with multi-tenant branch isolation.
     */
    public function listUsers(UserFilterDTO $filters, ?User $currentUser = null, int $perPage = 15)
    {
        // Enforce branch isolation for non-Super Admin users
        if ($currentUser && !$currentUser->hasRole('Super Admin')) {
            $effectiveBranchId = session('active_branch_id', $currentUser->branch_id);
            if ($effectiveBranchId) {
                $filters->branch_id = (int) $effectiveBranchId;
            }
        }

        return $this->repository->filterAndPaginate($filters, $perPage);
    }

    /**
     * Create a new user record.
     */
    public function createUser(array $data): User
    {
        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'status' => $data['status'] ?? \App\Enums\UserStatus::ACTIVE,
            'branch_id' => $data['branch_id'] ?? null,
        ];

        if (isset($data['avatar']) && $data['avatar'] instanceof UploadedFile) {
            $userData['avatar'] = $data['avatar']->store('uploads/avatars', 'public');
        }

        $user = User::create($userData);

        if (!empty($data['roles'])) {
            $roles = is_array($data['roles']) ? $data['roles'] : [$data['roles']];
            $user->roles()->sync($roles);
        }

        return $user->fresh(['roles', 'branch']);
    }

    /**
     * Update an existing user record.
     */
    public function updateUser(User $user, array $data): User
    {
        $userData = [];

        if (isset($data['name'])) {
            $userData['name'] = $data['name'];
        }

        if (isset($data['email'])) {
            $userData['email'] = $data['email'];
        }

        if (isset($data['phone'])) {
            $userData['phone'] = $data['phone'];
        }

        if (isset($data['status'])) {
            $userData['status'] = $data['status'];
        }

        if (array_key_exists('branch_id', $data)) {
            $userData['branch_id'] = $data['branch_id'];
        }

        if (!empty($data['password'])) {
            $userData['password'] = Hash::make($data['password']);
        }

        if (isset($data['avatar']) && $data['avatar'] instanceof UploadedFile) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $userData['avatar'] = $data['avatar']->store('uploads/avatars', 'public');
        }

        $user->update($userData);

        if (isset($data['roles'])) {
            $roles = is_array($data['roles']) ? $data['roles'] : [$data['roles']];
            $user->roles()->sync($roles);
        }

        return $user->fresh(['roles', 'branch']);
    }

    /**
     * Toggle user account status (ACTIVE, PASSIVE, SUSPENDED).
     */
    public function toggleStatus(User $user, string $status): User
    {
        $validStatuses = ['ACTIVE', 'PASSIVE', 'SUSPENDED'];
        $enumStatus = strtoupper($status);

        if (!in_array($enumStatus, $validStatuses)) {
            throw new \InvalidArgumentException("Invalid user status: {$status}");
        }

        $user->update([
            'status' => \App\Enums\UserStatus::from($enumStatus),
        ]);

        return $user->fresh();
    }

    /**
     * Assign / sync roles to a user.
     */
    public function assignRoles(User $user, array|int $roles): User
    {
        $roleIds = is_array($roles) ? $roles : [$roles];
        $user->roles()->sync($roleIds);

        return $user->fresh(['roles']);
    }

    /**
     * Reset user password.
     */
    public function resetPassword(User $user, string $newPassword): User
    {
        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        return $user->fresh();
    }
}

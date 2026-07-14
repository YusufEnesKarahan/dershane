<?php
namespace App\Domain\Auth\Actions\User;

use App\DTOs\User\UpdateUserDTO;
use App\Core\Repositories\Interfaces\UserRepositoryInterface;
use App\Events\UserUpdated;
use App\Events\UserRoleChanged;
use App\Events\UserPasswordChanged;
use App\Models\User;

class UpdateUserAction
{
    public function __construct(protected UserRepositoryInterface $repository) {}

    public function execute(User $user, UpdateUserDTO $dto): User
    {
        // Security checks
        $currentUser = auth()->user();
        if ($currentUser && $currentUser->id === $user->id) {
            // Check if removing own admin role
            $isAdminNow = $user->hasRole('Administrator');
            $willBeAdmin = in_array(
                \App\Models\Role::where('name', 'Administrator')->first()?->id,
                $dto->roles
            );
            if ($isAdminNow && !$willBeAdmin) {
                abort(403, 'You cannot remove your own Administrator role.');
            }
        }

        // Check if removing last administrator
        $adminRoleId = \App\Models\Role::where('name', 'Administrator')->first()?->id;
        if ($adminRoleId && $user->hasRole('Administrator') && !in_array($adminRoleId, $dto->roles)) {
            $adminCount = \App\Models\User::whereHas('roles', function ($q) {
                $q->where('name', 'Administrator');
            })->count();
            if ($adminCount <= 1) {
                abort(403, 'System must have at least one Administrator.');
            }
        }

        // Prevent last Administrator from being deactivated
        if ($user->hasRole('Administrator') && $dto->status !== 'ACTIVE') {
            $adminCount = \App\Models\User::whereHas('roles', function ($q) {
                $q->where('name', 'Administrator');
            })->where('status', 'ACTIVE')->count();
            if ($adminCount <= 1) {
                abort(403, 'The last active Administrator cannot be deactivated.');
            }
        }

        $data = [
            'name' => $dto->name,
            'email' => $dto->email,
            'phone' => $dto->phone,
            'status' => $dto->status,
            'branch_id' => $dto->branch_id,
        ];

        if ($dto->password) {
            $data['password'] = bcrypt($dto->password);
        }

        $user->update($data);

        $oldRoles = $user->roles->pluck('id')->toArray();
        $user->roles()->sync($dto->roles);

        // Dispatches
        event(new UserUpdated($user));

        if ($dto->password) {
            event(new UserPasswordChanged($user));
        }

        if (array_diff($oldRoles, $dto->roles) || array_diff($dto->roles, $oldRoles)) {
            event(new UserRoleChanged($user));
            // Clear permission cache
            app(\App\Domain\Auth\Services\PermissionCache::class)->clearUserCache($user);
        }

        return $user;
    }
}

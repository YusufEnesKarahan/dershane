<?php
namespace App\Domain\Auth\Actions\User;

use App\Core\Repositories\Interfaces\UserRepositoryInterface;
use App\Events\UserDeleted;
use App\Models\User;

class DeleteUserAction
{
    public function __construct(protected UserRepositoryInterface $repository) {}

    public function execute(User $user, bool $force = false): bool
    {
        $currentUser = auth()->user();
        if ($currentUser && $currentUser->id === $user->id) {
            abort(403, 'You cannot delete yourself.');
        }

        // Prevent deleting last administrator
        if ($user->hasRole('Administrator')) {
            $adminCount = \App\Models\User::whereHas('roles', function ($q) {
                $q->where('name', 'Administrator');
            })->count();
            if ($adminCount <= 1) {
                abort(403, 'System must have at least one Administrator.');
            }
        }

        if ($force) {
            $res = $this->repository->forceDelete($user->id);
        } else {
            $res = $this->repository->delete($user->id);
        }

        if ($res) {
            event(new UserDeleted($user));
        }

        return $res;
    }
}

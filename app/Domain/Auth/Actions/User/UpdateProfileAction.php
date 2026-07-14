<?php
namespace App\Domain\Auth\Actions\User;

use App\DTOs\User\UserProfileDTO;
use App\Events\UserUpdated;
use App\Models\User;

class UpdateProfileAction
{
    public function execute(User $user, UserProfileDTO $dto): User
    {
        $user->update([
            'name' => $dto->name,
            'email' => $dto->email,
            'phone' => $dto->phone,
        ]);

        event(new UserUpdated($user));

        return $user;
    }
}

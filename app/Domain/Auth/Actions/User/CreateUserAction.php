<?php
namespace App\Domain\Auth\Actions\User;

use App\DTOs\User\CreateUserDTO;
use App\Core\Repositories\Interfaces\UserRepositoryInterface;
use App\Events\UserCreated;
use App\Models\User;

class CreateUserAction
{
    public function __construct(protected UserRepositoryInterface $repository) {}

    public function execute(CreateUserDTO $dto): User
    {
        $user = $this->repository->create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => bcrypt($dto->password),
            'phone' => $dto->phone,
            'status' => $dto->status,
            'branch_id' => $dto->branch_id,
            'preferences' => $dto->preferences,
        ]);

        if (!empty($dto->roles)) {
            $user->roles()->sync($dto->roles);
        }

        event(new UserCreated($user));

        return $user;
    }
}

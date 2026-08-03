<?php
namespace App\Domain\Auth\Actions\User;

use App\Domain\Platform\Services\SubscriptionLimitService;
use App\DTOs\User\CreateUserDTO;
use App\Core\Repositories\Interfaces\UserRepositoryInterface;
use App\Events\UserCreated;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

class CreateUserAction
{
    public function __construct(
        protected UserRepositoryInterface $repository,
        protected SubscriptionLimitService $limitService
    ) {}

    public function execute(CreateUserDTO $dto): User
    {
        $branch = $dto->branch_id ? Branch::query()->findOrFail($dto->branch_id) : $this->limitService->resolveBranch();

        if ($branch && !$this->limitService->canAddUser($branch)) {
            throw new AuthorizationException('Mevcut abonelik planı kullanıcı eklemeye izin vermiyor.');
        }

        $user = $this->repository->create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => bcrypt($dto->password),
            'phone' => $dto->phone,
            'status' => $dto->status,
            'branch_id' => $branch?->id,
            'preferences' => $dto->preferences,
        ]);

        if (!empty($dto->roles)) {
            $user->roles()->sync($dto->roles);
        }

        event(new UserCreated($user));

        return $user;
    }
}

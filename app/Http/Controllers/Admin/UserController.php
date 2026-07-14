<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Branch;
use App\DTOs\User\CreateUserDTO;
use App\DTOs\User\UpdateUserDTO;
use App\DTOs\User\UserFilterDTO;
use App\Http\Requests\Admin\User\StoreUserRequest;
use App\Http\Requests\Admin\User\UpdateUserRequest;
use App\Domain\Auth\Services\UserService;
use App\Domain\Auth\Actions\User\CreateUserAction;
use App\Domain\Auth\Actions\User\UpdateUserAction;
use App\Domain\Auth\Actions\User\DeleteUserAction;
use App\Domain\Auth\Actions\User\RestoreUserAction;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(protected UserService $service) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $filters = UserFilterDTO::fromRequest($request->all());
        $users = $this->service->paginate($filters);
        
        $roles = Role::all();
        $branches = Branch::all();

        return view('admin.users.index', compact('users', 'roles', 'branches'));
    }

    public function create()
    {
        $this->authorize('create', User::class);
        
        $roles = Role::all();
        $branches = Branch::all();

        return view('admin.users.create', compact('roles', 'branches'));
    }

    public function store(StoreUserRequest $request, CreateUserAction $action)
    {
        $dto = CreateUserDTO::fromRequest($request->validated());
        $action->execute($dto);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);
        
        $roles = Role::all();
        $branches = Branch::all();

        return view('admin.users.edit', compact('user', 'roles', 'branches'));
    }

    public function update(UpdateUserRequest $request, User $user, UpdateUserAction $action)
    {
        $dto = UpdateUserDTO::fromRequest($request->validated());
        $action->execute($user, $dto);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user, DeleteUserAction $action)
    {
        $this->authorize('delete', $user);
        $action->execute($user);

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    public function restore(int $id, RestoreUserAction $action)
    {
        $user = User::withTrashed()->findOrFail($id);
        $this->authorize('update', $user);
        $action->execute($id);

        return redirect()->route('admin.users.index')->with('success', 'User restored successfully.');
    }

    public function bulk(Request $request, \App\Core\Repositories\Interfaces\UserRepositoryInterface $repository)
    {
        $this->authorize('delete', User::class);

        $action = $request->input('bulk_action');
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return redirect()->back()->with('error', 'No users selected.');
        }

        switch ($action) {
            case 'delete':
                $repository->bulkDelete($ids);
                break;
            case 'restore':
                $repository->bulkRestore($ids);
                break;
            case 'status_active':
                $repository->bulkStatus($ids, 'ACTIVE');
                break;
            case 'status_passive':
                $repository->bulkStatus($ids, 'PASSIVE');
                break;
        }

        return redirect()->route('admin.users.index')->with('success', 'Bulk action completed successfully.');
    }
}

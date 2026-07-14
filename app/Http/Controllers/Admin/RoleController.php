<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use App\DTOs\Role\CreateRoleDTO;
use App\DTOs\Role\UpdateRoleDTO;
use App\DTOs\Role\RoleFilterDTO;
use App\Http\Requests\Admin\Role\StoreRoleRequest;
use App\Http\Requests\Admin\Role\UpdateRoleRequest;
use App\Http\Requests\Admin\Role\CloneRoleRequest;
use App\Domain\Auth\Services\RoleService;
use App\Domain\Auth\Services\PermissionMatrixService;
use App\Domain\Auth\Actions\Role\CreateRoleAction;
use App\Domain\Auth\Actions\Role\UpdateRoleAction;
use App\Domain\Auth\Actions\Role\DeleteRoleAction;
use App\Domain\Auth\Actions\Role\RestoreRoleAction;
use App\Domain\Auth\Actions\Role\CloneRoleAction;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct(
        protected RoleService $service,
        protected PermissionMatrixService $matrixService
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Role::class);

        $filters = RoleFilterDTO::fromRequest($request->all());
        $roles = $this->service->paginate($filters);

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $this->authorize('create', Role::class);

        $role = new Role();
        $matrix = $this->matrixService->getMatrix($role);
        $permissions = Permission::all();

        return view('admin.roles.create', compact('matrix', 'permissions'));
    }

    public function store(StoreRoleRequest $request, CreateRoleAction $action)
    {
        $dto = CreateRoleDTO::fromRequest($request->validated());
        $action->execute($dto);

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    public function show(Role $role)
    {
        $this->authorize('view', $role);
        
        $users = $role->users()->take(10)->get();
        $userCount = $role->users()->count();

        return view('admin.roles.show', compact('role', 'users', 'userCount'));
    }

    public function edit(Role $role)
    {
        $this->authorize('update', $role);

        $matrix = $this->matrixService->getMatrix($role);
        $permissions = Permission::all();

        return view('admin.roles.edit', compact('role', 'matrix', 'permissions'));
    }

    public function update(UpdateRoleRequest $request, Role $role, UpdateRoleAction $action)
    {
        $dto = UpdateRoleDTO::fromRequest($request->validated());
        $action->execute($role, $dto);

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role, DeleteRoleAction $action)
    {
        $this->authorize('delete', $role);
        $action->execute($role);

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }

    public function restore(int $id, RestoreRoleAction $action)
    {
        $role = Role::withTrashed()->findOrFail($id);
        $this->authorize('update', $role);
        $action->execute($id);

        return redirect()->route('admin.roles.index')->with('success', 'Role restored successfully.');
    }

    public function showClone(Role $role)
    {
        $this->authorize('create', Role::class);
        return view('admin.roles.clone', compact('role'));
    }

    public function clone(CloneRoleRequest $request, Role $role, CloneRoleAction $action)
    {
        $action->execute($role, $request->name, $request->description, $request->color);

        return redirect()->route('admin.roles.index')->with('success', 'Role cloned successfully.');
    }

    public function bulk(Request $request, \App\Core\Repositories\Interfaces\RoleRepositoryInterface $repository)
    {
        $this->authorize('delete', Role::class);

        $action = $request->input('bulk_action');
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return redirect()->back()->with('error', 'No roles selected.');
        }

        switch ($action) {
            case 'delete':
                // Check system roles
                foreach ($ids as $id) {
                    $role = Role::find($id);
                    if ($role && $role->isSystemRole()) {
                        return redirect()->back()->with('error', 'System roles cannot be deleted.');
                    }
                }
                $repository->bulkDelete($ids);
                break;
            case 'restore':
                $repository->bulkRestore($ids);
                break;
        }

        return redirect()->route('admin.roles.index')->with('success', 'Bulk action completed successfully.');
    }
}

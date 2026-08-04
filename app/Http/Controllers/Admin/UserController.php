<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Branch;
use App\DTOs\User\UserFilterDTO;
use App\Domain\UserManagement\Services\UserManagementService;
use App\Domain\UserManagement\Actions\CreateUserAction;
use App\Domain\UserManagement\Actions\UpdateUserAction;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        protected UserManagementService $service
    ) {}

    /**
     * Display paginated user list with branch isolation.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $filters = UserFilterDTO::fromRequest($request->all());
        $users = $this->service->listUsers($filters, auth()->user());
        
        $roles = Role::all();
        $branches = Branch::all();

        return view('admin.users.index', compact('users', 'roles', 'branches'));
    }

    /**
     * Render user creation form.
     */
    public function create()
    {
        $this->authorize('create', User::class);
        
        $roles = Role::all();
        $branches = Branch::all();

        return view('admin.users.create', compact('roles', 'branches'));
    }

    /**
     * Store a new user.
     */
    public function store(Request $request, CreateUserAction $action)
    {
        $this->authorize('create', User::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:50',
            'status' => 'nullable|string|in:ACTIVE,PASSIVE,SUSPENDED',
            'branch_id' => 'nullable|exists:branches,id',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $action->execute($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Kullanıcı başarıyla oluşturuldu.');
    }

    /**
     * Render user edit form.
     */
    public function edit(User $user)
    {
        $this->authorize('update', $user);
        
        $roles = Role::all();
        $branches = Branch::all();

        return view('admin.users.edit', compact('user', 'roles', 'branches'));
    }

    /**
     * Update existing user record.
     */
    public function update(Request $request, User $user, UpdateUserAction $action)
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'phone' => 'nullable|string|max:50',
            'status' => 'nullable|string|in:ACTIVE,PASSIVE,SUSPENDED',
            'branch_id' => 'nullable|exists:branches,id',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $action->execute($user, $validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Kullanıcı bilgileri güncellendi.');
    }

    /**
     * Toggle user active/passive/suspended status.
     */
    public function toggleStatus(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'status' => 'required|string|in:ACTIVE,PASSIVE,SUSPENDED',
        ]);

        $this->service->toggleStatus($user, $validated['status']);

        return redirect()->back()->with('success', 'Kullanıcı durumu başarıyla değiştirildi.');
    }

    /**
     * Delete user record.
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Kullanıcı başarıyla silindi.');
    }

    /**
     * Bulk actions handler.
     */
    public function bulk(Request $request)
    {
        $this->authorize('create', User::class);

        $action = $request->input('bulk_action');
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return redirect()->back()->with('error', 'Lütfen en az bir kullanıcı seçin.');
        }

        foreach ($ids as $userId) {
            $user = User::find($userId);
            if (!$user || auth()->id() === $user->id) continue;

            if ($action === 'delete') {
                $user->delete();
            } elseif ($action === 'status_active') {
                $this->service->toggleStatus($user, 'ACTIVE');
            } elseif ($action === 'status_passive') {
                $this->service->toggleStatus($user, 'PASSIVE');
            }
        }

        return redirect()->route('admin.users.index')->with('success', 'Toplu işlem başarıyla tamamlandı.');
    }
}

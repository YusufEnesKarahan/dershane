# SaaS User & Role Management System Architecture

## Overview

The **User & Role Management System** provides centralized user administration, role assignment, account status toggles (`ACTIVE`, `PASSIVE`, `SUSPENDED`), password resets, and branch-level multi-tenant isolation for institution managers.

---

## 1. Database Schema

### `users`
- **Fields:** `id`, `name`, `email`, `phone`, `avatar`, `status` (Enum: `ACTIVE`, `PASSIVE`, `SUSPENDED`), `last_login_at`, `branch_id`, `password`, `preferences`, `created_at`, `updated_at`, `deleted_at`.
- **Tenant Scope:** Scoped via `branch_id` foreign key.

### RBAC Tables
- `roles`: `id`, `name`, `guard_name`, `created_at`, `updated_at`.
- `permissions`: `id`, `name`, `guard_name`, `created_at`, `updated_at`.
- `role_permissions`: `role_id`, `permission_id`.
- `role_user`: `role_id`, `user_id`.

---

## 2. Domain Layer Architecture (`app/Domain/UserManagement/`)

### `UserManagementService` (`Services/UserManagementService.php`)

Core business service enforcing tenant isolation and user mutations:

```php
use App\Domain\UserManagement\Services\UserManagementService;

$service = app(UserManagementService::class);

// List users with branch isolation for non-Super Admins
$users = $service->listUsers($filterDto, $currentUser, $perPage = 15);

// Create user
$user = $service->createUser([
    'name' => 'Ahmet Yılmaz',
    'email' => 'ahmet@dershane.com',
    'password' => 'secret123',
    'phone' => '05550000000',
    'status' => 'ACTIVE',
    'branch_id' => $branchId,
    'roles' => [$teacherRoleId],
]);

// Update user
$service->updateUser($user, $updateData);

// Toggle account status (ACTIVE, PASSIVE, SUSPENDED)
$service->toggleStatus($user, 'PASSIVE');

// Assign / sync roles
$service->assignRoles($user, [$roleId1, $roleId2]);

// Password reset
$service->resetPassword($user, 'newSecurePassword123');
```

---

## 3. Authorization & Tenant Isolation

### Policy (`UserManagementPolicy.php`)
Bound to `User::class` in `AppServiceProvider.php`.

- **Super Admin:** Full cross-branch access to view, create, edit, update, delete, and toggle status.
- **Branch Admin:** Full access strictly restricted to users belonging to their assigned branch (`branch_id`). Cannot manage users in other branches.
- **Teacher, Student, Parent:** Access denied (403 Forbidden).

---

## 4. Admin Management Panel

Located at `/admin/users` (Sidebar navigation under Access Management -> Users).

- **User Index Page (`admin.users.index`):** Modern responsive table displaying avatar with fallback, name, email, phone, role badges, status badge, branch name, last login time, search filters, and bulk status/delete actions.
- **Create User (`admin.users.create`):** Form with profile fields, branch selector, role multi-select, avatar upload, and initial status.
- **Edit User (`admin.users.edit`):** Profile edit form, role reassignment, status toggle, and optional password reset.

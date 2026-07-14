# User Management Module

This module governs creation, updates, soft-deletes, restorations, and role/branch assignments for all users in the system.

## Database & Models
- `status`: Mapped to `App\Enums\UserStatus` (`ACTIVE`, `PASSIVE`, `SUSPENDED`).
- `branch_id`: Represents the branch to which the user belongs.
- `last_login_at`: Tracks the last timestamp when the user authenticated.

## Repository & Domain Actions
All user modifications trigger events:
- `App\Events\UserCreated`
- `App\Events\UserUpdated`
- `App\Events\UserDeleted`
- `App\Events\UserPasswordChanged`
- `App\Events\UserRoleChanged`
- `App\Events\UserAvatarChanged`

## Security Checks
Within `UpdateUserAction` and `DeleteUserAction`:
- Prevents deleting your own account.
- Prevents removing your own `Administrator` role.
- Prevents deleting the last `Administrator` in the system.

# Role Management Module

The Role Management module provides administrative actions to organize user scopes and active permissions.

## Database & Models
- `roles`: Extended with `description` (text, nullable) and `color` (string, nullable).
- Belongs to users (`users()` relationship).
- Governed by `SystemRoleGuard` to prevent modifying or deleting protected roles.

## Controller & Actions
- `RoleController` delegates all creation, updates, cloning, and deletions to dynamic Domain Actions:
  - `CreateRoleAction`
  - `UpdateRoleAction`
  - `DeleteRoleAction`
  - `CloneRoleAction`
  - `SyncRolePermissionsAction`

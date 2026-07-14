# RBAC Architecture

This platform utilizes a completely custom, cache-backed Role-Based Access Control (RBAC) system optimized for multi-tenancy and high performance.

## Core Components
- **AuthorizationService:** The central authority for checking permissions. Evaluates if a User has a role or permission, automatically granting full access to `Administrator`.
- **PermissionCache:** Caches user permissions infinitely to Redis/File. The cache is automatically cleared when a user's role is updated, or a role's permissions are modified by the `RoleManager`.
- **PermissionManager & RoleManager:** Dedicated services for creating, deleting, and syncing roles and permissions.

## Database
- `roles` (name, guard)
- `permissions` (name, guard)
- `role_user` (pivot)
- `permission_role` (pivot)

# RBAC Architecture

This platform utilizes a completely custom, cache-backed Role-Based Access Control (RBAC) system optimized for multi-tenancy.

## Core Components
- **AuthorizationService**: Evaluates user access. Intercepts checks to automatically grant the `Administrator` role full permissions.
- **PermissionCache**: Infinite caching layer. Cleared and automatically rebuilt when role permissions are updated, preventing any stale caches without forcing a logout.
- **SystemRoleGuard**: Hardcoded guard protecting system roles (`Administrator`, `Super Admin`) from being deleted, renamed, or having their permissions stripped.
- **RoleCloneService**: Utility to duplicate existing roles and permission mappings.
- **EffectivePermissionService**: Unifies Roles, Editions, and Feature Flags into a single permission payload.

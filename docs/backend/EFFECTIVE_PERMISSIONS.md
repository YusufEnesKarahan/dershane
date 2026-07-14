# Effective Permission Service

The `EffectivePermissionService` (`app/Domain/Auth/Services/EffectivePermissionService.php`) computes the resolved permissions for a User.

## Execution Flow
- Resolves the union of permissions attached to all roles assigned to the User.
- Restricts these permissions using the User's Tenant/Company Edition settings (Basic, Professional, Ultimate).
- Further restricts these based on enabled or disabled Feature Flags.

This single service ensures that any policy check or UI rendering block checks the absolute, final set of active permissions.

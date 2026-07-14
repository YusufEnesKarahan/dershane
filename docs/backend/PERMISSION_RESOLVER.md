# Permission Resolver Architecture

The `PermissionResolver` (`app/Domain/Auth/Services/PermissionResolver.php`) acts as the single point of authority for evaluating permission queries inside the application.

## Integration Points
To ensure consistent authorization policies, the following features delegate their permission validations to the resolver:
- **Policies**: Blade and Controller authorization checks (e.g. `UserPolicy`, `PagePolicy`) call `$user->hasPermission()` which hooks directly into the resolver.
- **Middleware**: The custom `PermissionMiddleware` routes request validations through the resolver.
- **Blade Directives**: Arayüz directives like `@permission` and `@cananypermission` evaluate using the resolver.
- **Sidebar Engine**: The `VisibilityResolver` uses this service to hide or show sidebar menu links dynamically.

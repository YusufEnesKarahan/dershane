# Middleware

Custom route middleware exists for the RBAC and Edition checks.

## Registered Aliases
Defined in `bootstrap/app.php`:
- `role`: `\App\Http\Middleware\RoleMiddleware`
- `permission`: `\App\Http\Middleware\PermissionMiddleware`
- `edition`: `\App\Http\Middleware\EditionMiddleware`

## Usage Examples

**Protecting a route by Permission:**
```php
Route::get('/admin/users', [UserController::class, 'index'])->middleware('permission:users.view');
```

**Protecting a route by Role:**
```php
Route::get('/admin/reports', [ReportController::class, 'index'])->middleware('role:Administrator');
```

**Protecting a route by Edition:**
```php
Route::get('/admin/crm', [CrmController::class, 'index'])->middleware('edition:professional');
```

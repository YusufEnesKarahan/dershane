# Policies

Every major Model in the system has a dedicated Policy generated in `app/Policies`.

## Mechanics
- Policies inject the `AuthorizationService` via the constructor.
- Methods check specific permissions using `hasPermission()` (e.g. `$user->hasPermission('users.view')`).

## Gate Interception
- `AppServiceProvider` contains a `Gate::before` hook that automatically grants all permissions if the user has the `Administrator` role, bypassing standard checks.

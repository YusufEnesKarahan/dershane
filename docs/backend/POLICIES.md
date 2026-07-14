# Policies

Every major Model in the system has a dedicated Policy generated in `app/Policies`.

## Mechanics
- Policies inject the `AuthorizationService` via the constructor.
- Policies map standard CRUD methods (`viewAny`, `view`, `create`, `update`, `delete`) to the specific resource permission (e.g., `pages.view`, `pages.create`).

## Gate Interception
- `AppServiceProvider` contains a `Gate::before` hook that automatically grants all permissions if the user has the `Administrator` role. This prevents redundant policy checking for super admins.

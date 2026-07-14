# Custom Authentication Foundation

The authentication system in this SaaS platform is completely custom-built, avoiding starter kits like Laravel Breeze or Jetstream to provide full control over multi-tenant and role-based logic.

## Architecture
- **AuthManager**: Resides in `app/Domain/Auth/Services/AuthManager.php`. It handles loading user roles, permissions, and active Editions/Features immediately after a successful login.
- **Actions**: `LoginAction`, `LogoutAction`, `ForgotPasswordAction`, `ResetPasswordAction`, `ChangePasswordAction`. These classes encapsulate the core business logic, session regeneration, rate limiting, and upcoming audit hooks.
- **Controllers**: Thin controllers that handle only the HTTP request/response cycle and delegate logic to Actions.
- **FormRequests**: Handle validation. `LoginRequest` includes strict rate-limiting.

## Security Policies
- **Rate Limiting**: Configured in `config/security.php`. By default, 5 attempts per minute.
- **Password Policies**: Managed via `config/security.php`. Default: Min 8 chars, requires uppercase, lowercase, numbers, and symbols.
- **Session**: Regenerates on login, invalidates on logout. Remember Me tokens are supported.

## UI / Views
Located in `resources/views/auth/`. Built with the platform's premium design system (`<x-section>`, `<x-container>`). Fully accessible via keyboard and ARIA labels.

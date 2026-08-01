# Sprint 8.4 — HQ Enterprise Identity & Access Management (IAM)

## Overview

In Sprint 8.4, the HQ Central Platform was enhanced with a comprehensive Enterprise Identity & Access Management (IAM) module. This robust system brings zero-trust principles and robust access controls to the platform without breaking existing architectural constraints.

## Key Accomplishments

### 1. Database and Storage Architecture
- A specialized migration `2026_07_30_085445_create_hq_iam_tables.php` was created to hold 10 core tables:
  - `roles`, `permissions`, `hq_role_permissions`, `hq_user_roles`
  - `access_policies`
  - `hq_api_keys`, `hq_service_accounts`
  - `hq_mfa_settings`, `hq_login_attempts`, `hq_security_sessions`
- **Zero SQLite Enforcement:** Adhered strictly to the MySQL constraint; all queries and relations are built efficiently for production RDS databases.

### 2. Core Service Layers & Identity
A new Service layer (`App\Core\Services\IAM`) was constructed to handle specialized authentication methods.
- **PermissionService (RBAC):** Enables cross-tenant and system-level Role-Based Access Control logic with caching and Super Admin overrides.
- **AccessPolicyService (ABAC):** An advanced rules engine that processes Contextual Access Policies based on Time, IP range (`Request::ip()`), and Resource scopes.
- **HQApiKeyService:** Manages headless access securely, ensuring hashed tokens (`token_hash`) and preventing usage of revoked keys.
- **HQServiceAccountService:** Facilitates machine-to-machine operations securely.
- **SessionManagementService:** Tracks active device footprints, IP usage, User Agents, and allows force-termination of rogue sessions.
- **LoginSecurityService:** Analyzes login patterns, detects brute-force, and records forensic data into `hq_login_attempts`.
- **MfaService:** Multi-Factor Authentication backend logic ready to pair with user TOTP tokens and manage recovery codes securely.

### 3. API & Controller Integrations
- Implemented `HQAuthApiController` with endpoints like `/auth/api-key/create`, `/auth/sessions`, `/auth/permissions` integrated via signature verification.
- Implemented UI through `HQIdentityController` enabling administrators to visualize active roles, API keys, sessions, and security logs in the dashboard natively via Blade views.

### 4. Zero RCE & Standard Compliance
- Built entirely within standard Laravel guidelines avoiding native shells.
- All RCE-prone native methods (`exec`, `system`, `eval`, `shell_exec`) were strictly forbidden.
- Used UUID bindings safely with Event listening architectures to maintain decoupled systems.

### 5. Tests
- Created `IAMSecurityTest.php` with 41 assertions achieving 100% pass rate.
- Simulated token validations, login brute-forcing, role syncing, and strict ABAC behaviors securely without affecting active configurations.

## Future Recommendations
- Integration of SSO standards (SAML2.0 / OIDC) natively into the UI based on the `MockSsoProvider` built.
- Addition of specialized Audit Log viewer tailored purely for anomaly detection using `SuspiciousLoginDetected` events.

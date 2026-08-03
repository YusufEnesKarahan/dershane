# Student Management Architecture

The Student Management module is the core operational module of the Dershane SaaS platform. It handles the complete lifecycle of a student within a specific tenant (branch).

## Core Components

1. **Http/Controllers/Admin/StudentController**
   - The primary entry point for student CRUD operations.
   - Refactored to be thin, moving business logic to `StudentManagementService`.
   - Injects `SubscriptionLimitService` to enforce SaaS limits before creation.
   - Authorizes actions via `StudentPolicy`.

2. **Domain/Student/Services/StudentManagementService**
   - Encapsulates all student-related business logic.
   - Handles multi-table atomic transactions (creating/updating a Student along with their Guardian, Contact, and Address records).
   - Generates platform audit logs (`PlatformAuditLog`) automatically on CRUD events.
   - Prepares aggregated data arrays for complex views like the student profile page.

3. **Domain/Tenant/Services/SubscriptionLimitService**
   - Centralized service for validating tenant SaaS limits.
   - Currently implements `checkStudentLimit($branchId)` by reading the active subscription plan and comparing it with the current non-deleted student count.

## Tenant Isolation

- **Global Scope (`TenantScoped` trait)**: The `Student` model automatically appends a `WHERE branch_id = ?` clause based on `TenantContext::getActiveBranchId()`.
- **RBAC & Policies (`StudentPolicy`)**: Ensures cross-tenant tampering is blocked by explicitly verifying that the actor's branch ID matches the target student's branch ID (`isSameTenant` method).
- **Controller Enforcement**: Methods like `index()` and `create()` also explicitly retrieve the active branch ID to use as a strict reference.

## Testing Strategy

- Tested via `tests/Feature/StudentManagementTest.php`.
- Includes scenarios for role-based authorization (Admin vs. Staff vs. Teacher).
- Tests the SaaS subscription limit enforcement.
- Tests tenant boundary violations (Tenant A cannot see Tenant B's students).

## Future Extensibility

- **StudentImportService**: Currently, students are created manually via the UI. A future sprint can introduce Excel/CSV imports utilizing the same `StudentManagementService::createStudent()` method to ensure consistency and limit enforcement.
- **Analytics**: Can be easily extended by injecting `StudentAnalyticsService` into the existing controller structure for the `analytics` route.

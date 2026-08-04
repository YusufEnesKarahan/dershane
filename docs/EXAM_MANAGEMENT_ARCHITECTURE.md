# Exam Management Module Architecture

The Exam Management Module is designed for the Dershane SaaS platform, ensuring high performance, tenant isolation, and adhering to strict permission and architectural standards.

## 1. Domain-Driven Design (DDD)

The module is organized under `app/Domain/Exam/` (although portions are accessible via standard Controller namespaces for HTTP layers).

### Service Classes
- **ExamManagementService**: Handles all core business logic for institution admins (Branch/Tenant Admins) and teachers. Includes creating exams, assigning classrooms, managing results, recalculating rankings, and compiling statistics.
- **StudentExamService**: (Or integrated within policies) Handles access patterns specifically restricted to students.
- **ParentExamService**: (Or integrated within policies) Manages access and visibility controls for parents (StudentGuardians).

### Architectural Rules
- **Thin Controller, Fat Service**: All business logic (ranking, limit checking, etc.) resides in the Services. Controllers only handle validation, authorization, and delegating to services.
- **Strict Tenant Isolation**: Enforced by the `TenantScoped` global scope on models (`Exam`, `ExamResult`, `ExamType`) using `TenantContext` to ensure models only query the active `branch_id`.

## 2. Database Models

- **`exams`**:
  - Contains core exam details (`title`, `exam_date`, `duration_minutes`, `total_score`, `status`).
  - Linked to a specific `branch_id` and optionally a `classroom_id`.
  - `exam_type_id` establishes the category (e.g., Mock Exam, Quiz).

- **`exam_types`**:
  - Classifies exams and allows filtering.
  - Linked to a `branch_id`.

- **`exam_results`**:
  - Stores individual student performance (`score`, `rank`, `notes`).
  - Relates `student_id` to `exam_id` within the boundaries of a `branch_id`.

## 3. Subscription & Limit Enforcement

Institutions have limits based on their subscription tier, managed by the JSON column `limits` on the `Plan` model.
- **Limit Checks**: The `SubscriptionLimitService` validates the maximum number of exams a branch can create (`$plan->limits['max_exams']`). Attempting to exceed this returns a redirect with a unified validation error.

## 4. Security & Permissions

Access is strictly controlled using the Spatie-compatible Role and Permission structure implemented via `PermissionDictionary` and Custom Policies:
- **Admin**: Has `exams.*` permissions, granting CRUD access to exams and result entry.
- **Teacher**: Granted `exams.view` and `exams.results`. Teachers cannot view or enter results for exams assigned to a classroom they do not teach, enforced via `ExamPolicy`.
- **Student**: Granted `exams.view` and `exams.results`. Strictly restricted to viewing only their own `ExamResult` models.
- **Parent**: Granted `exams.view` and `exams.results`. Restricted to viewing only results for `Student` models linked to their account via `StudentGuardian` (`relation`).

## 5. Caching & Middleware

- **`EnsureActiveBranch`**: Sets the active tenant context for the session.
- **`PermissionCache`**: Permission resolution aggressively caches user permissions. Any role assignment dynamically invalidates and regenerates this cache to maintain high-speed access resolution.
- **Gate Integration**: The application binds `ExamPolicy` within `AppServiceProvider`'s `boot()` method.

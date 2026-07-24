# Sprint 5.1 — RBAC & Authorization Audit

Audit scope: read-only review of route declarations, middleware, policies, controllers, permission services, menu metadata, and the seeded role matrix. No authorization code, migration, or seed data was changed.

## Executive summary

The project contains a usable role/permission data model, middleware aliases, a permission cache, policies, and admin-menu visibility metadata. These pieces are **not yet enforced consistently**. Every scoped admin, teacher, and parent route is protected by `auth`, but none is protected with route-level `permission` or `role` middleware. Many critical controllers do not call policies, and 13 policies contain unconditional `return true` decisions.

**Production readiness for RBAC: not ready.** The most serious risks are an authenticated-user-only admin surface, cross-record access in teacher flows, and a parent portal IDOR risk.

## 1. Route authorization audit

### Route inventory result

| Scope | Route count | `auth` | `permission` middleware | `role` middleware |
|---|---:|---:|---:|---:|
| Admin, Teacher, Parent and dashboard routes | 278 | 278 | 0 | 0 |

`routes/admin.php` applies `auth` to its full `/admin` group. `routes/teacher.php` and `routes/parent.php` also apply only `auth`. `routes/web.php` is a registry and `bootstrap/app.php` correctly loads it once.

### Findings

| Severity | Area | Finding | Impact |
|---|---|---|---|
| Critical | Admin routes | All admin endpoints, including users, roles, finance, HR, inventory, settings, documents, admissions and queue history, depend on `auth` only. | Any authenticated user reaches endpoints whose controller does not independently authorize the action. |
| Critical | Teacher routes | `/teacher/*` has no teacher-role middleware or equivalent route authorization. | Any authenticated user can enter teacher endpoints; controllers then rely on incomplete application logic. |
| Critical | Parent routes | `/parent/*` has no parent-role middleware or ownership guard at route level. | Any authenticated account can call parent portal endpoints. |
| High | Sensitive operations | Settings import/reset, user/role mutation, payroll, payments, employee actions, document operations and CRM mutation have no route permission guard. | A missing controller policy check becomes direct privilege escalation. |
| Medium | Dashboard route | `admin.dashboard` is `/dashboard` in `routes/auth.php`, while admin routes are `/admin/*`. | Not a security break by itself, but authorization placement is fragmented and easy to miss. |

## 2. Policy audit

### Policies with unconditional allow decisions

| Policy | Unconditional methods | Risk |
|---|---|---|
| `AdmissionPolicy` | viewAny, view, create, update, approve, delete | Full admission workflow is effectively open to any authenticated user when invoked. |
| `AssignmentPolicy` | CRUD methods | Homework data/actions are not permission-protected. |
| `AttendanceSessionPolicy` | CRUD methods | Attendance session and bulk-attendance authorization is ineffective. |
| `ClassroomPolicy` | CRUD methods | Classroom management is open once a controller calls the policy. |
| `EmployeePolicy` | CRUD methods | HR personnel data is unprotected. |
| `ExamPolicy` | CRUD methods | Exam management is unprotected. |
| `InvoicePolicy` | CRUD methods | Finance records are unprotected. |
| `LeadPolicy` | CRUD methods | CRM lead records are unprotected. |
| `LeavePolicy` | CRUD methods | Leave data/actions are unprotected. |
| `PayrollPolicy` | CRUD methods | Payroll is unprotected. |
| `ReportingPolicy` | viewAny, manage | Reporting/export actions are unprotected. |
| `StudentPolicy` | CRUD methods | Student PII and mutations are unprotected. |
| `TeacherPolicy` | CRUD methods | Teacher data and related operational actions are unprotected. |

### Permission-aware policies

`AssetPolicy`, `BlogPolicy`, `CoursePolicy`, `DocumentPolicy`, `InventoryPolicy`, `JobHistoryPolicy`, `NotificationPolicy`, `PagePolicy`, `PlatformSettingPolicy`, `PurchasePolicy`, `RegistrationPolicy`, `RolePolicy`, and `UserPolicy` contain permission checks. Their effectiveness still depends on their controllers actually invoking `authorize()`.

### Policy permission inconsistencies

The following policy permission names are neither in `PermissionDictionary` nor present in the seeded `permissions` table:

- `categories.create`, `categories.delete`, `categories.view`
- `comments.delete`, `comments.view`, `moderation.approve`
- `tags.create`, `tags.delete`, `tags.update`, `tags.view`
- `contactmessages.create`, `contactmessages.delete`, `contactmessages.update`, `contactmessages.view`

This makes `BlogCategoryPolicy`, `BlogCommentPolicy`, `BlogTagPolicy`, and `ContactMessagePolicy` deny non-administrators even when menu-level access appears available. `Administrator` bypasses the issue through `AuthorizationService` and `Gate::before`.

## 3. Controller authorization audit

### Modules with no authorization calls

| Module | Controllers audited with no `$this->authorize()` / direct permission check |
|---|---|
| Teachers | `TeacherController`, salary, schedule and contract flows rely on permissive `TeacherPolicy` where used. |
| Finance | `PaymentController`; `InvoiceController` calls a currently permissive `InvoicePolicy`. |
| HR | `AnalyticsController`, `EmployeeController`, `PayrollController`, `LeaveController`, `ExpenseController`, `AdvanceController`, `PerformanceController`. |
| CRM | `LeadController`, `LeadDashboardController`, `LeadPipelineController`, `LeadAnalyticsController`. |
| Admission | `AdmissionController`, `EnrollmentController`. |
| Documents | `DocumentController`, `DocumentCategoryController`, `DocumentSearchController`. |
| Inventory | `AssetController`, `AssetCategoryController`, `InventoryController`, `SupplierController`, `PurchaseController`, `MaintenanceController`, `TransferController`. |

`StudentController` and `InvoiceController` call `authorize()`, but their policies currently return `true`; these calls do not restrict access. The blog, page, media, user, role, notification, settings, course, classroom and system-job paths are the strongest current examples of policy invocation, though some referenced policy permissions are inconsistent.

### Portal object-ownership findings

1. **Parent portal IDOR — Critical.** `ParentPortalService::getDashboardData($parentId, $studentId)` loads the student by `findOrFail($studentId)` and never verifies that the student belongs to `$parentId`. A logged-in user can supply another student ID through `parent/dashboard?student_id=…` and receive attendance, exam, homework, invoice and announcement data.
2. **Teacher attendance — High.** The controller lists all attendance sessions and accepts any `session_id`; it does not verify that the session belongs to the logged-in teacher’s assigned classes before reading or writing attendance.
3. **Teacher homework — High.** The controller exposes all classrooms/courses and accepts arbitrary classroom, course, assignment and submission IDs. Creation and evaluation do not verify teacher ownership or assignment scope.

## 4. Permission-system audit

### Existing strengths

- `Permission`, `Role`, role-permission pivots, `AuthorizationService`, `PermissionResolver`, wildcard permissions, and cache invalidation services exist.
- `PermissionMiddleware` and `RoleMiddleware` correctly return HTTP 403 when invoked.
- `Gate::before` grants the `Administrator` role full access.
- All 19 distinct permissions used in `config/admin-menu.php` exist in `PermissionDictionary`.

### Gaps

- No scoped route currently invokes `permission:*` or `role:*` middleware.
- Menu visibility is not authorization. A user can directly request a hidden route when its controller lacks a policy check.
- The dictionary contains 103 values, but policy literals have drifted from it as listed above.
- `AuthorizationService` and `PermissionResolver` duplicate wildcard/administrator resolution logic, increasing divergence risk.
- The permission cache uses `rememberForever`; correctness depends on every role/user mutation clearing the relevant cache keys.

## 5. Current role/access matrix

This is the actual seeded role state, not a proposed target state.

| Requested role | Current role record | Current effective access | Assessment |
|---|---|---|---|
| Super Admin | Not present | None as a named role | Missing. |
| Admin | Not present; closest is `Administrator` | Global allow via `Gate::before`, `AuthorizationService`, and `PermissionResolver` | Functional naming differs from requested role. |
| Teacher | Present | `dashboard.view`, `students.view`, `courses.view`, `classrooms.view` | Route/policy enforcement gaps grant more access than this matrix intends. |
| Student | Not present | None | Missing. |
| Parent | Not present | None | Missing; parent portal is merely `auth`-protected. |
| Accountant | Not present | None | Missing. |
| Secretary | Not present; closest is `Reception` | Lead, registration and limited student permissions | Naming/permission mapping needs an explicit decision. |

Additional seeded roles are `Branch Manager`, `Marketing`, `Editor`, and `Viewer`. The `Administrator` role has no pivot permissions because it receives an intentional global bypass.

## 6. Required authorization test plan

Do not implement these in the audit phase. Add them before declaring RBAC hardened.

1. **Route middleware tests:** guest → login; authenticated user without permission → 403; permitted user → expected response for each critical module family.
2. **Role isolation tests:** Teacher, Parent, Reception/Secretary, Accountant and Administrator access the same route matrix with explicit expected outcomes.
3. **Policy matrix tests:** every policy ability (`viewAny`, `view`, `create`, `update`, `delete`, and custom abilities) must reject a user lacking the mapped permission.
4. **Object ownership tests:** parent cannot request another student ID; teacher cannot read/write another teacher’s session, assignment, submission or class roster.
5. **High-risk mutation tests:** settings reset/import, role/user mutation, payroll approval/payment, invoice cancellation/payment, document sharing/download, admission conversion, CRM pipeline conversion and job dashboards.
6. **Permission consistency test:** all policy and route permission literals must exist in `PermissionDictionary` and the seeded permission catalog.
7. **Cache invalidation tests:** role permission changes and user role assignment/revocation must take effect without stale authorization.
8. **Menu versus endpoint tests:** hiding a menu item must never be the only access-control mechanism.

## 7. Recommended remediation order

1. Close Parent portal IDOR and teacher cross-scope operations first.
2. Apply module-level route permission middleware for all admin domains, then add action-level policy enforcement for record operations.
3. Replace unconditional policy decisions with dictionary-backed permissions, starting with finance, HR, student, admission and CRM.
4. Define and seed the approved role catalogue: Super Admin/Admin, Teacher, Student, Parent, Accountant and Secretary, including whether existing names are renamed or mapped.
5. Reconcile policy literals with `PermissionDictionary`; do not introduce ad-hoc string permissions.
6. Add the test plan above and make unauthorized route assertions mandatory in CI.

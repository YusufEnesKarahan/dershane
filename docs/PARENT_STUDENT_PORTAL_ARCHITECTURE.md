# Parent & Student Portal Architecture

## Overview
The Parent and Student portals provide dedicated, role-based dashboards for guardians and students. This module adheres to the "Thin Controller, Fat Service" architectural pattern, leveraging Role-Based Access Control (RBAC) and Tenant Isolation.

## Key Principles
1. **Thin Controller**: Controllers (`ParentPortalController`, `StudentPortalController`) are responsible solely for handling HTTP requests, dependency injection, and returning views.
2. **Fat Service**: Business logic (fetching records, calculating stats, enforcing ownership checks) resides in `ParentPortalService` and `StudentPortalService`.
3. **Tenant Isolation**: Both modules respect the `EnsureActiveBranch` middleware and rely on `TenantContext` to ensure users cannot view data belonging to other branches.
4. **RBAC Security**: Access to the portals is restricted using specific roles (`Parent`, `Student`) and distinct permissions (`parent.view_child`, `student.view_profile`, etc.).
5. **Data Ownership**: A parent can ONLY access data for their associated children (linked via the `student_guardians` pivot structure and matching logic). Students can ONLY access their own profiles.

## Database Additions
- Added `user_id` to the `students` table to link a `Student` record to a login-capable `User`.
- Added `user_id` to the `student_guardians` table to link a `StudentGuardian` to a login-capable `User`.

## Components
- **Controllers**: `ParentPortalController`, `StudentPortalController`
- **Services**: `ParentPortalService`, `StudentPortalService`
- **Views**: 
  - `resources/views/portal/parent/dashboard.blade.php`
  - `resources/views/portal/student/dashboard.blade.php`
- **Routes**:
  - `routes/parent.php`
  - `routes/student.php`

## Permissions
The following permissions govern access to the Portal features:
- `parent.view_child`: Allows access to the Parent Dashboard.
- `parent.view_attendance`: Allows a parent to view their child's attendance.
- `student.view_profile`: Allows access to the Student Dashboard.
- `student.view_schedule`: Allows a student to view their own schedule.

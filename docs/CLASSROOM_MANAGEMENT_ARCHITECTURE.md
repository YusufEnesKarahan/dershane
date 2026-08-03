# Classroom Management Architecture

This document describes the architecture of the Classroom Management module in the Dershane CRM application.

## Overview
The Classroom Management module allows tenant admins to create and manage classrooms (Sınıflar) within their branch. A classroom serves as a container for students and is associated with a primary/advisor teacher. It includes its capacity, color code, type, and status.

## Key Principles Followed
- **Thin Controller, Fat Service**: All business logic (creation, capacity checks, teacher assignments, student attachments) is abstracted away from the controller into `ClassroomManagementService`.
- **Tenant Isolation**: All operations explicitly filter by `branch_id`. The active branch is retrieved from the `TenantContext`. The `ClassroomPolicy` ensures that users can only view or modify classrooms within their branch.
- **RBAC**: Handled via `classrooms.view` and `classrooms.manage` permissions. `ClassroomPolicy` uses these permissions to authorize actions.
- **Subscription Limits**: Checked in `SubscriptionLimitService` using the `max_classrooms` property on the `Plan` model. A tenant cannot create more classrooms than their plan allows.

## Components

### Models
- **Classroom**: Holds `name`, `code`, `capacity`, `color_code`, `is_active`, `branch_id`, `classroom_type_id`, and `teacher_id`.
  - Uses `TenantScoped` trait to ensure global isolation (except for super admins).
  - Relationships: `teacher()` (advisor), `students()` (enrolled students), `schedules()`.

### Services
- **`ClassroomManagementService`**: 
  - Handles `createClassroom`, `updateClassroom`, `deleteClassroom`.
  - Handles `attachStudents` and `detachStudents`.
  - Ensures robust database transactions (`DB::transaction`) for data consistency.
- **`SubscriptionLimitService`**:
  - `checkClassroomLimit(int $branchId)` verifies the active branch's classroom count against their subscription plan's `max_classrooms` attribute.

### Controller
- **`ClassroomController`**:
  - Located at `App\Http\Controllers\Admin\ClassroomController`.
  - Endpoints for `index`, `create`, `store`, `edit`, `update`, `show`, `destroy`.
  - Custom endpoints for `students`, `attachStudents`, `detachStudents`.
  - Only concerns itself with request validation, authorization checks via Gate/Policy, and formatting responses.

### Views
- Located at `resources/views/admin/classrooms/`.
- Uses responsive modern UI mimicking a premium SaaS dashboard.
- Includes list view (`index`), detail view (`show`), creation form (`create`), edit form (`edit`), and student assignment view (`students`).

### Tests
- **`ClassroomManagementTest`**: Validates end-to-end functionality including tenant isolation, subscription limits, and authorization.

## Database Migrations
- `2026_08_03_115234_add_teacher_id_to_classrooms_table`: Adds `teacher_id` foreign key.
- `2026_08_03_120100_add_max_classrooms_to_plans_table`: Adds `max_classrooms` limit to subscription plans.

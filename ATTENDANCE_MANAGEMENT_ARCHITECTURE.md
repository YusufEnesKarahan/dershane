# Attendance & Presence Management Architecture

## 1. Overview
The Attendance & Presence Management Module (Sprint 9.3) handles daily attendance tracking, teacher session management, and portal reporting for the Dershane SaaS platform. It strictly enforces tenant isolation and integrates with the role-based access control (RBAC) and subscription limit systems.

## 2. Core Principles
- **Thin Controller, Fat Service**: All business logic, including mass updates, limit validation, and reporting, resides inside the `AttendanceManagementService` and `AttendanceReportService`. Controllers only handle validation and routing.
- **Tenant Isolation**: Every attendance session and record is tied to a `branch_id`. Global scopes (`TenantScoped`) ensure data does not leak across branches.
- **Limit Enforcement**: `SubscriptionLimitService` validates the maximum number of daily attendance sessions (`max_daily_attendance` in the Plan's JSON limits).
- **Ad-Hoc Sessions**: Attendance sessions can optionally be tied to a `lesson_schedule_id` but can also be created independently just for a specific classroom and teacher.

## 3. Database Schema
### `attendance_sessions`
- Tracks an attendance taking instance.
- **Fields**: `id`, `branch_id`, `classroom_id`, `lesson_schedule_id` (nullable), `teacher_id`, `session_date`, `start_time` (nullable), `end_time` (nullable), `status` (open, completed, cancelled).

### `attendance_records`
- Tracks the specific attendance status of a single student for a specific session.
- **Fields**: `id`, `branch_id`, `attendance_session_id`, `student_id`, `status` (present, absent, late, excused), `note` (nullable).

### `attendance_settings`
- Stores branch-specific preferences for attendance policies (e.g., late threshold).
- **Fields**: `id`, `branch_id`, `late_threshold_minutes`, `notify_parents_on_absence`, `notify_parents_on_late`.

## 4. Services
### `AttendanceManagementService`
- Handles the creation of sessions and bulk saving of attendance records.
- Ensures duplicate submissions for the same student in the same session overwrite the existing record rather than duplicating it.

### `AttendanceReportService`
- Generates aggregated attendance statistics (e.g., total sessions, present count, absent count, late count) at both the branch level (for Admins) and student level (for Parents and Students).

## 5. Security & Authorization
- **Policies**: `AttendanceSessionPolicy` and `AttendanceRecordPolicy` handle access control.
- **Permissions**:
    - `attendance.view`
    - `attendance.create`
    - `attendance.update`
    - `attendance.delete`
    - `attendance.report`
- **Role Scopes**:
    - Teachers can only update and view attendance for sessions assigned to them (`user->teacher->id === session->teacher_id`).
    - Students and Parents can only view attendance records belonging to their associated student profile.

## 6. Testing
A comprehensive test suite (`AttendanceManagementTest.php`) verifies:
- Admin session creation and reporting.
- Teacher access isolation (own class vs. other class).
- Student view restrictions.
- Tenant isolation (Admin cannot view sessions from another branch).
- Avoidance of duplicate attendance records on multiple updates.
- Enforcement of subscription limits (`max_daily_attendance`).

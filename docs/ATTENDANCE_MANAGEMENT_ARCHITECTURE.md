# Attendance Management Architecture

## Overview
The Attendance Management module allows teachers and administrators to record, track, and manage student attendance. It integrates with the Tenant architecture to ensure strict data isolation across branches and enforces role-based access control (RBAC).

## Key Components

### 1. Models & Database
- **`AttendanceSession`**: Represents a single instance of a class gathering. Contains `branch_id`, `classroom_id`, `course_id`, `teacher_id`, `session_date`, `start_time`, and `end_time`. Uses the `TenantScoped` trait.
- **`Attendance`**: Represents a student's attendance record for a specific session. Contains `attendance_session_id`, `student_id`, `attendance_status_id`, and `remarks`.
- **`AttendanceStatus`**: A lookup table defining statuses such as 'P' (Present), 'A' (Absent), 'L' (Late), and 'E' (Excused).

### 2. Service Layer
- **`AttendanceManagementService`**: Encapsulates core business logic.
  - `createSession(array $data, int $branchId)`: Creates a new session bound to a tenant.
  - `takeAttendance(int $sessionId, array $records, ?int $teacherId = null)`: Handles bulk upserts of attendance records. Implements security checks to verify teacher assignments and ensures students belong to the classroom.
  - `generateReports(int $branchId, array $filters)`: Generates attendance analytics and summaries.

### 3. Controller Layer
- **`Admin\AttendanceController`**: Handles administrative attendance duties. Admins can view reports, manage sessions, and bulk-update attendance.
- **`Teacher\TeacherAttendanceController`**: Restricted to the teacher portal. Teachers can only view and manage sessions assigned to them.

### 4. Security & Authorization
- **Policies**: `AttendanceSessionPolicy` explicitly checks for `attendance.*` permissions and verifies that the `branch_id` matches the `TenantContext::getActiveBranchId()` to enforce strict tenant isolation.
- **Route Model Binding & Tenant Context**: The `EnsureActiveBranch` middleware initializes the TenantContext before the controller logic is executed, ensuring that global scopes operate correctly on Route Model Binding lookups.
- **Exception Handling**: HttpExceptions (e.g., 403 Forbidden) from `abort()` are properly caught and re-thrown or handled by the centralized exception handler to ensure robust security checks without unexpected 302 redirects during API/test flows.

## Testing Strategy
The module is comprehensively tested via `AttendanceManagementTest.php`:
- **Tenant Isolation**: Verifies that users from one branch cannot access sessions in another.
- **Teacher Security**: Ensures teachers can only take attendance for their explicitly assigned classrooms and courses.
- **Student Data Integrity**: Validates that attendance can only be taken for students officially enrolled in the respective classroom.
- **Admin Privileges**: Confirms that Tenant Admins have unrestricted access within their branch boundary.

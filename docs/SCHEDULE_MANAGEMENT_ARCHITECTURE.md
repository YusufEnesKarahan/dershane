# Schedule Management Architecture

## 1. Overview
The Schedule (Timetable) Management Module (Sprint 8.9) is designed to handle all aspects of class scheduling for the Dershane SaaS platform. It strictly enforces tenant isolation, handles robust conflict detection (Teacher, Classroom, Room), and integrates seamlessly with the role-based access control (RBAC) and subscription limit systems.

## 2. Core Principles
- **Thin Controller, Fat Service**: All business logic, especially conflict validation and CRUD operations, resides inside `LessonScheduleManagementService`. Controllers only handle basic validation and response routing.
- **Tenant Isolation**: Every schedule record is tied to a `branch_id`. Global scopes (`TenantScoped`) ensure data does not leak across branches.
- **Limit Enforcement**: `SubscriptionLimitService` validates the maximum number of schedules a branch can create (`max_schedules` in the Plan's JSON limits).

## 3. Database Schema
### `schedule_slots`
- Pre-defined time slots for branches (e.g. "Morning Slot 1: 08:00-08:45"). (Optional usage depending on branch preferences).

### `lesson_schedules`
The core table storing the actual schedule items.
- `branch_id`: Ensures multi-tenant isolation.
- `academic_term_id`: Associates the schedule with a specific term.
- `classroom_id`, `course_id`, `teacher_id`: The core relationships.
- `day_of_week`, `start_time`, `end_time`: Time definitions.
- `room`: Physical location.

### `lesson_schedule_teachers`
A pivot table allowing multiple teachers (e.g., co-teachers or assistants) to be assigned to a single lesson schedule block.

## 4. Services
### `LessonScheduleManagementService`
Provides methods like `createSchedule`, `updateSchedule`, `deleteSchedule`, `duplicateWeek`, and handles complex queries like `validateTeacherConflict`.

### Portal Services
- `TeacherPortalService::getWeeklySchedule()`
- `StudentPortalService::getWeeklySchedule()`
- `ParentPortalService::getStudentSchedule()`
These methods format the schedule specific to the requesting entity, enforcing data visibility strictly based on their relationships.

## 5. Security & Authorization
- **Policies**: `LessonSchedulePolicy` uses the predefined permissions (`schedules.view`, `schedules.create`, etc.) to gate access. 
- Teachers can only view schedules assigned to them (either directly via `teacher_id` or via the pivot table).
- Administrators with `schedules.*` permissions have full CRUD capabilities.

## 6. Testing
`ScheduleManagementTest` covers:
- Authorized access to create schedules.
- Strict prevention of overlapping schedules for the same teacher or classroom.
- Enforcement of `CheckOnboardingStatus` bypass for unit testing standard CRUD logic.

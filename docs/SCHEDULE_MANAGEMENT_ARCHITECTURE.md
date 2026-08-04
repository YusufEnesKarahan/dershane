# Schedule & Lesson Management Architecture

## 1. Overview
The **Schedule & Lesson Management Module** (Sprint 9.6) provides classroom schedule management for educational institutions. It allows administrators to create and manage lesson programs, teachers to view their teaching timetables, and students/parents to monitor class schedules in real-time.

## 2. Architecture Principles
- **Thin Controller, Fat Service**: Business logic (including conflict detection for teachers and classrooms) is entirely encapsulated in `ScheduleManagementService` and `LessonPeriodService`.
- **Tenant Isolation**: All models (`LessonSchedule`, `LessonPeriod`) enforce tenant boundary protection via `TenantScoped` traits, ensuring strict `branch_id` isolation.
- **RBAC & Policies**: Access control is governed by permissions (`schedule.view`, `schedule.create`, `schedule.update`, `schedule.delete`) and enforced via `SchedulePolicy`.

## 3. Database Schema

### `lesson_schedules` Table
- `id`: Primary key
- `branch_id`: Foreign key to `branches`
- `academic_term_id`: Foreign key to `academic_terms`
- `classroom_id`: Foreign key to `classrooms`
- `course_id`: Foreign key to `courses`
- `teacher_id`: Foreign key to `teachers`
- `lesson_period_id`: Nullable foreign key to `lesson_periods`
- `day_of_week`: Day name (`Monday`, `Tuesday`, etc.)
- `start_time`: Lesson start time (`HH:MM`)
- `end_time`: Lesson end time (`HH:MM`)
- `room`: Nullable room/classroom identifier
- `status`: Schedule status (`active`, `cancelled`)
- `created_at`, `updated_at`, `deleted_at`

### `lesson_periods` Table
- `id`: Primary key
- `branch_id`: Foreign key to `branches`
- `name`: Slot name (e.g. "1. Ders", "Sabah Etüdü")
- `start_time`: Time slot start
- `end_time`: Time slot end
- `created_at`, `updated_at`

## 4. Conflict Engine Logic
`ScheduleManagementService::checkConflicts()` verifies two critical rules before creating or updating a schedule:
1. **Teacher Conflict**: A teacher cannot be assigned to two overlapping lessons on the same day (`start_time < end_time_2 AND end_time > start_time_2`).
2. **Classroom Conflict**: A classroom cannot have two lessons scheduled at overlapping times on the same day.

## 5. Security & Authorization
- **Admin**: Full CRUD capabilities across all classroom and teacher schedules.
- **Teacher**: Restricted to viewing their own assigned lesson schedules (`teacher_id === $user->teacher->id`).
- **Student**: Restricted to viewing schedules belonging to their classroom (`classroom_id === $user->student->classroom_id`).
- **Parent**: Restricted to viewing schedules belonging to their student's assigned classroom.

## 6. Testing Scope
Feature tests in `tests/Feature/ScheduleManagementTest.php` verify:
- Admin CRUD operations
- Teacher/Student/Parent portal schedule visibility
- Conflict prevention for teacher and classroom overlaps
- Multi-tenant data isolation (`branch_id`)

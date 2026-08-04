# Homework & Assignment Management Architecture

## 1. Overview
The Homework & Assignment Management Module (Sprint 9.5) handles the lifecycle of assignments, from creation by teachers to submission by students, and tracking by parents. It is integrated with the Notification system for real-time alerts and the Guidance Module's Risk Engine (`StudentPerformanceService`) to track low performance and late submissions.

## 2. Core Principles
- **Thin Controller, Fat Service**: All logic (limits, risk calculation, business rules) is encapsulated in `HomeworkManagementService`, `HomeworkSubmissionService`, and `HomeworkReportService`.
- **Tenant Isolation**: `branch_id` is mandatory. The `TenantScoped` global scope ensures no data leaks across branches.
- **Limit Enforcement**: Subscriptions limits (`max_homeworks` and `max_daily_submissions`) are validated in `SubscriptionLimitService` before any database insertion.

## 3. Database Schema
### `homeworks`
- `branch_id` (Tenant)
- `classroom_id`, `course_id`, `teacher_id`
- `title`, `description`, `homework_type`
- `assigned_date`, `due_date`
- `allow_late_submission`, `max_score`
- `attachment_path` (For assignment files)
- `status` (draft, published, closed)

### `homework_submissions`
- `branch_id` (Tenant)
- `homework_id`, `student_id`
- `submitted_at`, `status` (submitted, late, graded)
- `grade`, `teacher_feedback`
- `attachment_path`
- `graded_by`, `graded_at`

### `homework_comments`
- `branch_id`
- `homework_id`, `user_id`
- `comment`

## 4. Workflows

### Homework Creation
1. Teacher submits assignment via `TeacherHomeworkController`.
2. Controller calls `HomeworkManagementService::createHomework()`.
3. Service checks `max_homeworks` limit.
4. If published, `NotificationService` alerts relevant students.

### Homework Submission
1. Student submits assignment via `StudentHomeworkController`.
2. `HomeworkSubmissionService::submitHomework()` is called.
3. Checks `max_daily_submissions` limit.
4. Determines if submission is `late` based on `due_date`.
5. If `late`, it triggers `StudentPerformanceService::updateRiskLevel()` to mark student at medium risk.

### Grading
1. Teacher grades submission via `TeacherHomeworkController`.
2. `HomeworkSubmissionService::gradeSubmission()` is called.
3. If grade is below 40% of `max_score`, `StudentPerformanceService::updateRiskLevel()` marks the student at high risk.
4. Sends `HOMEWORK_GRADED` notification to the student.

## 5. Security & RBAC
- **Roles**: Admin, Teacher, Student, Parent.
- **Permissions**: `homework.view`, `homework.create`, `homework.update`, `homework.delete`, `homework.publish`, `homework.grade`, `homework.submit`, `homework.report`.
- **Policies**: `HomeworkPolicy` and `HomeworkSubmissionPolicy` enforce branch-level and ownership-level constraints (e.g., Teachers only grade their classes, Students only submit their own homework).

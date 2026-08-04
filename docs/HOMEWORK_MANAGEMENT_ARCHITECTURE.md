# Homework Management Architecture

## 1. Overview
The Homework Management Module (Sprint 9.0) enables branches (Dershanes) to create, assign, and manage homework assignments for classrooms. It strictly enforces tenant isolation and integrates with the role-based access control (RBAC) and subscription limit systems.

## 2. Core Principles
- **Thin Controller, Fat Service**: All business logic for homework management and submission grading is inside `HomeworkManagementService` and `HomeworkSubmissionService`.
- **Tenant Isolation**: Every record (`homeworks`, `homework_submissions`, `homework_files`) is tied to a `branch_id`. Global scopes (`TenantScoped`) ensure data does not leak across branches.
- **Subscription Limits**: `SubscriptionLimitService` validates the maximum number of homeworks a branch can create (`max_homeworks` in the Plan's JSON limits).

## 3. Database Schema
### `homeworks`
- Represents a single assignment given to a specific `classroom_id`.
- Stores metadata: `title`, `description`, `due_date`, `max_score`, `allow_late_submission`.
- Statuses: `draft`, `published`, `closed`.

### `homework_submissions`
- Tracks the submission of a single `student_id` for a specific `homework_id`.
- Statuses: `pending`, `submitted`, `late`, `graded`.
- Stores `score` and `feedback` provided by the teacher.

### `homework_files`
- A unified attachment table for both `homeworks` and `homework_submissions` using foreign keys.

## 4. Workflows

### Creating & Publishing
- **Drafts**: Homeworks can be created in draft state by Admins/Teachers.
- **Publishing**: When marked as 'published', a job (or synchronous notification in `NotificationService`) triggers system-wide alerts to Students and Parents.

### Submitting
- Students view published homeworks via `StudentPortalService::getPendingHomeworks`.
- Students upload files, handled by `HomeworkSubmissionService::submitHomework`.
- System automatically determines if it is on-time (`submitted`) or `late` based on the `due_date`.
- Late submissions can be strictly prevented if `allow_late_submission` is false.

### Grading
- Teachers review submissions through `TeacherPortalService::getPendingHomeworkReviews`.
- Teachers grade out of `max_score`, optionally adding feedback.
- Upon grading, `NotificationService` alerts the Student and their Parents.

## 5. Security & Roles
- **Admin**: Has `homework.*` (view, create, update, delete, publish, grade) across the branch.
- **Teacher**: Can manage and grade only homeworks assigned to them (`teacher_id`).
- **Student**: Can view and submit homeworks assigned to their `classroom_id`.
- **Parent**: Can view homework status, grades, and submissions of their linked `student_id`.

## 6. Notification Integration
This module leverages `App\Domain\Notification\Services\NotificationService` to ensure unified communication across all portals (Portal UI + potential email channels in the future).

## 7. Storage
Files are stored securely. Mime types and sizes are logged in `homework_files` to provide validation and cleanup logic. Disks can be configured per tenant or via standard `public`/`s3` disks.

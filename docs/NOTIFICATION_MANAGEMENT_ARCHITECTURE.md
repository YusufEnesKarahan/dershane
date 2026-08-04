# Notification & Communication Center Architecture

## 1. Overview
The **Notification & Communication Center** (Sprint 9.7) provides in-app system notification capabilities across the Dershane SaaS platform. It allows admins and teachers to dispatch notifications to users, while automatically generating event-driven notifications when key domain events occur (homework published, attendance marked absent, exam results submitted, guidance records added).

## 2. Architecture Principles
- **Domain Driven Service Layer**: All notification creation, targeting, bulk dispatching, and mark-as-read status updates are handled by `NotificationService`.
- **Tenant Isolation**: Both `Notification` and `NotificationTemplate` Eloquent models use the `TenantScoped` trait, guaranteeing 100% `branch_id` data isolation.
- **RBAC & Authorization**: Permissions (`notification.view`, `notification.create`, `notification.send`, `notification.manage`) and `NotificationPolicy` enforce fine-grained access control across Admin, Teacher, Student, and Parent roles.

## 3. Database Schema

### `notifications` Table
- `id`: Primary key
- `branch_id`: Foreign key to `branches` (tenant boundary)
- `sender_id`: Nullable foreign key to `users`
- `receiver_id`: User ID receiving the notification
- `receiver_type`: Target portal type (`student`, `teacher`, `parent`, `admin`)
- `type`: Category (`homework`, `attendance`, `exam`, `guidance`, `announcement`, `system`)
- `title`: Short summary heading
- `message`: Detailed notification message
- `read_at`: Nullable timestamp indicating when the user viewed/read the notification
- `created_at`, `updated_at`

### `notification_templates` Table
- `id`: Primary key
- `branch_id`: Foreign key to `branches`
- `name`: Template identifier
- `type`: Category type
- `title_template`: Dynamic template string with variables
- `message_template`: Dynamic body template string
- `created_at`, `updated_at`

## 4. Module Integrations

- **Homework Module**: `HomeworkManagementService::publishHomework()` triggers student and parent notifications when new homework is published.
- **Attendance Module**: `AttendanceManagementService::markStudentAttendance()` triggers parent notification when a student is marked absent (`absent`).
- **Exam Module**: `ExamResultService::submitResult()` triggers student notification when exam results are published.
- **Guidance Module**: `StudentGuidanceService` notifies parents when guidance meetings/records are added.

## 5. Security & Authorization
- **Admin**: Full visibility and management of branch notifications.
- **Teacher**: Can create/send notifications to authorized students.
- **Student**: Can view and mark read only their own notifications.
- **Parent**: Can view and mark read notifications belonging to themselves or connected student accounts.

## 6. Testing Scope
Feature tests in `tests/Feature/NotificationManagementTest.php` cover:
- Admin notification creation & dispatch
- Teacher student notification dispatch
- Student notification inbox visibility
- Parent notification inbox visibility
- Multi-tenant isolation (`branch_id`)
- Mark-as-read functionality
- Automated triggers from Homework, Attendance, and Exam domain services

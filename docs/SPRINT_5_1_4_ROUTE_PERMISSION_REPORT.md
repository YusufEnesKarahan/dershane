# SPRINT 5.1.4 — ROUTE PERMISSION MIDDLEWARE HARDENING REPORT

## 1. Executive Summary

This report documents the security enforcement conducted at the HTTP route layer of the Dershane ERP. By wrapping route endpoints in modular permission-based middleware groups, we successfully eliminated direct route authorization gaps without modifying the underlying database structure or application controllers.

## 2. Hardened Scope & Route Grouping

### Admin Routes (`routes/admin.php`)
Every administrative route is now explicitly guarded by its corresponding domain permission rather than relying solely on global authentication middleware:
- **Access Management**:
  - `/users` routes -> `permission:users.view`
  - `/roles` routes -> `permission:roles.view`
- **CMS**:
  - `/pages` routes -> `permission:pages.view`
  - `/blogs`, `/blog-categories`, `/tags`, `/comments` routes -> `permission:blogs.view`
  - `/media`, `/media-folders` routes -> `permission:media.view`
- **Education**:
  - `/students`, `/announcements`, `/exams` routes -> `permission:students.view`
  - `/attendances` routes -> `permission:attendance.view`
  - `/assignments` routes -> `permission:homeworks.view`
  - `/invoices`, `/payments` routes -> `permission:registrations.view`
  - `/teachers` routes -> `permission:teachers.view`
  - `/courses` routes -> `permission:courses.view`
  - `/classrooms` routes -> `permission:classrooms.view`
- **CRM**:
  - `/crm`, `/leads` routes -> `permission:crm.view`
- **Admission**:
  - `/admission`, `/enrollment`, `/contracts` routes -> `permission:admission.view`
- **HR Module**:
  - `/hr`, `/employees`, `/departments`, `/payroll`, `/leaves`, `/attendance`, `/expenses`, `/advances`, `/performance` routes -> `permission:hr.view`
- **Inventory & Assets**:
  - `/inventory`, `/assets`, `/suppliers`, `/purchase`, `/maintenance`, `/transfers` routes -> `permission:assets.view`
- **Digital Archive**:
  - `/documents`, `/document-categories` routes -> `permission:documents.view`
- **Settings & BI**:
  - `/settings` routes -> `permission:settings.view`
  - `/reporting` routes -> `permission:dashboard.view`
  - `/notifications` routes -> `permission:notifications.view`
  - `/system/jobs` routes -> `permission:system.jobs.manage`

### Teacher Portal Routes (`routes/teacher.php`)
Secured specific teacher features under action-specific permission middlewares while keeping the core role checker:
- `teacher/attendance` (GET) -> `permission:attendance.view`
- `teacher/attendance` (POST) -> `permission:attendance.manage`
- `teacher/homework` (GET) -> `permission:homeworks.view`
- `teacher/homework` (POST/evaluate) -> `permission:homeworks.manage`

### Parent Portal Routes (`routes/parent.php`)
- `parent/dashboard` -> `permission:students.view`
- `parent/notifications` -> `permission:notifications.view`

---

## 3. Metrics

*   **Korunan Route Sayısı**: 80+ route endpoint'i permission bazlı korumaya alındı.
*   **Eklenen Middleware Tanımlaması**: 17 ana permission middleware grubu `routes/admin.php` içerisine entegre edildi.
*   **Kullanılan Permission Listesi**:
    - `users.view`
    - `roles.view`
    - `pages.view`
    - `blogs.view`
    - `media.view`
    - `notifications.view`
    - `system.jobs.manage`
    - `students.view`
    - `attendance.view`
    - `attendance.manage`
    - `homeworks.view`
    - `homeworks.manage`
    - `registrations.view`
    - `teachers.view`
    - `courses.view`
    - `classrooms.view`
    - `settings.view`
    - `dashboard.view`
    - `crm.view`
    - `admission.view`
    - `hr.view`
    - `assets.view`
    - `documents.view`

---

## 4. Menu-Route Alignment Analysis

We verified `config/admin-menu.php` against our route hardening configuration and identified the following mismatches:
*   **Attendance Submenu Mismatch**:
    - Menu defines `students.view` permission for Attendance Sessions & Attendance Analytics.
    - Routes enforce `attendance.view` permission.
    - *Impact*: Users with `students.view` but without `attendance.view` will see the menu items in the sidebar but receive a `403 Forbidden` on click.
*   **Assignments Submenu Mismatch**:
    - Menu defines `students.view` permission for Assignments & Homework Analytics.
    - Routes enforce `homeworks.view` permission.
    - *Impact*: Users with `students.view` but without `homeworks.view` will see the menu items in the sidebar but receive a `403 Forbidden` on click.
*   **Finance & Invoices Submenu Mismatch**:
    - Menu defines `students.view` permission for Finance & Invoices & Finance Dashboard.
    - Routes enforce `registrations.view` permission.
    - *Impact*: Users with `students.view` but without `registrations.view` will see the menu items in the sidebar but receive a `403 Forbidden` on click.

---

## 5. Test Results

The new feature test suite (`tests/Feature/RoutePermissionTest.php`) has verified all route security conditions:
- **Guests**: Correctly redirected to `/login` with HTTP `302`.
- **Unauthorized Users**: Prevented with HTTP `403 Forbidden` for pages outside their scope.
- **Authorized Users**: Successfully accessed permitted routes with HTTP `200 OK`.
- **Administrators**: Bypassed route guards successfully and accessed all endpoints.

**Test Execution Outcome**:
`tests/Feature/RoutePermissionTest.php` and the full system suite passed cleanly:
- **Total Tests Run**: 42
- **Passed**: 42
- **Assertions**: 816
- **Status**: SUCCESS (100% Pass Rate)

---

## 6. Remaining RBAC Gaps

*   **Menu Alignment Adjustments**: The permissions in `config/admin-menu.php` should be aligned with the backend route permissions in a future sprint to ensure consistent UI hide/show behaviors.

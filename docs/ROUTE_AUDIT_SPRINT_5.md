# Sprint 5.0 Route Audit

Audit date: 24 July 2026. The audit uses `php artisan route:list --json`, controller reflection, a static scan of `resources/views` and `app`, and `RouteHealthTest`.

Result: **307 registered routes**, **305 named routes**, **0 duplicate route names**, **0 controller methods missing**, and **0 undefined static route references** (232 references scanned).

## Route registry

Resource rows represent the registered REST endpoints shown in the Method column; custom endpoints are listed individually.

| Route Name | URI | Controller | Method | Status |
|---|---|---|---|---|
| frontend.home | / | Closure | GET | OK |
| frontend.courses.index/show | /kurslar, /kurslar/{slug} | Closure | GET | OK |
| frontend.blogs.index/show | /blog, /blog/{slug} | Closure | GET | OK |
| frontend.pre-register | /on-kayit | Closure | GET | OK |
| frontend.contact | /iletisim | Closure | GET | OK |
| frontend.legal.* | /yasal/* | Closure | GET | OK |
| auth.* | /login, /register, /forgot-password, etc. | Laravel auth controllers | GET/POST | OK |
| admin.dashboard | /admin/dashboard | AdminDashboardController | index | OK |
| admin.users.* | /admin/users | UserController | index/create/store/edit/update/destroy | OK |
| admin.roles.* | /admin/roles | RoleController | REST + clone | OK |
| admin.pages.* | /admin/pages | PageController | index/create/store/edit/update/destroy + workflow | OK |
| admin.media.* | /admin/media | MediaController | index/store/destroy + download | OK |
| admin.blogs.* | /admin/blogs | BlogController | index/create/store/edit/update/destroy + workflow | OK |
| admin.blog-categories.* | /admin/blog/categories | BlogCategoryController | index/store/destroy | OK |
| admin.tags.* / admin.comments.* | /admin/tags, /admin/comments | BlogTagController / BlogCommentController | supported CRUD actions | OK |
| admin.notifications.* | /admin/notifications | NotificationController | dashboard/index/store/read/preferences | OK |
| admin.students.* | /admin/students | StudentController | supported CRUD + transfer/enrollment/analytics | OK |
| admin.attendances.* | /admin/attendances/* | AttendanceSessionController | index/store/take/storeBulk/analytics | OK |
| admin.exams.* | /admin/exams | ExamController / ExamResultController | index/store/results/analytics | OK |
| admin.assignments.* | /admin/assignments | AssignmentController / AssignmentSubmissionController | index/store/submissions/analytics | OK |
| admin.invoices.* | /admin/invoices | InvoiceController | index/store/show/cancel/dashboard | OK |
| admin.announcements.* | /admin/announcements | AnnouncementController | index/store | OK |
| admin.teachers.* | /admin/teachers | TeacherController and supporting controllers | supported CRUD + schedules/performance/analytics | OK |
| admin.courses.* | /admin/courses | CourseController and CourseLevelController | supported CRUD + levels/analytics | OK |
| admin.classrooms.* | /admin/classrooms | ClassroomController and supporting controllers | supported CRUD + calendar/schedules/holidays | OK |
| admin.reporting.* | /admin/reporting/* | ExecutiveDashboardController | dashboard/analytics/reports | OK |
| admin.crm.* / admin.leads.* | /admin/crm/*, /admin/leads | CRM controllers / LeadController | dashboards, pipeline, follow-ups, supported CRUD | OK |
| admin.admission.* | /admin/admission | AdmissionController | index/store/show + workflow | OK |
| admin.contracts.* / admin.enrollment.* | /admin/contracts, /admin/enrollment | ContractController / EnrollmentController | supported actions | OK |
| admin.hr.* | /admin/hr/* | HR analytics controllers | dashboard/analytics | OK |
| admin.departments.* / admin.employees.* | /admin/departments, /admin/employees | DepartmentController / EmployeeController | supported CRUD | OK |
| admin.payroll.* / admin.leaves.* | /admin/payroll, /admin/leaves | PayrollController / LeaveController | supported CRUD + workflow | OK |
| admin.attendance.* | /admin/attendance | EmployeeAttendanceController | index/store | OK |
| admin.expenses.* / admin.advances.* | /admin/expenses, /admin/advances | ExpenseController / AdvanceController | supported CRUD + workflow | OK |
| admin.inventory.* / admin.assets.* | /admin/inventory, /admin/assets | InventoryController / AssetController | supported CRUD + operations | OK |
| admin.inventory.categories.* | /admin/inventory/categories | AssetCategoryController | index/store/storeLocation | OK |
| admin.suppliers.* / admin.purchase.* | /admin/suppliers, /admin/purchase | SupplierController / PurchaseController | supported CRUD + workflow | OK |
| admin.maintenance.* / admin.transfers.* | /admin/maintenance, /admin/transfers | MaintenanceController / TransferController | supported CRUD + workflow | OK |
| admin.documents.* | /admin/documents | DocumentController / DocumentSearchController | supported CRUD + search/version/share/download | OK |
| admin.document-categories.* | /admin/document-categories | DocumentCategoryController | index/store/update/destroy | OK |
| admin.system.jobs.* | /admin/system/jobs/* | SystemJobController | dashboard/failed/history/automation | OK |
| teacher.* | /teacher/* | Teacher portal controllers | dashboard/classes/attendance/homework/analytics | OK |
| parent.* | /parent/* | Parent portal controllers | dashboard/notifications | OK |

## Fixed findings

| Finding | Resolution |
|---|---|
| Admin/auth routes were loaded twice. | Removed the duplicate `then` route groups from `bootstrap/app.php`; `routes/web.php` remains the sole registry. |
| Resource routes advertised unsupported controller methods. | Limited every resource declaration to implemented actions or removed only incomplete placeholder entries. |
| Blog and inventory categories shared `/admin/categories`. | Separated them into `/admin/blog/categories` and `/admin/inventory/categories`, including menu, forms, and redirects. |
| Education attendance routes targeted the HR attendance controller. | Routed them to `AttendanceSessionController` and implemented the existing domain-backed take, bulk-save, and analytics actions. |
| Teacher analytics had no model parameter. | Added the `{teacher}` binding and typed controller parameter. |
| Several Blade route names and a missing table component were invalid. | Corrected frontend/admin route names and used the existing `x-admin.table.layout` component. |
| A repository interface was not registered. | Bound `ContactMessageRepositoryInterface` to `ContactMessageRepository`. |
| Placeholder or misleading menu entries were exposed. | Removed Permissions, Contacts, Branches, Teacher Performance resource entry, and the unrelated asset-assignment shortcut. |

## Removed routes

- Placeholder routes: `admin.permissions.index`, `admin.contacts.index`, `admin.branches.index`, `admin.logs.index`.
- Unsupported resource endpoints removed by action restriction, including unimplemented `show`, `create`, `edit`, `update`, and `destroy` actions where their controllers did not support them.

The removal is intentional: incomplete features no longer surface a broken URL or a misleading admin-menu entry.

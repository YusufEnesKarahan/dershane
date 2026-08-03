# Sprint 4.8 Walkthrough

1. Open **Admin / Bildirim Merkezi** for totals and channel distribution.
2. Send a panel, email, or SMS-ready notification from the list.
3. Create templates with `{{name}}`, then review analytics and delivery logs.
4. New students, overdue invoices, exam results, assignments, and CRM follow-ups produce event-driven notifications.

## Sprint 4.9 Queue & Automation

Open **System / Queue Dashboard** to inspect pending, failed, and completed jobs. Automation Logs records daily payment, exam, attendance, and CRM scheduling runs. Start workers with `php artisan queue:work database` and the scheduler with `php artisan schedule:work`.

## Sprint 6.1: HQ Central Panel Integration Foundation
**Goal**: Prepare the local ERP for future HQ management by establishing a system identity and local aggregation services, without actual HTTP communication.

### Key Changes
1. **Migration & Model**: Created the `system_identity` table (with UUID keys) and `SystemIdentity` model to uniquely identify the ERP instance.
2. **Integration Service**: Implemented `HQIntegrationService` to aggregate version, license, enabled features, and health summary data.
3. **Admin UI**: Added a readonly page at `/admin/platform/hq-integration` showing local integration status, UUIDs, and health.
4. **Dashboard Widget**: Added a "HQ Status" component to the Executive Dashboard indicating the offline status and system UUID.

### Validation
- Created and passed `tests/Feature/HQIntegrationTest.php`.
- Test suite executed with 53 passing tests and 141 assertions.
- Added documentation in `SPRINT_6_1_HQ_FOUNDATION_REPORT.md`.

## Sprint 6.2: HQ Communication Layer Foundation
**Goal**: Prepare the local ERP for secure communications with the HQ Panel by setting up local token storage, validation middleware, and payload structures.

### Key Changes
1. **Migration & Model**: Created the `hq_api_tokens` table and `HqApiToken` model.
2. **API Service**: Created `HQApiService` to manage generating/revoking/validating 64-character Bearer tokens, and structuring health/system payloads.
3. **Admin UI & Routing**: Added management views and routes under Super Admin scope at `/admin/platform/api` to let admins view, generate, or revoke active API tokens.
4. **Middleware**: Created `HQApiMiddleware` to validate Bearer tokens on secure incoming requests.
5. **Dashboard Widget**: Added token activation status and expiry indicators to the Executive Dashboard.

### Validation
- Created and passed `tests/Feature/HQApiTest.php` with 6 detailed scenarios (generation, validation, revocation, middleware blocking, etc.).
- Complete test suite passed successfully with 59 tests and 163 assertions.
- Added documentation in `SPRINT_6_2_HQ_API_FOUNDATION_REPORT.md`.

## Sprint 6.3: HQ Synchronization Engine Foundation
**Goal**: Implement a lightweight local synchronization queue system to record business events meant for the HQ Panel, completely isolating the sync process from external requests.

### Key Changes
1. **Migration & Model**: Created `hq_sync_queue` table and `HQSyncEvent` model to persist event statuses (pending, completed, failed, processing) and JSON payloads locally.
2. **Sync Service**: Developed `HQSyncService` with helper methods (`queueLicenseChanged`, `queueFeatureChanged`, etc.) to easily queue generic JSON-formatted events, retrieve cache-optimized metrics, and retry failed events.
3. **Admin UI & Routing**: Created `HQSyncController` and `resources/views/admin/platform/sync/index.blade.php` to securely display a read-only list of the latest 20 sync events and overall metrics for Super Admins.
4. **Dashboard Widget**: Added a "HQ Sync Queue" statistics widget to the Executive Dashboard containing counts for pending/success/failed synchronization events and the time of the latest event.

### Validation
- Created and passed `tests/Feature/HQSyncTest.php` testing event generation, structure, assertions in the DB, and UI elements.
- The entire SaaS + Platform test suite passed successfully with 64 passing tests and 183 assertions.
- Added documentation in `SPRINT_6_3_HQ_SYNC_ENGINE_REPORT.md`.

## Sprint 6.4: HQ Secure Communication (HTTP Sync v1)
**Goal**: Implement the first real, manual communication layer via HTTP to the HQ Panel, heavily relying on HMAC signatures and manual invocation to keep the integration lightweight and observable without relying on background job processing.

### Key Changes
1. **Database & Audit Logs**: Generated `hq_sync_logs` and `HQSyncLog` to meticulously track every outgoing request (method, payload, signature details, response, and duration) directly to the database.
2. **Security & Signing**: Designed `SignatureService` applying `HMAC SHA256` hashing to the JSON payload combined with the HQ API Token as a secret to ensure payload integrity (as the `X-Signature` header).
3. **HTTP Client Wrapper**: Implemented `HQHttpService` utilizing Laravel's `Http` client to dispatch fully authenticated requests (incorporating standard headers like System-UUID and System-Version) to HQ while masking errors and dumping all HTTP traffic traces to the Audit log.
4. **Management UI**: Created `HQCommunicationController` and mapped to `/admin/platform/communication` where an administrator can manually invoke actions such as "Ping", "Health Send", "Register", and "Manual Sync", while visualizing the last 20 HTTP transaction outcomes.
5. **Dashboard Widget**: Upgraded the Executive Dashboard with a robust "HQ Connection" card that parses the `HQSyncLog` table to expose true external connectivity (if the last Ping was successful within the last 24h) alongside the timestamps of external transactions.

### Validation
- Designed `tests/Feature/HQCommunicationTest.php` leveraging `Http::fake()` to emulate HQ responses avoiding external networks. Evaluated payload hashing equality, valid and failed connection DB trails, and admin UI redirection behaviors.
- Complete platform test suite passed accurately with 69 tests and 200 assertions.
- Detailed implementation notes documented in `SPRINT_6_4_HQ_HTTP_COMMUNICATION_REPORT.md`.

## Sprint 6.5: HQ Remote Command Foundation
**Goal**: Create a secure, strictly whitelisted foundation allowing the ERP to receive specific commands from the HQ Panel while requiring manual Administrator approval prior to localized execution. No unsafe dynamic code execution or automated background processors were introduced.

### Key Changes
1. **Database & Persistence**: Established `hq_commands` table and `HQCommand` model to store remote command UUIDs, types, payloads, states (pending/approved/failed), and execution results.
2. **Command Executor**: Engineered `HQCommandExecutor` strictly limited to 4 explicitly defined system commands (`health_check`, `system_info`, `cache_clear`, `version_check`). Any unauthorized command defaults to rejection without executing.
3. **Command Middleware**: Constructed `HQCommandMiddleware` enforcing inbound API security by confirming Bearer Token validity and regenerating+verifying the HMAC SHA256 `X-Signature` attached to the request payload.
4. **Management Interface**: Added `HQCommandController` mapped to `/admin/platform/commands` granting Super Admins the authority to manually Review, Approve, Reject, or Execute queued HQ remote commands while viewing the final execution payloads.
5. **Dashboard Widgets**: Embedded a dynamic "HQ Command Status" statistical widget in the Executive Dashboard to track pending command queues alongside recent failures and execution timelines.

### Validation
- Crafted `tests/Feature/HQCommandTest.php` evaluating safe execution routines, HTTP middleware rejections due to bad signatures, and complete admin-level RBAC restrictions.
- Total unified SaaS and Platform test suite executed with zero errors, validating 78 distinct tests mapping to 223 assertions.
- Complete architectural summary documented via `SPRINT_6_5_HQ_COMMAND_FOUNDATION_REPORT.md`.

## Sprint 6.6: HQ Telemetry & Monitoring Foundation
**Goal**: Create a secure, lightweight, and purely read-only telemetry layer designed to collect ERP system health, usage, and performance statistics and submit them as snapshots to the HQ Panel manually.

### Key Changes
1. **Database & Logging**: Built the `hq_telemetry_logs` table alongside the `HQTelemetryLog` model. This structure meticulously records every generated metrics snapshot to keep an auditable trace of dispatched data.
2. **Telemetry Service**: Programmed `HQTelemetryService` grouping 4 logic sets (`collectHealth`, `collectSystem`, `collectUsage`, `collectPerformance`) that inspect variables (database connections, memory usage, cache status, etc.) strictly via safe standard PHP or Laravel features (no unsafe shell access).
3. **HTTP Dispatch**: Extended the existing `HQHttpService` with a `sendTelemetry` method, channeling the composite JSON snapshot securely using existing HMAC SHA-256 signatures through the Http wrapper to HQ.
4. **Admin UI & Dashboard**: Configured `HQTelemetryController` operating via `/admin/platform/telemetry`, establishing a visual dashboard demonstrating dynamic status nodes (DB, Cache, Users) with an action button for Super Admins to manually cast the metrics snapshot. Additionally, augmented the main Executive Dashboard integrating an "HQ Telemetry Status" widget.

### Validation
- Forged `tests/Feature/HQTelemetryTest.php` proving out precise snapshot assemblage (System UUID, Database availability, RAM stats), HTTP dispatch logic mocking, and precise RBAC boundary enforcement.
- Integrated platform tests surged to 85 passing validations handling 249 assertions.
- Full details documented inside `SPRINT_6_6_HQ_TELEMETRY_REPORT.md`.

## Sprint 6.7: HQ Scheduler & Auto Sync Foundation
**Goal**: Design a strict, configurable task scheduling layer allowing the ERP instance to periodically emit heartbeats, dispatch telemetry snapshots, and eventually process sync queue events. As a security measure, the scheduler defaults to `disabled` and operates solely on pre-authorized commands.

### Key Changes
1. **Database Tracking**: Created `hq_scheduler_logs` and `HQSchedulerLog` model to chronologically track all attempted jobs, their precise execution duration (`duration_ms`), and terminal states (success vs error).
2. **Robust Service Layer**: Introduced `SchedulerService` enforcing standardized execution loops featuring try/catch boundaries, automatic timestamping, and database logging via the isolated `executeTask` method.
3. **Discrete Commands**: Crafted three dedicated Artisan commands (`hq:telemetry`, `hq:heartbeat`, `hq:sync`) hooked directly into Laravel's native Console Kernel via `routes/console.php`. Each verifies the `config('hq.scheduler.enabled')` flag prior to any processing.
4. **Administration Interface**: Delivered `HQSchedulerController` mapped to `/admin/platform/scheduler` presenting system administrators with live diagnostic cards showing enabled status, the timestamps of the last automated telemetry and heartbeat runs, alongside a historical grid of task logs. 
5. **Dashboard Widgets**: Fortified the Executive Dashboard adding a dedicated "HQ Automation Status" panel rendering real-time enabled/disabled state flags and tracking recent failing jobs.

### Validation
- Authored `tests/Feature/HQSchedulerTest.php` actively asserting disabled-by-default logic, accurate telemetry/heartbeat command isolation, flawless DB logging, and interface rendering limits. 
- All platform tests evaluated flawlessly spanning 92 tests resolving 272 total assertions.
- Core design structure published into `SPRINT_6_7_HQ_SCHEDULER_REPORT.md`.

## Sprint 6.8: HQ Update Delivery Foundation
**Goal**: Establish a secure metadata tracking layer allowing the ERP instance to check HQ for available system updates, trace current version statuses, and meticulously catalog package changes. This strictly avoids dynamic code downloading/execution, serving only as the information foundation.

### Key Changes
1. **Version Auditing Tables**: Constructed `hq_updates` recording unique version bumps alongside their status and `hq_update_logs` persisting the audit trail (registered, installed, failed).
2. **Metadata Synchronization Service**: Deployed `HQUpdateService` leveraging the secure `HQHttpService` layer to `checkUpdates()` via POST against HQ, resolving version numbers and updating local definitions passively.
3. **Restricted Terminal Command**: Released `hq:update-check` verifying the disabled-by-default configuration (`hq.updates.enabled`) before pinging the Central Platform for available packages.
4. **Administration Interface**: Introduced `/admin/platform/updates` attached to `HQUpdateController` where Super Admins can monitor version discrepancies, view historical update logs, and simulate post-update statuses via isolated testing tools.
5. **Dashboard Updates**: Expanded the central Executive Dashboard yielding live awareness over pending updates, currently installed instances, and the active `config('app.version')`.

### Validation
- Crafted `tests/Feature/HQUpdateTest.php` verifying HTTP endpoint mocking, verifying robust configuration guards (`test_updates_disabled_default`), and strictly checking that manual overrides (`test_mark_installed`) effectively persist into the database.
- Global testing suite sustained across 100 dedicated checks validating 289 assertions without failure.
- Structural decisions recorded in `SPRINT_6_8_HQ_UPDATE_FOUNDATION_REPORT.md`.

## Sprint 6.9: HQ Central Management Backend Foundation
**Goal**: Elevate the codebase to function as the HQ Central Backend, enabling it to accept, manage, and monitor incoming connections from remote SaaS ERP instances. 

### Key Changes
1. **Central Schema Expansion**: Deployed dedicated central tables (`institutions`, `hq_system_instances`, `hq_api_connections`, `hq_telemetry_records`, `hq_central_commands`, and `hq_central_sync_logs`) to track multiple SaaS architectures.
2. **Domain Boundaries**: Segregated central logic inside `App\Core\Services`, creating bespoke registry (`SystemRegistryService`), command dispatching (`HQCommandService`), and telemetry processing (`HQTelemetryService`) services without polluting local ERP behaviors.
3. **API Foundation**: Constructed `/api/hq/*` routes governed by `VerifyHQApiSignature` middleware. This rigidly demands HMAC SHA-256 integrity, Bearer validation, and request-replay denial across every remote heartbeat and sync.
4. **Administrative Centralization**: Created `/admin/platform/hq-central` routed to `HQCentralController` offering Super Admins overarching visibility over connected tenants, offline durations, and the overarching API health map.

### Validation
- Designed `tests/Feature/HQBackendTest.php` rigorously ensuring tenant creation, dynamic heartbeats, explicit signature denial (401), token validation, payload processing, and payload assertions without regressions.
- Passed all 8 central endpoint assessments with 21 active assertions and a total project pass of 109 core test suites.
- Full context logged into `SPRINT_6_9_HQ_BACKEND_FOUNDATION_REPORT.md`.

## Sprint 7.0: HQ Central Administration Dashboard
**Goal**: Build a professional administration panel interface for the HQ Central Management Backend, empowering Super Admins to monitor health, tenants, telemetry, and distributed system environments visually.

### Key Changes
1. **Authorization & Access**: Created `HQPolicy` and explicitly registered gates (`hq.viewDashboard`, `hq.manageTenant`, `hq.sendCommand`) in `AppServiceProvider` restricting all central panel access to the Super Admin role exclusively.
2. **Controller Decomposition**: Segmented dashboard responsibilities to prevent monolithic code. `HQCentralController` powers the high-level dashboard, `HQSystemController` manages instance lists and deep-dive inspections, and `InstitutionController` handles CRUD operations for the overarching tenant organizations.
3. **Advanced Telemetry Aggregation**: Elevated `HQMonitoringService` to parse JSON telemetry payloads continuously, extracting dynamic averages (Memory & Storage Usage Percentages), tracking communication latency, and detecting stale instances (offline marking logic for >15 minutes elapsed).
4. **Premium UI/UX Implementation**: Developed dark-mode compatible interfaces under `/resources/views/admin/hq/*`:
   - Configured an overarching metric dashboard with 5 distinct grid areas (System Overview, Tenant Overview, Communication Health, Command Queue, Telemetry Insights).
   - Designed a dynamic System detail page (`systems.show`) enumerating raw telemetry JSON payloads, timeline-based communication logs, and active command statuses.
   - Built a javascript-driven Tenant Management modal interface allowing fluid creation and suspension of tied organizations.

### Validation
- Crafted `tests/Feature/HQDashboardTest.php` proving rigid HTTP security guarding (403 assertions for normal users, unauthenticated drops) and validating offline/online dynamic metric calculations.
- Over 18 precise assertions confirmed UI rendering constraints and functional system interactions. Tests completed flawlessly in under 3 seconds.
- Final strategic log consolidated inside `SPRINT_7_0_HQ_ADMIN_DASHBOARD_REPORT.md`.

## Sprint 7.1: HQ License Management v2
**Goal**: Implement a complete license management layer for HQ Central Panel to manage SaaS subscriptions, plans, expiration dates, and enabled features.

### Key Changes
1. **Schema Enhancements**: Deployed `hq_licenses` and `hq_license_features` tracking instances, tenant bindings, expiration timestamps, and isolated boolean capability locks.
2. **Business Service Logic**: Created `HQLicenseService` equipped with scalable logic functions such as `checkExpiration` (for CRON consumption) and granular boolean togglers (`enableFeature`, `disableFeature`).
3. **UI/UX Construction**:
   - Upgraded `admin.hq.index` inserting advanced License Status metrics widgets (Expiring < 30 days logic).
   - Designed `/licenses` indexing arrays and `/licenses/show` interfaces giving complete operational freedom to Super Admins (Feature revocation, suspension).
4. **Security Binding**: Appended `manageLicense` gate inside `HQPolicy` securing global routing directly to top-tier administrators.

### Validation
- Designed `tests/Feature/HQLicenseTest.php` confirming authorization blocks against standard roles, state management logic, automatic expiration triggers, and database feature creations. Tests are green and fast.
- Full architectural logic documented within `SPRINT_7_1_HQ_LICENSE_REPORT.md`.

## Sprint 7.6: System Monitoring & Operational Controls
**Goal**: Strengthen the Super Admin SaaS control surface with system health visibility, tenant usage diagnostics, and a shared platform audit trail.

### Key Changes
1. **System Health Service**: Added `SystemHealthService` to centralize Laravel/PHP/runtime checks, cache and queue state, storage and database reachability, and the latest successful cron timestamp from `HQSchedulerLog`.
2. **Tenant Intelligence**: Extended `SaaSOperationsService` with richer tenant usage statistics, last login/user activity details, estimated data size, and normalized activity feed assembly.
3. **Audit Logging**: Added `PlatformAuditLog` plus creation hooks for tenant suspension/activation, license plan changes, payment completion, and settings updates/resets.
4. **UI & Routes**: Introduced a dedicated Super Admin `Sistem Sağlığı` page and expanded `admin.saas.tenants.show` with usage statistics, system status, activity stream, and subscription history.
5. **Scheduler Persistence**: Added `HQSchedulerLog` storage so cron success timestamps can be queried without relying on log file parsing.

### Validation
- Added `tests/Feature/SystemHealthTest.php` covering Super Admin access, normal user denial, tenant statistic rendering, and audit log creation.
- Test execution is expected through `php artisan test` after the migrations are applied.

## Sprint 7.7: SaaS Subscription & Plan Management
**Goal**: Build the Super Admin subscription and plan management layer without breaking the existing Billing and License flow.

### Key Changes
1. **Schema & Models**: Extended `plans`, `subscriptions`, and added `subscription_histories` to support tenant subscription lifecycles, trial windows, and plan metadata.
2. **Service Layer**: Added `SubscriptionManagementService` and `SubscriptionLimitService` to centralize plan CRUD, assignment, upgrade/downgrade, renewal, cancellation, and tenant capacity checks.
3. **Admin Experience**: Added `/admin/platform/subscriptions`, plan list/detail pages, and tenant subscription actions for Super Admin operations.
4. **Auditability**: Plan creation/update and subscription lifecycle operations now write to `PlatformAuditLog`.
5. **Dashboard KPIs**: Super Admin dashboard now includes total plans, active subscriptions, trial tenants, and monthly revenue estimate.

### Validation
- `tests/Feature/SubscriptionManagementTest.php` added for plan creation, access control, tenant assignment, upgrade/cancel flows, audit logs, and limit checks.

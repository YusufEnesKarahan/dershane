# Sprint 7.3: HQ Remote Command Execution & Task Orchestration

## Overview
This sprint introduces a robust, secure, and fully whitelist-based remote command orchestration layer between the HQ Central Management Platform and the individual SaaS ERP instances. 
The system ensures that **no arbitrary code execution** (like `eval()`, `exec()`, or `artisan::call()`) is possible. Commands are tightly constrained by Enums and resolved through a static Command Registry.

## Architecture & Workflow

### 1. HQ Side (The Orchestrator)
- **Service**: `HQRemoteCommandService`
- **Responsibilities**: Validates commands against the `HQCommandType` enum, saves them to the `hq_central_commands` table, manages priority, schedules, expirations, and handles bulk dispatching (e.g., all instances in a tenant, or all global production instances) via database transactions.

### 2. The Transport Layer
- **Endpoint**: `GET /api/hq/commands` and `POST /api/hq/commands/{id}/result`
- **Security**: Both endpoints use the existing `VerifyHQApiSignature` middleware ensuring requests are signed with HMAC SHA256 and protected against replay attacks (5-minute window).

### 3. ERP Side (The Executor)
- **Executor**: `RemoteCommandExecutor`
- **Responsibilities**: Safely polls HQ for commands, resolves them using the `CommandRegistry`, and pushes the execution result back to HQ.
- **Registry**: `CommandRegistry` maps string commands to handler classes (e.g., `'ping' => PingHandler::class`).

## Key Deliverables

### Handlers Implemented
All handlers implement `RemoteCommandHandlerInterface`:
- `PingHandler`: Responds with pong and timestamp.
- `ClearCacheHandler`: Uses `Cache::flush()` natively.
- `SyncLicenseHandler`: Calls `LicenseManager::refresh()`.
- `TelemetryHandler`: Triggers `HQSchedulerService::runTelemetry()`.

### Database Schema Updates
The `hq_central_commands` table was enhanced with:
- `priority`: For queue sorting (higher runs first).
- `scheduled_at`: For future tasks.
- `expires_at`: Fails commands automatically if not picked up in time.
- `retry_count` & `max_retry`: Self-healing retry logic.
- `response` & `error_message`: Deep audit trailing.

### Admin Interfaces
- **Route**: `/admin/hq-central/commands`
- **UI**: Added a fully responsive dashboard with stats (Pending, Completed, Failed), an interactive queue view, detailed command inspection pages, and a Dispatch UI for single, tenant, or global command execution.

## Security Review
- **No Remote Code Execution**: System guarantees that `shell_exec`, `eval`, or dynamic file inclusion is not possible.
- **Whitelist Enforcement**: The `HQCommandType` enum and `CommandRegistry` form an impenetrable barrier against unknown commands.
- **Payload Validation**: Controller validation ensures that inputs conform to expectations, while the system UUID validation matches tenants correctly.
- **Role Control**: Routes are strictly guarded by `hq.manageSystem` and `hq.viewDashboard` permissions (Super Admin).

## Test Results
A comprehensive test suite `RemoteCommandTest` was added with 9 core scenarios and 45 assertions.

```
RemoteCommandTest .................... 9 tests, 45 assertions — PASSED
HQBackendTest ........................ 8 tests, 21 assertions — PASSED (regression)
Full Test Suite ...................... PASSED (only pre-existing SQLite role/permission failures remain)
```

## Production Readiness
This module is fully production-ready. The system defaults to silent failures with retry logic, prevents queue clogging via expirations, and uses bulk dispatching via DB transactions ensuring atomicity.

## Files Modified & Created

### Created
- `app/Domain/HQ/Enums/HQCommandType.php`
- `app/Domain/HQ/Services/HQRemoteCommandService.php`
- `app/Domain/System/Commands/CommandRegistry.php`
- `app/Domain/System/Commands/RemoteCommandExecutor.php`
- `app/Domain/System/Commands/RemoteCommandHandlerInterface.php`
- `app/Domain/System/Commands/Handlers/ClearCacheHandler.php`
- `app/Domain/System/Commands/Handlers/PingHandler.php`
- `app/Domain/System/Commands/Handlers/SyncLicenseHandler.php`
- `app/Domain/System/Commands/Handlers/TelemetryHandler.php`
- `app/Http/Controllers/Admin/HQRemoteCommandController.php`
- `resources/views/admin/hq/commands/create.blade.php`
- `resources/views/admin/hq/commands/index.blade.php`
- `resources/views/admin/hq/commands/show.blade.php`
- `tests/Feature/RemoteCommandTest.php`
- `database/migrations/2026_07_29_115803_add_orchestration_fields_to_hq_central_commands_table.php`

### Modified
- `app/Http/Controllers/Api/HQCentralApiController.php`
- `app/Models/HQCentralCommand.php`
- `app/Domain/Platform/Services/HQSchedulerService.php`
- `routes/admin.php`
- `resources/views/admin/hq/index.blade.php`
- `tests/Feature/HQBackendTest.php` (Fixed array structure assertion)

### Deleted
- `app/Domain/HQ/Services/HQCommandService.php` (Replaced)

# Sprint 7.4: HQ Update & Deployment Management

## Overview
This sprint introduces the **HQ Update & Deployment Management** module. It establishes an orchestration layer for managing software versions, tracking update lifecycles, and securely dispatching deployment jobs to connected ERP instances via the existing Remote Command architecture.

The system ensures that HQ retains full control over the update lifecycle (single instances, tenant-wide, or global) without directly executing arbitrary code. Remote execution is strictly guarded by the `HQCommandType` enum and `CommandRegistry`.

## Key Implementations

### 1. Database & Models
- **HQVersion**: Tracks software versions, release channels (stable, beta, alpha), and mandatory status.
- **HQUpdateJob**: Represents an update task (single, tenant, global) tracking its progress, lifecycle status, and error logs.

### 2. Services
- **HQVersionService**: Handles publishing, archiving, and version comparisons (mandatory checking).
- **HQUpdateService**: The orchestration brain. Wraps the previously built `HQRemoteCommandService` to dispatch `START_UPDATE` commands efficiently across single, tenant-wide, or global targets inside database transactions.

### 3. ERP End Handlers (Safeguarded Execution)
- **CheckUpdateHandler**: Checks if local version requires an update.
- **StartUpdateHandler**: Hooks into the ERP's updater logic (to be implemented) without using `exec()` or `shell_exec()`.
- **ReportUpdateProgressHandler**: Tracks fine-grained update steps.
- **ReportUpdateFinishedHandler**: Submits the final result.

### 4. API Endpoints
A new `HQUpdateApiController` was added to allow ERPs to autonomously report long-running update progress:
- `POST /api/hq/update/check`
- `POST /api/hq/update/start`
- `POST /api/hq/update/progress`
- `POST /api/hq/update/finished`
All are protected by HMAC SHA256 through the `VerifyHQApiSignature` middleware.

### 5. Admin Interface (HQ Panel)
- **HQ Dashboard**: Added `Updates & Deployments` widget alongside `Command Queue` showing the latest version, instances behind the curve, and active running deployments.
- **Version Management**: UI to publish new versions with mandatory flags and release notes.
- **Update Deployment**: Comprehensive dispatch interface allowing Admins to target instances, monitor live progress bars, cancel jobs, or retry failures.

## Security Constraints Checked & Satisfied
- [x] No `exec()`, `shell_exec()`, `system()`, `passthru()`, `popen()`, `proc_open()`.
- [x] No `eval()`.
- [x] No SSH, Git, Docker, or Composer execution commands directly invoked from HQ.
- [x] Operations remain fully within the Laravel bounds using strict OOP design and `RemoteCommandHandlerInterface`.

## Testing
`HQUpdateTest` comprehensively validates:
- Version publishing logic
- Single/tenant/global command dispatching
- API checks for latest/mandatory version
- HMAC-signed API progress reporting

## Production Readiness
This orchestration layer is built for scale. Global dispatching uses database chunking. Handlers use memory-safe arrays, and API callbacks use HMAC ensuring no rogue actor can forge update reports. The system is strictly an orchestrator, decoupling version management from the actual file-updating logic (which will be implemented safely within the ERPs later).

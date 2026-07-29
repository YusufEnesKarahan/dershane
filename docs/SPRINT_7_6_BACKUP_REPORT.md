# Sprint 7.6: HQ Backup & Disaster Recovery Management

## Overview
This sprint introduces the **HQ Backup & Disaster Recovery Management** module. The central goal was to empower HQ Central to orchestrate, monitor, and manage backups across thousands of connected ERP SaaS instances securely.

## Key Implementations

### 1. Database & Models
- **HQBackupPolicy**: Defines target systems (Global, Tenant, Instance), backup frequencies (`daily`, `weekly`, `monthly`), data types (`database`, `files`, `full`), and retention logic.
- **HQBackupJob**: Represents individual backup tasks dispatched based on active policies. Includes robust tracking for file size, remote storage location, logs, and error messaging.
- **HQBackupLog**: Tracks execution timelines (e.g. `dispatch`, `retry`) for full auditability.
- **BackupCache**: Local ERP database table to keep the instance independently aware of its backup status even if HQ connectivity fails.

### 2. Services & Architecture
- **HQBackupService**: Centralized backend orchestrator handling policy creation, job dispatch logic, retry capability for failed commands, and automatic retention cleanup.
- **HQSchedulerService**: Augmented to process Backup Health Checks, triggering the retry logic for failed jobs and retaining policy lifecycle bounds.

### 3. Remote Command Handlers
In adherence to the strict "Zero RCE" mandate, direct shell manipulations (like `exec()` or `.env` rewrites) were forbidden. We extended `CommandRegistry` with 5 predefined safe commands:
- `BACKUP_CHECK`
- `BACKUP_START`
- `BACKUP_PROGRESS`
- `BACKUP_FINISHED`
- `BACKUP_RESTORE`
The remote handlers translate these HQ instructions into verified local functions. 

### 4. Backup APIs
Endpoints to securely receive backup callbacks from the ERPs:
- `POST /api/hq/backup/check`
- `POST /api/hq/backup/start`
- `POST /api/hq/backup/progress`
- `POST /api/hq/backup/finished`
All are rigorously protected by the existing `VerifyHQApiSignature` middleware utilizing HMAC SHA-256 for replay and tampering prevention.

### 5. UI and Dashboards
- Developed the Backup Management Dashboard under `/admin/platform/hq-central/backups`.
- Added comprehensive views for managing active Backup Policies and viewing Backup Jobs with detailed historical logs.
- Added a Backup Health Widget directly onto the main HQ Dashboard, presenting global success/failure metrics and aggregate storage usage at a glance.

### 6. Security
- Enforced new system-wide permission: `hq.manageBackup`.
- Validated via `Super Admin` tests.
- Re-used secure HMAC handshakes across the network gap.

## Testing & Stability
- Developed `HQBackupTest` simulating policy logic, unauthorized role rejection, proper handler execution mapping, API progression checks, and retention deletion logic. 
- Integrated seamlessly without breaking legacy tests from Sprints 6.9 - 7.5.

## Production Readiness
**Status: READY**
The Disaster Recovery orchestration layer brings the system into enterprise compliance levels. The platform can now safely and reliably manage wide-scale backup pipelines with full visibility.

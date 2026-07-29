# Sprint 7.5: HQ Remote Configuration Management

## Overview
This sprint introduces the **HQ Remote Configuration Management** module. It establishes HQ Central as the definitive source of truth for remote configuration across all ERP instances. By leveraging the existing HMAC-authenticated APIs and the Command Registry from previous sprints, the module enables dynamic, centralized configuration pushes without requiring direct code execution (`exec`, `eval`) or `.env` modifications.

## Key Implementations

### 1. Database & Models
- **HQConfigurationProfile**: Represents a configuration block scoped globally, per tenant, or per instance.
- **HQConfigurationItem**: Stores the actual key-value pairs (e.g. `SMTP_PASSWORD`). Supports typed data mapping, and automatically utilizes AES-256-CBC Laravel Encryption for items marked `is_sensitive`.
- **HQConfigurationVersion**: Captures immutable snapshots of configurations across their lifecycle, allowing safe rollbacks.
- **HQConfigurationLog**: Tracks all creations, updates, rollbacks, and remote synchronization attempts.
- **ConfigurationCache**: Local offline cache implemented on the ERP side to ensure operation resilience if HQ goes down.

### 2. Services & Architecture
- **HQConfigurationService**: Orchestrates creating profiles, managing configuration keys, snapshotting versions, and performing rollbacks gracefully.
- **ConfigurationSynchronizationService (ERP)**: Handles making strict, HMAC SHA256-signed requests to HQ Central to download active configuration properties. Keys explicitly requested are then cached locally.
- **Command Handlers**: 
  - `SYNC_CONFIGURATION` (Triggers remote sync request).
  - `CLEAR_CONFIGURATION_CACHE` (Forces the ERP to purge and re-fetch config).

### 3. User Interface (HQ Central)
- Built an extensive UI to manage profiles across different targets.
- Configuration Items are represented in a table, displaying types prominently (boolean, json, encrypted) and physically masking sensitive fields inside the table (`*************`).
- A visually rich **Version History & Rollback** timeline allows administrators to view a diff of past JSON snapshots and execute a 1-click rollback, which safely preserves audit trails by generating a new forward-moving snapshot rather than erasing past history.

### 4. Security Highlights
- **Zero RCE**: Handlers explicitly perform standard database cache operations. They do NOT touch `.env`, use `exec()`, or execute arbitrary strings.
- **Cryptography**: Sensitive configurations are encrypted at rest in the central database using `Crypt::encryptString`. The encrypted string remains secure unless explicitly handled by the application logic during serialization for the API endpoints.
- **HMAC Signatures**: Every pull and callback is cryptographically verified to prevent tampering and replay attacks.

## Test Results
- `Tests\Feature\HQConfigurationTest` passed successfully.
- Asserted UI and profile creation correctly handles model scope limitations.
- Asserted `is_sensitive` items successfully encrypt in the local database but correctly decrypt dynamically when transmitted securely to the authenticated ERP endpoints over HTTPS + HMAC.
- Asserted `ConfigurationSynchronizationService` safely caches retrieved models.

## Production Readiness
**Status: READY**
The Remote Configuration Management module aligns seamlessly with previous sprints. It does not introduce breaking changes to existing architecture or telemetry models, and relies purely on the battle-tested command orchestration pipelines established in Sprint 7.3.

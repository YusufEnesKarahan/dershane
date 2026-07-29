# Sprint 7.2: HQ License Validation & Client Enforcement Layer

## Overview
This sprint implements a secure, bidirectional license validation pipeline between HQ Central Management Platform and remote SaaS ERP instances. HQ serves as the single source of truth for license state. ERP instances cache and enforce license restrictions locally, remaining resilient to network outages through an offline-tolerant caching layer.

## Architecture

```
HQ Central (Source of Truth)
    │
    │  POST /api/hq/license/validate
    │  ── VerifyHQApiSignature (HMAC SHA256 + Bearer + Timestamp)
    │
    ├── HQLicenseValidationService
    │     └── Reuses HQLicenseService::checkExpiration()
    │     └── Reads HQLicense + HQLicenseFeature models
    │
    ▼
ERP Instance (Client)
    │
    ├── LicenseVerificationService
    │     └── Uses HQHttpService::validateLicense() (no new HTTP client)
    │     └── Stores result in license_cache table
    │
    ├── LicenseCache Model (Offline tolerance)
    │
    ├── RequireFeature Middleware → Route::middleware('feature:crm')
    │
    ├── LicenseManager (Static API) → LicenseManager::has('crm')
    │
    └── hq:license-check Command (Daily via Scheduler)
```

## Key Deliverables

### 1. HQ License Validation API
- **Endpoint**: `POST /api/hq/license/validate`
- **Controller**: `HQLicenseValidationController`
- **Security**: Inherits full `VerifyHQApiSignature` middleware (Bearer token + HMAC SHA256 + timestamp replay prevention)
- **Response**: Returns `status`, `plan`, `expires_at`, and `features` map

### 2. HQ License Validation Service
- **Class**: `HQLicenseValidationService`
- **Methods**: `validateSystemLicense()`, `getLicenseFeatures()`, `buildLicenseResponse()`
- **Design**: Delegates expiration checks to existing `HQLicenseService::checkExpiration()` — zero logic duplication

### 3. ERP Client License Layer
- **Class**: `LicenseVerificationService`
- **Methods**: `validate()`, `refresh()`, `getCachedLicense()`, `hasFeature()`, `isActive()`
- **Communication**: Uses existing `HQHttpService::send()` — no new HTTP client layer

### 4. Local License Cache
- **Table**: `license_cache`
- **Model**: `LicenseCache`
- **Fields**: `uuid`, `system_uuid`, `license_key`, `status`, `plan`, `features` (JSON), `expires_at`, `last_checked_at`, `metadata` (JSON)
- **Purpose**: Offline tolerance — ERP functions normally even if HQ is unreachable

### 5. Feature Middleware
- **Class**: `RequireFeature`
- **Usage**: `Route::middleware('feature:crm')`
- **Behavior**: Returns 403 if feature disabled, Super Admin bypass built-in
- **Alias**: Registered as `'feature'` in `bootstrap/app.php`

### 6. LicenseManager
- **Class**: `LicenseManager`
- **Static API**: `LicenseManager::has('crm')`, `LicenseManager::active()`, `LicenseManager::plan()`
- **Registration**: Singleton in `AppServiceProvider`

### 7. Scheduler Integration
- **Command**: `hq:license-check`
- **Schedule**: Daily at 06:00
- **Pattern**: Uses existing `HQSchedulerService::executeTask()` — identical to other HQ commands

### 8. Admin Interface
- **Route**: `/admin/platform/license-status`
- **Controller**: `LicenseStatusController`
- **View**: Displays license status, plan, expiration, enabled/disabled features, HQ connection health, system identity

## Security

- All API communication protected by HMAC SHA256 + Bearer token + timestamp validation
- No `eval()`, `exec()`, `shell_exec()`, or dynamic code execution
- Super Admin bypass on feature middleware
- 5-minute replay attack prevention window

## Test Results

```
LicenseValidationTest .............. 8 tests, 22 assertions — PASSED
HQBackendTest ...................... 8 tests, 21 assertions — PASSED (regression)
HQLicenseTest ...................... 4 tests, 9 assertions  — PASSED (regression)
HQDashboardTest .................... 4 tests, 18 assertions — PASSED (regression)
```

## Files Created
- `app/Domain/HQ/Services/HQLicenseValidationService.php`
- `app/Http/Controllers/Api/HQLicenseValidationController.php`
- `app/Domain/License/Services/LicenseVerificationService.php`
- `app/Domain/License/LicenseManager.php`
- `app/Models/LicenseCache.php`
- `app/Http/Middleware/RequireFeature.php`
- `app/Http/Controllers/Admin/LicenseStatusController.php`
- `app/Console/Commands/HQLicenseCheckCommand.php`
- `database/migrations/2026_07_28_094135_create_license_cache_table.php`
- `resources/views/admin/platform/license-status.blade.php`
- `tests/Feature/LicenseValidationTest.php`

## Files Modified
- `routes/api.php` — Added license/validate endpoint
- `routes/admin.php` — Added license-status route
- `routes/console.php` — Added hq:license-check schedule
- `bootstrap/app.php` — Registered `feature` middleware alias
- `app/Domain/Platform/Services/HQHttpService.php` — Added `validateLicense()` method
- `app/Providers/AppServiceProvider.php` — Registered LicenseVerificationService singleton

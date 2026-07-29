# Sprint 7.1: HQ License Management v2

## Overview
HQ Central Platform now supports comprehensive SaaS License Management. This sprint establishes the foundational architecture to link billing plans, manage expiration timelines, and granularly control remote ERP feature accessibility through a centralized dashboard.

## Key Deliverables

### 1. Database Schema
- **`hq_licenses`**: Stores overarching subscriptions (Tenant, Instance, Plan, Expiration dates, generic JSON config). 
- **`hq_license_features`**: Specifically crafted to manage isolated module toggles linked to a parent license (e.g., toggling a 'CRM' or 'Accounting' feature dynamically without touching source code).

### 2. Service Layer
- **`HQLicenseService`**: Developed to safely handle business logic:
  - `createLicense` (Transactional creation with UUID keys)
  - `activateLicense` / `suspendLicense` (State manipulation)
  - `checkExpiration` (Scheduled capability to detect and automatically flip expired subcriptions)
  - `enableFeature` / `disableFeature` (Granular capability overrides)

### 3. Interface Layer
- **Dashboard Updates**: Enhanced the primary `/admin/platform/hq-central` view to include comprehensive statistics over Total, Active, Expired, and Expiring (<30 days) licenses.
- **System Detail View (`show`)**: Now natively reflects active license statuses on individual SaaS instances, rendering exact expiry states directly tied to the tenant.
- **License Hub (`/licenses`)**: 
  - **Index**: Lists all active and inactive licenses globally with filtering functionality.
  - **Show**: An advanced administration screen exposing current active features, enabling instant status toggling (Activate/Suspend) and dynamic feature assignment (Enable/Revoke buttons).

### 4. Security & Testing
- Bound to **`Super Admin`** permissions explicitly via `HQPolicy@manageLicense` and protected through Laravel Gates.
- Engineered `HQLicenseTest.php` suite covering:
  - Authorization blocking constraints
  - Automatic expiration logic verification
  - Dynamic boolean feature toggling logic
  - Database schema integrity assertions

## Conclusion
The HQ Central framework is actively equipped to function as a fully autonomous SaaS Management layer, successfully combining Tenant telemetry with enterprise license limitations seamlessly.

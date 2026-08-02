# Module Structure

The application is structured into domain-focused modules to maintain separation of concerns.

## Domains (`app/Domain/`)
- **Student:** Handles student lifecycle, guardians, and analytics.
- **Teacher:** Handles teacher profiles, schedules, and analytics.
- **Finance:** Manages invoices, payments, discounts, scholarships, and refunds.
- **Platform:** Core SaaS functionalities like Feature Flags, Licensing, and Tenant Context.
- **Reporting:** Dashboards, snapshots, and business intelligence.

## Core Infrastructure (`app/Core/`)
- **Repositories:** Centralized data access layer.
- **Context:** Tools for handling runtime context (e.g., `TenantContext`).

## UI / Views (`resources/views/`)
- **admin:** Core ERP interfaces, structured by domain (students, teachers, finance).
- **components:** Reusable Blade components (`x-admin.form`, `x-admin.button`, etc.).
- **layouts:** Main structural templates (`layouts.frontend`, `layouts.app`).

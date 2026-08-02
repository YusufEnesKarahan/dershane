# Architecture

## Overview
Dershane SaaS ERP is built on a multi-tenant Single Database architecture.
It uses standard Laravel patterns combined with Domain-Driven concepts.

## Core Concepts
- **Multi-Tenancy:** Handled primarily via `branch_id` scoping at the Repository and Model level.
- **Repository Pattern:** Abstracting Eloquent models in `app/Core/Repositories`.
- **Services:** Business logic is encapsulated inside Domain-specific services (e.g. `App\Domain\Student\Services\`).
- **DTOs:** Data Transfer Objects are used to strongly type request payloads being passed to Actions or Services.
- **Actions:** Single-responsibility classes for specific operations (e.g., `CreateStudentAction`).

## Layers
1. **Controllers (App\Http\Controllers):** Handle HTTP requests, basic validation, and return views or JSON.
2. **Services (App\Domain\*\Services):** Contain core business logic and coordinate between Repositories and third-party APIs.
3. **Actions (App\Domain\*\Actions):** Reusable single-purpose logic blocks.
4. **Repositories (App\Core\Repositories):** Isolate database operations, including eager loading and multi-tenant scoping.
5. **Models (App\Models):** Eloquent models representing the database schema.

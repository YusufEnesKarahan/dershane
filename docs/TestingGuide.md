# Testing Guide

## Overview
We use PHPUnit for testing. The test suite is divided into Unit and Feature tests.

## Running Tests
Run the entire test suite:
```bash
php artisan test
```

## Writing Tests
1. **Critical Flows:** Always write tests for critical business operations (creation, updates, deletion, financial transactions).
2. **Tenant Isolation:** Ensure that users from Branch A cannot access data from Branch B.
3. **Use Helpers:** Utilize `setupSaaSTenant()` defined in `tests/TestCase.php` to quickly scaffold a tenant environment.
4. **Assertions:**
   - Always verify HTTP status codes.
   - Assert database state (`assertDatabaseHas`, `assertDatabaseMissing`).
   - Validate session errors (`assertSessionHasNoErrors`).

## Test Environment
Tests run using the in-memory SQLite database (`:memory:`) to ensure speed and isolation, as defined in `phpunit.xml`.

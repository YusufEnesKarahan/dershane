# Coding Standards

## PHP Standards
- Follow PSR-12 coding standards.
- Use strict typing where applicable (`declare(strict_types=1);` is encouraged for new Domain logic).
- Always include return types and parameter types for Services, Repositories, and Actions.

## Naming Conventions
- **Controllers:** PascalCase, ending with `Controller`.
- **Services:** PascalCase, ending with `Service`.
- **Repositories:** PascalCase, ending with `Repository` and implementing `RepositoryInterface`.
- **Actions:** PascalCase, describing the action (e.g., `CreateUser`).
- **Variables:** camelCase.
- **Methods:** camelCase.

## Best Practices
- **No Magic Numbers:** Use constants (e.g., `Status::ACTIVE`) instead of hardcoded strings or numbers.
- **No Raw Queries:** Always use Eloquent or Query Builder via Repositories.
- **Keep Controllers Thin:** Offload business logic to Services or Actions.
- **Use DTOs:** Pass strongly-typed DTOs instead of raw array data to Domain layers.
- **Comments & PHPDoc:** Document complex logic. Always include PHPDoc blocks for interfaces and complex methods.

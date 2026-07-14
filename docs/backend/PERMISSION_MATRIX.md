# Permission Matrix

The Permission Matrix is the central grid interface used to customize role scopes.

## Implementation
- **Groupings**: Reads the categories dynamically from `config/permissions.php` (`PermissionGroupService`).
- **Presets**: Direct interaction using preset buttons triggers Javascript selectors that quickly toggle permission sets (`CRUD`, `ReadOnly`, `FullAccess`) in the UI.
- **Visuals**: Grouped in an accordion component using standard design guides.

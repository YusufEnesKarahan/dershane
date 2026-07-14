# Navigation Service

The `AdminMenuService` (`app/Domain/Auth/Services/AdminMenuService.php`) is responsible for generating the UI layout and navigation payload. 

## Features
- The payload defines hierarchical menu structures.
- It leverages the `AuthorizationService` to ensure menus are never sent to the UI if the user lacks the necessary permission.
- Currently, the service provides the core sidebar hierarchy. Breadcrumbs and recent page stubs will be aggregated in this service in the future.

# Admin Menu Architecture

The administration panel's sidebar and navigation are entirely configuration-driven.

## Configuration
All menus are declared in `config/admin-menu.php`. The config describes labels, icons, route mappings, and authorization properties (`permission`, `edition`, `feature`).

## Core Classes
- **MenuBuilder**: Iterates over the configuration arrays and filters out disallowed links.
- **VisibilityResolver**: Evaluates the strict union: `permission` AND `edition` AND `feature` checks.
- **BadgeResolver**: Computes dynamic notification badge numbers.
- **BreadcrumbResolver**: Generates page location contexts.
- **AdminMenuService**: The orchestrator which binds the sub-services together.

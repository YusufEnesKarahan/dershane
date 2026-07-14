# Role Cloning Architecture

Cloning allows rapid creation of new user roles inheriting permissions from existing roles.

## Mappings
- **Cloned Source**: When cloning a role (e.g. `Administrator`), all permissions are synced to the target (`RoleCloneService`).
- **Overrides**: Administrators can customize the cloned name, description, and color (rendered dynamically in lists) to differentiate it.

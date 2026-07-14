# Permission Dictionary

All permissions follow a `resource.action` syntax. Wildcards (`*`) are supported (e.g. `users.*` grants all user permissions).

## System
- `dashboard.view`: Access the admin panel.
- `settings.view`, `settings.update`
- `branches.view`, `branches.create`, `branches.update`, `branches.delete`
- `media.view`, `media.create`, `media.delete`
- `logs.view`: Access system logs.

## Access Management
- `users.view`, `users.create`, `users.update`, `users.delete`
- `roles.view`, `roles.create`, `roles.update`, `roles.delete`
- `permissions.view`, `permissions.assign`

## CMS
- `pages.view`, `pages.create`, `pages.update`, `pages.delete`
- `blogs.view`, `blogs.create`, `blogs.update`, `blogs.delete`
- `gallery.*`
- `announcements.*`

## Education
- `students.*`
- `teachers.*`
- `courses.*`
- `classrooms.*`
- `registrations.*`

## CRM
- `leads.*`
- `contacts.*`

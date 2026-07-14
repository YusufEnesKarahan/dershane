# Permission Dictionary

All permissions are strictly registered in the `PermissionDictionary` (`app/Domain/Auth/Dictionaries/PermissionDictionary.php`). They follow the `module.action` pattern:

- `users.view`, `users.create`, `users.update`, `users.delete`, `users.restore`, `users.export`
- `roles.view`, `roles.create`, `roles.update`, `roles.delete`, `roles.assign`
- `permissions.view`, `permissions.assign`
- `pages.view`, `pages.create`, `pages.update`, `pages.delete`
- `blogs.view`, `blogs.create`, `blogs.update`, `blogs.delete`
- `teachers.view`, `teachers.create`, `teachers.update`, `teachers.delete`
- `courses.view`, `courses.create`, `courses.update`, `courses.delete`
- `gallery.view`, `gallery.create`, `gallery.delete`
- `media.view`, `media.create`, `media.delete`
- `settings.view`, `settings.update`
- `crm.view`, `crm.manage`
- `attendance.view`, `attendance.manage`
- `homeworks.view`, `homeworks.manage`
- `classrooms.view`, `classrooms.manage`
- `branches.view`, `branches.manage`

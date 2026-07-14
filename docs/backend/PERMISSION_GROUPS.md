# Permission Groups

This system categorizes permissions into functional groups using the `PermissionGroupService` (`app/Domain/Auth/Services/PermissionGroupService.php`).

## Architecture
The groupings are defined in `App\Domain\Auth\Dictionaries\PermissionDictionary`. The `PermissionGroupService` maps them dynamically:
- **System**: Base panel controls and Branch configurations.
- **Users**: User CRUD actions.
- **Roles**: Access management settings (permissions/roles assign).
- **CMS**: Page/Blog/Announcements management.
- **Education**: Courses, Classrooms, Homeworks, and Attendance.
- **CRM**: Leads and Contacts details.
- **Media**: Asset library adjustments.
- **Settings**: System configurations.

This categorization is used to dynamically construct lists in the role management UI.

# Student Module Architecture

Manages student enrollment lifecycles, personal details, and branch transfers:

- **Student Number (`student_number`)**: Unique system-wide identifier.
- **Academic Statuses**: Active, Inactive, Graduated, Left (logged via `student_status_histories`).
- **Transfers Engine**: Tracks branch and classroom migrations (`student_transfers`).

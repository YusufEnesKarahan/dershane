# Announcement & Read System

Manages general announcements, distribution targeting, and read audit logs:

- **Announcements (`announcements`)**: General notices with custom titles, content, publishing status, and release times.
- **Announcement Groups (`announcement_groups`)**: Targets notices to specific subsets (e.g. All Students, All Instructors, Branch Staff).
- **Announcement Read Log (`announcement_reads`)**: Unique constraint on `[announcement_id, user_id]` provides precise administrative read-receipt auditing.

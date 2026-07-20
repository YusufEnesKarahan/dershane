# Course Module Architecture

The Course module provides complete management of academic course offerings:

- **Unique Course Code**: System enforces global uniqueness on course codes (`code` field).
- **Levels & Subjects**: Hierarchical categorizations for student tracking (`CourseLevel`, `CourseSubject`).
- **Prerequisites Engine**: Prevents student enrollments if prerequisite course requirements are unfulfilled.
- **Draft & Publish Workflow**: Supports staging states before publishing.

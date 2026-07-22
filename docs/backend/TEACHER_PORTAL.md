# Teacher Portal & Staff Management Architecture

The Teacher Portal enables instructors to manage classroom actions, attendance recording, homework assignments, and performance tracking:

- **Class Assignments (`teacher_assignments`)**: Maps teachers to their active classrooms and courses.
- **Schedules (`teacher_schedules`)**: Registers time blocks and course details for calendar listing.
- **Performance Evaluation (`teacher_performance_logs`)**: Records scores and metrics logged by administrators.
- **Teacher Portal Dashboard**: A secure portal (`/teacher/dashboard`) with sub-modules for class listings, attendance input, homework evaluations, and personal success metrics.

# Submission & Evaluation System

Handles student homework submissions and teacher evaluations:

- **Submissions (`assignment_submissions`)**: Tracks student submissions, due date validation, and automatically flags late submissions (`is_late = true`).
- **Single Submission Constraint**: Unique index on `['assignment_id', 'student_id']` prevents duplicate submissions.
- **Evaluation & Scoring (`assignment_scores`)**: Teachers assign scores and submit qualitative feedback.
- **File Attachments (`assignment_files`)**: Supports multi-file uploads for both assignments and submissions.

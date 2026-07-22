# Exam Module Architecture

Manages institutional and trial examinations:

- **Exams (`exams`)**: Code, title, exam type (TYT, AYT, Trial, Subject), total questions, duration.
- **Sessions (`exam_sessions`)**: Classroom and branch exam assignments.
- **Duplicate Protection**: Unique index `['exam_id', 'student_id']` prevents duplicate exam submissions per student.

# Attendance Module Architecture

Manages session-based student attendance records:

- **Attendance Session (`attendance_sessions`)**: Connects classroom, course, teacher, session date, and time blocks.
- **Attendance Records (`attendances`)**: Logs per-student status (PRESENT, LATE, ABSENT, EXCUSED).
- **Duplicate Protection**: Database constraint `['attendance_session_id', 'student_id']` prevents double logging.
- **QR Check-in**: Supports direct QR code check-in timestamps (`qr_code_scanned`, `check_in_time`).

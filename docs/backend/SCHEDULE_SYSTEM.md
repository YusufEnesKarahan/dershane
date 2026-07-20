# Schedule Engine & Conflict Detection

An automated verification engine (`ScheduleConflictService`) ensures collision-free weekly schedules:

- **Classroom Overlap Check**: Rejects overlapping time slots in the same classroom.
- **Teacher Double-Booking Check**: Prevents assigning an instructor to multiple places simultaneously.
- **Holiday Constraint**: Disallows lesson placement on registered holidays.
- **Schedule Exceptions (`schedule_exceptions`)**: Manages makeup lessons and cancellations.

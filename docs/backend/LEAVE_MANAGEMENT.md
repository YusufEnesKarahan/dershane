# Leave Management System

The Leave Management system tracks annual, sick, maternity, paternity, administrative, and unpaid leaves requested by employees:

- **Leave Requests (`leave_requests`)**: Includes request period, total days calculation, approval logs (`approved_by`, `approved_at`), and status states (`Pending`, `Approved`, `Rejected`).
- **Validation**: Leave days are calculated as the duration between start and end dates.

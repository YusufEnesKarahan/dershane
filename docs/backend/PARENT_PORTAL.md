# Parent Portal System Architecture

The Parent Portal provides parents/guardians with real-time academic and administrative tracking of linked students:

- **Linkage (`parent_students`)**: Maps parent users to student profiles with role assignments (e.g. Mother, Father, Guardian).
- **Access Tracking (`parent_access_logs`)**: Records portal access history, IP addresses, and user agents for audit logs.
- **Push Notification Subsystem (`parent_devices` & `parent_notifications`)**: Stores mobile device tokens and lists notification broadcasts sent specifically to parents.
- **Unified Overview Dashboard**: Displays dynamic tabs for Student Profile, Attendance lists, Exam Net results, Homework task submissions, and billing Invoices.

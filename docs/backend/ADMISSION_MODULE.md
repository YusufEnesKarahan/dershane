# Student Admission & Pre-Registration Module

The Admission module manages pre-registration applications for candidates converting from the CRM pipeline:

- **Pre-Registration Records (`student_admissions`)**: Contains student info, guardian contacts, prepare program, fee quote, and deposit payments.
- **Evrak / Document Verification (`admission_documents`)**: Tracks uploaded student ID photocopies, guardian forms, and contracts with approval status.
- **Workflow State Logs (`admission_status_logs`)**: Records audit history for every status change (`lead_converted` -> `pre_registration` -> `document_pending` -> `document_completed` -> `contract_ready` -> `payment_pending` -> `enrolled`).

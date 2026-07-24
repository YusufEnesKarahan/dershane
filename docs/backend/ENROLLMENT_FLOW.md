# Student Enrollment & Contract Flow

The Enrollment flow transitions pre-registration admissions into active students and generates financial billing:

1. **Lead Conversion**: A CRM lead is converted to a pre-registration (`StudentAdmission`).
2. **Document Approval**: Required documents (ID, forms) are uploaded and approved.
3. **Contract Generation**: Dynamic contract template (`ContractTemplate`) is rendered with student placeholders and signed.
4. **Final Enrollment (`EnrollStudentDTO`)**:
   - Creates official `Student` record with a unique `student_no`.
   - Generates Finance `Invoice` & `InvoiceItem` for tuition fees.
   - Settles deposit payments.
   - Creates `StudentEnrollment` record linking student, classroom, and invoice.

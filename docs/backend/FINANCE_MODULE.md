# Finance & Billing Module Architecture

Manages invoicing, student debt accounts, discounts, and scholarships:

- **Invoices (`invoices`)**: Unique `invoice_number`, student, issue date, due date, total amount, paid amount, status (`Pending`, `Partial`, `Paid`, `Cancelled`).
- **Invoice Items (`invoice_items`)**: Line items per invoice.
- **Student Debts (`student_debts`)**: Tracks remaining student balances synchronized with invoices and payments.
- **Discounts & Scholarships (`discounts`, `scholarships`)**: Fixed/percentage discounts and student scholarship tracking.

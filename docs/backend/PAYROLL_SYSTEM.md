# Payroll Management System

The Payroll system calculates monthly employee salaries, ek bonuses, overtime credits, tax rates, social insurance, and net payouts:

1. **Gross Salary**: `base_salary` + `bonus` + `overtime_amount`.
2. **Insurance**: `gross_salary` * 14%.
3. **Tax**: (`gross_salary` - `insurance`) * 15%.
4. **Net Salary**: `gross_salary` - `insurance` - `tax` - `deductions`.
5. **Payment Tracking**: Payouts transition through states: `Draft` -> `Approved` -> `Paid`.

# Settings Module

The Platform Configuration module provides database-driven persistence:

- **Historic Logs**: The `setting_histories` audit trail keeps track of modified records.
- **Sensitive Values**: Encryption is triggered automatically using Laravel Crypt.
- **Cache layer**: Handled automatically via observers.

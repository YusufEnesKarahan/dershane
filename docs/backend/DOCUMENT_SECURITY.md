# Document Security & Access Control

Access control and compliance for digital archive documents:

- **Policy Enforcements**: `DocumentPolicy` ensures users hold `documents.view` or `documents.manage` permissions before accessing files.
- **Granular Permissions (`document_permissions`)**: Role-level grants for specific document access (`can_view`, `can_download`, `can_delete`).
- **Audit Trails**: Every download, view, or deletion triggers a `document_logs` entry storing the user's ID, action type, and client IP address.

# Document Management System Architecture

The Document Management System provides a central digital repository for all institution files and archives:

- **Document Categories (`document_categories`)**: Taxonomies including student records, HR personnel files, contracts, financial invoices, bank receipts, and official MEB communications.
- **Documents (`documents`)**: Core document metadata registers with polymorphic associations (`documentable`) allowing links to Students, Employees, Teachers, Admissions, or Invoices.
- **Document Versions (`document_versions`)**: Track file revisions, version numbers (v1, v2, ...), uploader credits, and change logs.
- **Document Audit Logs (`document_logs`)**: Records access events (`upload`, `update`, `delete`, `download`, `share`, `restore`) with timestamp and IP origin details.

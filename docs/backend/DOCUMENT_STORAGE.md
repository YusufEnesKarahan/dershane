# Document File Storage Guidelines

`FileStorageService` manages physical file uploads, validations, and storage paths:

- **Storage Location**: `storage/app/public/documents/` exposed through public link.
- **Allowed Extensions**: `pdf`, `doc`, `docx`, `xls`, `xlsx`, `ppt`, `pptx`, `jpg`, `jpeg`, `png`, `gif`, `zip`, `rar`, `txt`, `csv`.
- **Max File Size Limit**: 50MB per upload file.
- **File Naming Standard**: Sanitized slugified title + timestamp suffix to prevent collisions.

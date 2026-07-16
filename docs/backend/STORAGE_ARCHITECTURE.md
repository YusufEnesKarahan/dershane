# Storage Architecture

The physical storage is fully decoupled:

- **StorageService**: Wraps raw storage writes/removals behind dynamic `Storage::disk(...)` wrappers.
- **Config Driven**: Default disk targets are configured via `config/media.php`.
- **Decoupled API**: Moving from local disk to cloud storage arrays (S3, MinIO, or Cloudflare R2) requires only updating configuration settings.

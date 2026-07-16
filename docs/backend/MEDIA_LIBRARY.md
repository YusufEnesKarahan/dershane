# Media Library Documentation

The Media Library module provides a package-free digital asset management system:

- **Usage Tracker**: Tracks database relationships inside `media_usages` to warn users if they attempt to delete a file that is in use.
- **Checksum Matches**: Uses SHA256 hashing (`checksum`) to bypass duplicate file saves on upload.
- **Polymorphic Observer Hooks**: `MediaObserver` intercepts Eloquent events to dynamically trigger optimization pipelines.

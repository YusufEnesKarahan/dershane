# Profile Management

Users can manage their individual profiles directly within the administration panel.

## Features
- **Kişisel Bilgiler (Personal Info)**: Updates name, email, and phone using the `UpdateProfileAction`.
- **Güvenlik (Security)**: Resets passwords, forcing session invalidation across all other devices via `auth()->logoutOtherDevices()`.
- **Profil Resmi (Avatar)**: Uploads images directly into `storage/app/public/avatars`. Resolves default fallback initials if no avatar exists.

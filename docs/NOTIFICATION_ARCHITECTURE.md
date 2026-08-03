# Notification & Announcement Architecture

This document describes the architectural implementation of the Notification and Announcement modules in the Dershane SaaS application.

## Overview

The notification system uses a combination of Laravel's built-in Notification features (via `Notification::send()`) along with customized channels and models to support our strict multitenancy and RBAC requirements.

## Key Components

### 1. Database Model (`App\Models\Notification`)
- We use a custom `Notification` model instead of Laravel's default `DatabaseNotification` class.
- The `$fillable` array defines which attributes are permitted for bulk assignment. Since `id` (the UUID from Laravel notifications) is not included in `$fillable`, Eloquent uses auto-increment integer keys instead.
- We added an `isRead()` helper method and a `markAsRead()` method to safely mark notifications as read.

### 2. Custom Channel (`App\Channels\CustomDatabaseChannel`)
- Laravel's default `DatabaseChannel` assumes that `notifications` table structure precisely mirrors its hardcoded requirements.
- We created `CustomDatabaseChannel` to extract `title` and `content` attributes from the `data` payload and map them directly to `title`, `message`, and `content` columns in our custom table schema.
- This mapping ensures that `NOT NULL` constraints on our `notifications` table are satisfied when `create()` is called.

### 3. Service Layer (`App\Domain\Notification\Services\NotificationService` & `AnnouncementService`)
- **Thin Controller, Fat Service**: Controllers solely handle validation, authorization, and dispatching to services.
- **AnnouncementService**: Handles creation and broadcasting of announcements. It uses `User::whereHas` for role-based targeting while strictly appending `->where('branch_id', $announcement->branch_id)` and `->where('status', \App\Enums\UserStatus::ACTIVE->value)` to maintain tenant isolation.
- **NotificationService**: Handles the actual dispatching of `GeneralNotification` classes to users, utilizing our custom channel. Also handles bulk marking notifications as read via `update(['read_at' => now(), 'status' => 'Read'])`.

### 4. Event & Observers
- An observer `NotificationObserver` can be utilized for advanced mapping, but our main logic for parsing `data` into columns takes place in `CustomDatabaseChannel::buildPayload()`.

## Security & Multitenancy

1. **Branch Isolation**: In `AnnouncementService::sendToRole()` and `sendToBranch()`, the query scopes to the `$announcement->branch_id`, ensuring announcements created by one branch administrator never leak to users of another branch.
2. **RBAC Validation**: Endpoints (such as `admin.announcements.publish` and `student.notifications.read`) use policies (`$this->authorize(...)`) and direct permission checks (`abort_unless(auth()->user()->hasPermission(...))`) to verify access.

## Portals
- **Student Portal** (`resources/views/portal/student/dashboard.blade.php`): Contains a "Bildirimler & Duyurular" component that lists unread and read notifications.
- **Parent Portal** (`resources/views/portal/parent/dashboard.blade.php`): Identical component that enables parents to view their notifications. Parents only receive notifications if they have the `Parent` role and belong to the correct branch.

## Testing
Comprehensive testing is implemented in `tests/Feature/NotificationSecurityTest.php`:
- Testing proper delivery based on Roles.
- Testing branch isolation (users from a different branch do not receive announcements).
- Verifying students cannot read other users' notifications.
- Ensuring Database constraints are met without exceptions.

# Notification Module Architecture

Manages dispatch and templates for system, short message (SMS), and email notifications:

- **Notifications (`notifications`)**: Tracks custom system titles, type, user, content, and read state.
- **Notification Templates (`notification_templates`)**: Preset notification messages with support for parameter variables (e.g. `{amount}`, `{due_date}`).
- **SMS Providers (`sms_providers`)**: Stores API configurations for external text messaging gateways.
- **Mail Templates (`mail_templates`)**: HTML templates for SMTP-dispatched institutional emails.

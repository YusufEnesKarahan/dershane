# Notification System

Notification Center stores delivery intent in `notifications`, attempts in `notification_logs`, templates in `notification_templates`, and user channel consent in `notification_preferences`. Panel, email, and SMS placeholder adapters are selected by `channel`. `NotificationService` owns creation, sending, and read state.

Lists eager-load recipients; user/read and channel/date indexes support dashboard and filter queries.

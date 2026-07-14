# User Preferences

User settings and preferences are serialized into a single `preferences` JSON column inside the `users` table.

## Dictionary Key Schema
- `theme`: `light`, `dark`, or `auto` (default)
- `language`: `tr` or `en`
- `timezone`: Timezone identifier (default: `Europe/Istanbul`)
- `density`: Compact or comfortable
- `pagination`: Default items shown in lists.

This avoids database schema alterations when adding minor user-level presentation settings.

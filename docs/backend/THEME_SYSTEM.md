# Theme System

The user interface styles dynamically adapt to theme custom selections:

- **CSS Variables compilation**: Modifying colors, spacing, and border-radius triggers `ThemeService::compileThemeCss()` to generate `public/css/theme_custom.css`.
- **Live Previewing**: Alpine variables bind form elements in real-time, displaying card layouts, color changes, and buttons before committing changes to the database.

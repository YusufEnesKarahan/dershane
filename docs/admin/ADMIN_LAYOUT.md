# Admin Layout Framework

The Dershane SaaS platform features a fully custom, Tailwind-based administration interface optimized for high performance, multi-tenancy, and advanced RBAC.

## Architecture
- **Master Layout (`resources/views/layouts/admin.blade.php`)**: The root HTML structure. Handles Alpine.js data states (`sidebarOpen`, `miniSidebar`, `darkMode`), Accessibility properties, and wraps the Topbar and Sidebar components.
- **Sidebar (`<x-admin.sidebar.layout>`)**: Responsive, collapsible navigation. Automatically renders the `AdminMenuService` payload. Uses `<x-admin.sidebar.group>` and `<x-admin.sidebar.item>` for structured nesting.
- **Topbar (`<x-admin.topbar.layout>`)**: Global header housing the Global Search trigger, Theme Switcher, and User Dropdown menu.

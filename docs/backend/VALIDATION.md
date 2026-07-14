# Backend Validation Standards

This document outlines the standard validation rules and formatting requirements for incoming requests in the Dershane SaaS platform.
Future CRUD operations via FormRequests will strictly adhere to these rules.

## Core Principles
1. **Always Validate:** Never trust user input. Use dedicated Form Request classes for validation.
2. **Use Strong Typing:** Expect exact types (e.g., `integer`, `boolean`, `string`).
3. **Fail Fast:** The API or controller should return standard validation errors instantly.

## Model Specific Rules

### Users / Auth
- `name`: required, string, max:255
- `email`: required, email, max:255, unique:users
- `password`: required, string, min:8, strong (letters, numbers, symbols)

### Branches
- `name`: required, string, max:255, unique:branches
- `slug`: required, string, max:255, unique:branches
- `phone`: nullable, string, max:20
- `email`: nullable, email, max:255

### Pages & Content (Blogs, Announcements, Events)
- `title`: required, string, max:255
- `slug`: required, string, max:255, unique
- `content`: required, string
- `is_published`/`is_active`: boolean
- `published_at`/`event_date`: nullable, date

### Education (Courses, Classrooms, Teachers)
- `price`: nullable, numeric, min:0
- `capacity`: required, integer, min:1
- `branch_id`: required for branch-specific entities, exists:branches,id

### CRM (Leads, Contact Messages)
- `phone`: required, string, max:20, regex for phone formats
- `email`: nullable, email
- `status`: required, in:[new, contacted, qualified, lost]

## Custom Messages
All Form Request classes should provide user-friendly localized messages mapping to `lang/tr/validation.php`.

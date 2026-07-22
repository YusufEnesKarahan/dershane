# System Architecture Overview

## Domain-Driven Modular Design

The platform uses a layered architecture:

- **Core / Repository Layer**: Interfaces and Eloquent implementations for data persistence.
- **DTO Layer**: Strongly typed Data Transfer Objects for request validation and business operations.
- **Domain Service & Action Layer**: Encapsulated business rules, calculations, rankings, and analytics.
- **Controller & Presentation Layer**: Thin controllers rendering Blade components and handling administrative workflows.

## Modules Overview

- **CMS & Blog Suite**: Dynamic page builder, revisions, categories, and tag management.
- **Media Library (DAM)**: Polymorphic media attachments with multi-variant strategy.
- **Platform Configuration & White Label**: Granular platform settings, theme customization, and audit histories.
- **Teacher & Course Suite**: Instructor schedules, performance, pricing, and branch assignments.
- **Classroom & Academic Calendar Suite**: Capacity limits, schedules, collision detection, and holiday management.
- **Student Suite**: Enrollment lifecycle, guardian contacts, document attachments, status history.
- **Attendance Suite**: Lesson attendance sessions, bulk recording, QR scan support, risk student reports (>15% threshold).
- **Exam & Assessment Suite**: TYT/AYT/Trial exams, net engine ($Net = Correct - \frac{Wrong}{4}$), 500-pt scaled scores, global/branch standings.
- **Homework & Assignment Suite**: Individual/Classroom/Course targeting, due date validations, late flags (`is_late`), teacher scoring & feedback, submission analytics.
- **Finance & Billing Suite**: Invoicing, payment receipts, student debt tracking, installment plans, discounts, scholarships, refunds, and financial collection analytics.
- **Communication & Notification Suite**: Multi-channel dispatching (SMS, Email, System notifications), message template engine, delivery log registry, announcement groups, and read receipts.
- **Parent Portal Suite**: Parent-student profile linkages, portal access monitoring logs, custom parent notifications, device push registers, and tabbed academic/financial trackers.

# Guidance & Student Performance Management Architecture

## 1. Overview
The Guidance & Student Performance Management Module (Sprint 9.2) is a specialized domain dedicated to tracking student risk factors, managing behavioral interventions, setting performance goals, and scheduling guidance meetings. It aggregates data from the Homework, Exam, and Attendance modules to proactively identify at-risk students and manage the subsequent follow-up interactions.

## 2. Core Principles
- **Thin Controller, Fat Service**: All domain logic, particularly the risk engine calculations, resides in the Service layer (e.g., `StudentPerformanceService`).
- **Tenant Isolation**: Deeply integrated with `TenantScoped` global scopes. The `branch_id` is mandatory for all transactions to prevent tenant data leakage.
- **Service-Driven Data Access**: Eschews Repository and Action patterns in favor of structured Services (`GuidanceManagementService`, `TeacherGuidanceService`, etc.) following a simplified DDD approach.
- **Role-Based Access Control (RBAC)**: All access to guidance resources is heavily protected by Policies (`StudentGuidanceRecordPolicy`, etc.), determining whether an Admin, Teacher, Student, or Parent can interact with the record.

## 3. Database Schema Overview
- **`student_guidance_records`**: Centralized records for tracking behavioral incidents and interventions.
- **`student_meetings` / `parent_meetings`**: Scheduling system for tracking past and future consultations with students and parents.
- **`student_goals`**: Tracks academic and personal targets assigned to students.
- **`student_risk_levels`**: Historical log of student risk classifications (Low/Medium/High/Critical).
- **`performance_snapshots`**: Time-stamped aggregations of a student's academic standing at a particular date.
- **`student_notes`**: Private annotations made by teachers or counselors.

## 4. Key Services & Engines
### StudentPerformanceService (Risk Engine)
The Risk Engine continuously analyzes student data across multiple domains and computes a weighted `risk_score`.
- **Inputs**: Attendance rates, exam averages, homework completion ratios, and late submission frequencies.
- **Processing**: Weights these metrics against defined thresholds to generate a risk score from 0-10.
- **Outputs**: Generates a `PerformanceSnapshot` and updates the `StudentRiskLevel`. Scores mapping:
  - **Critical (≥ 6 points)**: High priority intervention required.
  - **High (≥ 4 points)**: At risk, requires monitoring.
  - **Medium (≥ 2 points)**: Mild risk, some attention needed.
  - **Low (0-1 points)**: On track.

### Portal Services
- **`TeacherGuidanceService`**: Feeds the Teacher Dashboard. It surfaces all students currently classified as High or Critical risk in their classes, alongside upcoming scheduled meetings.
- **`StudentPerformanceService` (Portal Methods)**: Exposes read-only views for the student portal, allowing students to check their own goals, historical performance, and risk metrics.
- **`ParentGuidanceService`**: Exposes read-only aggregations for parents to monitor their child's guidance notes and upcoming meetings.

## 5. Security & Isolation
- The `TenantScoped` trait is applied to all models.
- When generating performance snapshots, cross-tenant data spillage is strictly avoided by passing contextually valid `$branchId` constraints.
- `Gate::before` interceptors are handled exclusively for `Super Admin` logic, delegating typical authorization to module-specific Policies mapped via Laravel's Policy registrar.

## 6. Implementation Notes
- All financial, attendance, and evaluation queries check for class existence before instantiating related queries to safely support missing legacy data or soft-deleted parents.
- Requires DB transactions when generating records alongside status notes or linked goals to guarantee data consistency.

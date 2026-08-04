# Exam & Assessment Management Architecture

## 1. Overview
The Exam & Assessment Management Module (Sprint 9.4) allows Dershane administrators to create exams, teachers to input results, and students/parents to track performance. It integrates tightly with the Subscription Limit system and the Guidance Performance Engine.

## 2. Core Principles
- **Thin Controller, Fat Service**: Controllers route requests to the domain services (`ExamManagementService`, `ExamResultService`, `ExamAnalysisService`).
- **Tenant Isolation**: Branch isolation is fully enforced through the `TenantScoped` global scope and the `branch_id` field on every exam-related model.
- **RBAC Authorization**: Permissions are checked in `ExamPolicy` and `ExamResultPolicy` using role-based access control.
- **Performance Engine Integration**: Exam results actively feed into the `StudentPerformanceService` to detect declining trends and update the student's risk level automatically.

## 3. Database Schema

### `exams`
Stores the main exam record.
- `branch_id`: Tenant context.
- `title`, `description`, `type`: General exam details.
- `exam_date`, `duration_minutes`, `total_score`: Exam configuration.
- `status`: draft, published, completed, cancelled.

### `exam_subjects`
Defines the breakdown of the exam by course/subject.
- `exam_id`: References the exam.
- `course_id`: References the subject.
- `question_count`, `max_score`: Subject scoring configuration.

### `exam_results`
Stores the overall score of a student for an exam.
- `exam_id`: References the exam.
- `student_id`: References the student.
- `score`: The student's total score.
- `correct_answers`, `wrong_answers`, `empty_answers`: Answer breakdowns.
- `notes`: Optional teacher notes.

### `exam_answers`
Stores detailed per-subject performance.
- `exam_result_id`: References the result.
- `course_id`: References the subject.
- `correct`, `wrong`, `empty`: Answer details for that subject.

### `exam_rankings`
Optional table to track branch-level or classroom-level rankings.

## 4. Key Services

### `ExamManagementService`
Handles CRUD operations for exams. Checks `SubscriptionLimitService` to ensure `max_exams` limit is respected before creation.

### `ExamResultService`
Allows saving and updating student exam results. Validates that the score is within the `total_score` limit. Upon saving results, it interacts with `StudentPerformanceService` to recalculate risk scores based on declining exam trends.

### `ExamAnalysisService`
Provides statistical data (average scores, highest, lowest, distribution) for an exam, helping administrators and teachers analyze overall performance.

## 5. Security & Authorization
- **Subscription limits**: `SubscriptionLimitService` is called when creating exams to prevent exceeding plan constraints.
- **Policies**: Standard policies map to specific permissions like `exam.create`, `exam.view`, `exam.result.create`.
- **Tenant Context**: `\App\Core\Context\TenantContext::getActiveBranchId()` acts as a fallback for enforcing the branch dynamically during creation.

# SaaS Institution Onboarding & Setup Wizard Architecture

## Overview

The **SaaS Institution Onboarding & Setup Wizard** provides a streamlined 5-step interactive setup process for newly registered institutions and branches on the Dershane SaaS platform.

It ensures every branch initializes its core metadata (institution settings, active academic year, license package, first teacher, first classroom) before accessing critical operational portals.

---

## 1. Database Schema

### `onboarding_steps`
Tracks high-level setup progress per branch.
- `id`, `branch_id`, `step` (1 to 5), `status` (`in_progress`, `completed`), `completed_at`, `timestamps`.

### `institution_settings`
Stores official institution profile info (uses `TenantScoped`).
- `id`, `branch_id`, `institution_name`, `logo`, `phone`, `email`, `address`, `city`, `district`, `website`, `academic_year`, `timestamps`.

### `onboarding_checklists`
Fine-grained checklist item tracker per branch (uses `TenantScoped`).
- `id`, `branch_id`, `key`, `completed` (boolean), `completed_at`, `timestamps`.

---

## 2. The 5-Step Wizard Flow

| Step | Title | Key Handled | Database Writes / Action |
|---|---|---|---|
| **Step 1** | **Kurum Bilgileri** | `institution_profile_completed` | Updates `institution_settings` record |
| **Step 2** | **Akademik Yıl** | `academic_year_created` | Creates/activates `academic_terms` record |
| **Step 3** | **Paket Seçimi** | `package_selected` | Invokes `PackageService::changeBranchPackage()` writing to `branch_packages` |
| **Step 4** | **Öğretmen Ekleme** | `teacher_added` | Creates `User` (with `Teacher` role) & `Teacher` domain record |
| **Step 5** | **Sınıf Tanımlama** | `classroom_created` | Creates `Classroom` record |

---

## 3. Key Services & Middleware

### `OnboardingService` (`app/Domain/Onboarding/Services/OnboardingService.php`)
- `getProgress($branch)`: Calculates percentage, completed steps, remaining steps, and checklist states.
- `completeStep($branch, int $stepNumber, ?string $key)`: Advances step status and checks off key.
- `isCompleted($branch)`: Returns true if all 5 setup steps/checklists are completed.
- `initializeBranchOnboarding($branch)`: Seed default checklist and step records for new branches.

### `EnsureOnboardingCompleted` Middleware (`app/Http/Middleware/EnsureOnboardingCompleted.php`)
- Registered as route middleware alias `'onboarding.completed'`.
- Intercepts admin requests for branches with uncompleted onboarding and redirects to `/admin/onboarding` (exempting `/admin/onboarding*`, logout, profile, and active branch switch routes).

---

## 4. How to Add a New Onboarding Step

1. **Add key constant to `OnboardingService::CHECKLIST_KEYS`:**
   ```php
   public const CHECKLIST_KEYS = [
       'institution_profile_completed',
       'academic_year_created',
       'package_selected',
       'teacher_added',
       'classroom_created',
       'custom_step_completed', // new step key
   ];
   ```
2. **Update `TOTAL_STEPS` constant:** Change `public const TOTAL_STEPS = 6;`.
3. **Add Controller method & Blade view** under `app/Http/Controllers/Admin/OnboardingController.php` and `resources/views/admin/onboarding/`.
4. **Update `x-onboarding.stepper` component** (`resources/views/components/onboarding/stepper.blade.php`).

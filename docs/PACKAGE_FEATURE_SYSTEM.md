# V1/V2/V3 Package & Feature Licensing System Architecture

## Overview

The **Package & Feature Licensing System** provides modular feature control for the Dershane SaaS platform. It allows platform administrators to toggle specific feature access per institution branch without requiring complex billing integration, user limits, or recurring subscription gateways.

---

## 1. Package Tier Definitions

The system natively defines 3 package tiers:

| Package Tier | Code | Target Audience & Goal | Active Core Features | Restricted Features |
|---|---|---|---|---|
| **Paket V1 — Başlangıç** | `V1` | Basic digital institution intro & core admin | `student`, `teacher`, `classroom`, `schedule` | `attendance`, `exam`, `homework`, `notification`, `guidance`, `finance`, `reports` |
| **Paket V2 — Profesyonel** | `V2` | Operational school with attendance & testing | V1 + `attendance`, `exam`, `homework`, `notification` | `guidance`, `finance`, `reports` |
| **Paket V3 — Enterprise** | `V3` | Full enterprise suite with guidance & finance | **All Features** (`student`, `teacher`, `classroom`, `schedule`, `attendance`, `exam`, `homework`, `notification`, `guidance`, `finance`, `reports`) | *None* |

---

## 2. Database Schema

### `packages`
Stores SaaS package tiers.
- `id`, `name`, `code` (`V1`, `V2`, `V3`), `description`, `price_yearly`, `price_3_year`, `status`, `timestamps`.

### `features`
Stores individual feature definitions.
- `id`, `name`, `code` (`student`, `attendance`, `exam`, etc.), `description`, `module`, `status`, `timestamps`.

### `package_features`
Pivot table attaching features to packages.
- `package_id`, `feature_id`.

### `branch_packages`
Tracks active package licenses assigned to branches.
- `id`, `branch_id`, `package_id`, `license_type` (`yearly`, `three_year`), `start_date`, `end_date`, `status` (`active`, `expired`, `cancelled`), `timestamps`.

---

## 3. Core Developer API & Helpers

### Global Helper Function: `feature_enabled()`
Check feature availability anywhere in PHP code or Blade views:

```php
if (feature_enabled('exam')) {
    // Perform exam-related operation
}

// Or check for a specific branch:
if (feature_enabled('finance', $branchId)) {
    // Branch has finance enabled
}
```

### Blade Conditional: `@feature_enabled()`
Hide/show UI elements cleanly in Blade templates:

```blade
@feature_enabled('attendance')
    <a href="{{ route('admin.attendance.index') }}">Yoklama Sistemi</a>
@endfeature_enabled
```

### Route Protection Middleware: `feature.access`
Protect route groups or resource routes using `feature.access:{feature_code}`:

```php
Route::middleware(['auth', 'feature.access:exam'])->group(function () {
    Route::resource('exams', ExamController::class);
});
```

When a restricted feature is accessed by a branch on a package without that feature, the user receives a 403 Forbidden page detailing package restriction.

---

## 4. How to Add a New Feature

1. **Add Feature to Seeder or Database:**
   ```php
   Feature::create([
       'name' => 'Canlı Ders Entegrasyonu',
       'code' => 'live_class',
       'description' => 'Online canlı ders odaları ve yayın takibi.',
       'module' => 'Education',
       'status' => 'active',
   ]);
   ```
2. **Attach Feature to Packages (V2/V3):**
   ```php
   $packageV3 = Package::where('code', 'V3')->first();
   $packageV3->features()->attach($feature->id);
   ```
3. **Protect Routes:**
   ```php
   Route::middleware(['feature.access:live_class'])->group(function () { ... });
   ```
4. **Protect Navigation Menu:**
   Add `'feature' => 'live_class'` to the menu array in `config/admin-menu.php`.

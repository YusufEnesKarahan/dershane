# SaaS Institution Settings & Configuration Management Architecture

## Overview

The **Institution Settings Management System** provides centralized configuration management for institutions and branches on the Dershane SaaS platform.

It allows school administrators to maintain general contact details, tax & billing info, brand assets (logo, favicon, primary/secondary colors), regional localization preferences (language, timezone), and notification options per branch.

---

## 1. Database Schema

### `institution_settings`
Primary configuration table per branch (uses `TenantScoped` and `SoftDeletes`).

- **Identification:** `id`, `branch_id`
- **General Info:** `institution_name`, `description`, `phone`, `email`, `address`, `city`, `district`, `website`
- **Billing & Tax:** `tax_number`, `invoice_information` (JSON: title, tax_office, tax_number)
- **Branding:** `logo` (storage path), `favicon` (storage path), `primary_color` (default `#4f46e5`), `secondary_color` (default `#0f172a`)
- **Regional & Local:** `timezone` (default `Europe/Istanbul`), `language` (default `tr`)
- **Notification Preferences:** `notification_preferences` (JSON: email_notifications, system_notifications, parent_notifications)
- **Timestamps:** `created_at`, `updated_at`, `deleted_at`

---

## 2. Domain Layer & Service API

### `InstitutionSettingService` (`app/Domain/Institution/Services/InstitutionSettingService.php`)

```php
use App\Domain\Institution\Services\InstitutionSettingService;

$service = app(InstitutionSettingService::class);

// Retrieve or initialize settings for a branch
$settings = $service->getSettings($branchId);

// Update general information & invoice details
$service->updateSettings($branchId, [
    'institution_name' => 'Kadıköy Çözüm Akademi',
    'phone' => '02164445566',
    'tax_number' => '1234567890',
]);

// Update branding & handle file uploads
$service->updateBranding($branchId, [
    'primary_color' => '#4f46e5',
    'secondary_color' => '#0f172a',
], $logoFile, $faviconFile);

// Update regional settings
$service->updateRegionalSettings($branchId, [
    'language' => 'tr',
    'timezone' => 'Europe/Istanbul',
]);

// Update notification preferences
$service->updateNotificationPreferences($branchId, [
    'email_notifications' => true,
    'system_notifications' => true,
    'parent_notifications' => false,
]);
```

---

## 3. Authorization & Permissions

- **Permissions:**
  - `institution.settings.view`: Allows viewing institution settings panel.
  - `institution.settings.update`: Allows updating institution settings.
- **Policy (`app/Policies/InstitutionSettingPolicy.php`):**
  - Admins (`Super Admin`, `Branch Admin`) or users with explicit `institution.settings.*` permissions have full access.
  - Non-admin roles (`Teacher`, `Student`, `Parent`) are blocked with HTTP 403 Forbidden.

---

## 4. Admin Settings UI

Accessible at `/admin/settings/institution` (Sidebar menu item under "Ayarlar").
Organized in 4 interactive tabs:
1. **Genel Bilgiler:** Kurum adı, açıklama, iletişim bilgileri, il/ilçe, vergi dairesi ve fatura ünvanı.
2. **Marka & Görsel:** Logo yükleme, Favicon yükleme, renk paleti seçicileri (color pickers).
3. **Bölge & Dil:** Sistem dili (TR/EN) ve saat dilimi.
4. **Bildirim Tercihleri:** E-posta, sistem içi ve veli portalı bildirim anahtarları.

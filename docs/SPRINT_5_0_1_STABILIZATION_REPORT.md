# Sprint 5.0.1 — Regression Stabilization Report

## 1. Yapılan kontroller

- Route registry, named-route uniqueness, route-to-controller reflection audit.
- Blade `route`, `to_route`, include/extends and component scans.
- Admin menu route and permission dictionary checks.
- Seeded MySQL üzerinde kritik controller/view render smoke testi.
- Repository interface-to-implementation compatibility check.
- Setting model and document repository structure audit.

## 2. Bulunan problemler

1. **Admission document repository binding (High):** `DocumentRepositoryInterface`, kendi sözleşmesini implement etmeyen `EloquentDocumentRepository`e bağlıydı. `admin.admission.dashboard` controller çözümlemesinde `TypeError` üretiyordu.
2. **CRM analytics null-status SQL (High):** `REGISTERED` lead statusu yokken CRM sorgusu boş kimlikle `lead_status_id =` SQL’i üretiyor ve dashboard'u 500’e düşürüyordu.
3. **Legacy Setting model (Low):** `App\Models\Setting`, migration tarafından kaldırılmış `settings` tablosunu varsayıyor. Canlı uygulama kodunda kullanılmıyor; aktif settings akışı `PlatformSetting` modelini kullanıyor.
4. **SQLite test-environment migration uyumsuzluğu (Low):** Test config SQLite kullanırken mevcut `pages` migrationı SQLite `DROP COLUMN` operasyonunda başarısız oluyor. Production hedefi MySQL olduğu için `migrate:fresh --seed` başarıyla tamamlandı. Bu sprintte migration değiştirilmedi.

## 3. Yapılan düzeltmeler

- `AdmissionDocumentRepository` eklendi. Mevcut `DocumentRepositoryInterface`, `UploadDocumentDTO` ve `AdmissionDocument` modelini doğrudan uygular.
- `RepositoryServiceProvider` binding’i admission repository implementasyonuna düzeltildi.
- CRM analytics aggregate sorguları parameter binding kullanacak şekilde güncellendi; eksik registered status artık sıfır dönüşüm sayısı üretir.
- Route health testleri; admission repository sözleşmesi, Blade include/extends, anonim Blade component ve menü permission dictionary regresyonlarını kapsayacak şekilde genişletildi.
- Kritik admin dashboard URL’leri için auth/middleware route smoke kapsamı genişletildi.

## 4. Değişen dosyalar

- `app/Core/Repositories/AdmissionDocumentRepository.php`
- `app/Providers/RepositoryServiceProvider.php`
- `app/Domain/CRM/Services/LeadAnalyticsService.php`
- `tests/Feature/RouteHealthTest.php`
- `docs/SPRINT_5_0_1_ROUTE_REGRESSION_REPORT.md`
- `docs/SPRINT_5_0_1_STABILIZATION_REPORT.md`

## 5. Test sonuçları

| Kontrol | Sonuç |
|---|---|
| Route audit | 307 route, 305 named route, 0 duplicate |
| Controller method audit | 0 missing target |
| Static route scan | 232 referans, 0 tanımsız |
| Include/extends scan | 4 referans, 0 eksik view |
| Component scan | 36 component, 0 eksik component |
| Menu permission audit | 19 farklı permission, 0 sözlük dışı değer |
| Seeded MySQL controller/view smoke | 11 kritik ekran başarılı |
| `RouteHealthTest` | 10 test, 687 assertion başarılı |
| `php artisan migrate:fresh --seed` | Başarılı (MySQL) |
| `php artisan test` | 20 test, 706 assertion başarılı |
| `npm run build` | Başarılı |
| `optimize:clear`, `route:clear`, `config:clear` | Başarılı |
| PHP syntax / diff check | Başarılı |

## 6. Kalan teknik borçlar

- Kullanılmayan legacy `Setting` / `SettingFactory` yapısı, gelecekte kaldırılmalı veya `PlatformSetting`e yönlendirilmeli.
- SQLite test database uyumluluğu için eski migration zinciri MySQL’e bağımlı ifadelerden arındırılmalı. Bu, ayrı bir test-infrastructure işi olmalı.
- Queue dashboard render’ında boş nullable kayıtlar nedeniyle Laravel/PHP deprecation uyarıları gözlendi; HTTP hatası üretmiyor, ancak queue history view verileri normalize edilmelidir.

## 7. Sprint 5.1 için öneriler

1. RBAC sprinti başlamadan önce Settings legacy yapısını kaldırma/uyumluluk planı oluşturun.
2. SQLite veya MySQL test stratejisini netleştirip migration portability testini ekleyin.
3. RBAC için role-permission matrix feature testleri ve gerçek admin/teacher/parent browser smoke testleri ekleyin.
4. Queue history ekranındaki nullable payload/name alanlarını presentation katmanında güvenli varsayılanlarla gösterin.

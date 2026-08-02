# Sprint 7.2 — SaaS Subscription & Billing Engine Raporu

## 📌 Hedef ve Kapsam
Bir önceki sprintte oluşturulan Plan ve License yapısı korunarak, sistemin gerçek bir SaaS gibi abonelik yaşam döngüsüne sahip olması (Billing Engine) sağlanmıştır. Ödeme entegrasyonu (Stripe vb.) hariç tutularak, plan yükseltme/düşürme ve deneme süresi yönetimi gibi tüm süreçler backend servisleriyle geliştirilmiştir.

## 🛠 Yapılan Değişiklikler

### 1. Abonelik (Subscription) Altyapısı
- **Tablo ve Modeller:** `subscriptions` ve `subscription_logs` tabloları eklendi. `Subscription` ve `SubscriptionLog` modelleri oluşturuldu.
- **İlişkiler:** Mevcut `License` modeli ile 1-N ve 1-1 (latest) ilişkiler kuruldu. 

### 2. SubscriptionService (İş Akışı Katmanı)
Tüm abonelik döngüsü `App\Domain\Platform\Services\SubscriptionService` içerisinde aşağıdaki metotlarla merkezi bir yapıya kavuşturuldu:
- **`startTrial(License, Plan, Gün)`:** Sistemi kullanan tenant'a ücretsiz deneme başlatır ve bu işlemi loglar.
- **`activateSubscription(License, Plan)`:** Deneme süresi bitiminde veya manuel tetiklemeyle lisansı "aktif" konuma alır ve döngüyü yeniler.
- **`changePlan(License, Yeni Plan)`:** Kullanıcı mevcut paketini Upgrade (Yükseltme) veya Downgrade (Düşürme) yaptığında yeni plana geçirir ve bunu `subscription_logs` tablosuna işlem türü ile (upgraded/downgraded) kaydeder.
- **`checkExpiredSubscriptions()`:** Cron tarafından çalıştırılmak üzere, bitiş tarihi geçmiş olan abonelikleri "expired" durumuna alır.

### 3. Onboarding & Limit Entegrasyonu
- **`InstallService`:** Kurulum tamamlandığında `SubscriptionService->startTrial` metodu çağrılarak kullanıcının otomatik olarak 14 günlük **Starter** paket aboneliğine başlatılması sağlandı. Bu sayede test ve kurulum aşamaları kesintisiz çalışmaktadır.

### 4. Admin Panel UI
- `admin/platform/licenses/index.blade.php` sayfasına "Abonelik Yönetimi" (Subscription Management) bloğu eklendi.
- **İşlem Geçmişi Tablosu:** Abonelik üzerinde yapılan her işlemin (başlangıç, upgrade vb.) tarihi ve açıklaması listelenmektedir.
- **Demo Butonlar:** "Denemeyi Bitir ve Aktifleştir" ve "Planı Değiştir" formları (ödeme olmadan) eklendi.

### 5. Yeni Eklenen Route'lar (`routes/admin.php`)
- `GET /admin/licenses` (licenses.index)
- `POST /admin/licenses/activate` (licenses.activate)
- `POST /admin/licenses/change-plan` (licenses.change-plan)

## 🗄 Migration Listesi
- `database/migrations/2026_08_02_152606_create_subscriptions_tables.php`

## ✅ Test Sonuçları
`SubscriptionTest.php` geliştirildi ve test paketine dahil edildi.

```text
php artisan test
46 tests, 123 assertions, 0 failures, 0 errors.
Duration: ~6.7s (Passed)
```

## 🏗 Mimari Sonuç
Domain Driven Design (DDD) yapısı korunmuş, `License` modeli merkezde kalacak şekilde bir "Abonelik" katmanı eklenmiştir. Modüller (HR, CRM vs.) yine `LicenseLimitService` üzerinden çalışmaya devam ettiği için kırılma yaşanmamıştır. Mevcut tüm testlerin başarılı olması bu izolasyonu kanıtlamaktadır.

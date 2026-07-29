# Sprint 7.0: HQ Central Administration Dashboard

## Genel Bakış
SaaS ERP instance'larını yönetmek üzere kurgulanan HQ Central Backend altyapısı, Sprint 7.0 itibarıyla gelişmiş bir **Admin Dashboard (Yönetim Paneli)** arayüzüne kavuşmuştur. Bu sprint sayesinde Super Admin'ler, kendilerine sunulan profesyonel ekranlar vasıtasıyla bağlı tüm sistemlerin canlı sağlık durumlarını (telemetry), komut geçmişlerini, iletişim sürekliliğini ve kayıtlı tenant'ları tek ekrandan izleyip yönetebilir.

## Öne Çıkan Geliştirmeler

### 1. Yetkilendirme (Policy & Gate)
- `App\Policies\HQPolicy` tanımlandı.
- `AuthServiceProvider` (Laravel 11 için `AppServiceProvider`) üzerinde `hq.viewDashboard`, `hq.manageTenant`, `hq.sendCommand` gate'leri oluşturularak yetki sadece **Super Admin** rolüne kilitlendi.
- Route'lar ve Controller'lar `Gate::authorize` üzerinden bu güvenlik kontrolünden geçmektedir.

### 2. Gelişmiş Metrik Servisi
- `App\Domain\HQ\Services\HQMonitoringService` baştan yazılarak kapsamlı veri sağlaması için geliştirildi:
  - **Sistem İstatistikleri:** Toplam, online, offline, bilinmeyen. (Eğer 15 dakikadan uzun süre heartbeat gelmezse otomatik offline sayılması sağlandı).
  - **Tenant Bilgileri:** Toplam müşteri / tenant sayısı, aktif / askıda istatistikleri.
  - **Haberleşme Sağlığı:** Son iletişim zamanı, başarısız bağlantı denemeleri, API hata yüzdesi, ortalama yanıt süresi (ms).
  - **Komut Kuyruğu:** Bekleyen (pending), tamamlanan (completed) ve başarısız (failed) komut toplamları.
  - **Telemetri Verileri:** Son 10 gönderilen telemetri datası parse edilerek Ortalama RAM (Memory) ve Disk (Storage) yüzdeleri Dashboard'a aktarıldı.

### 3. Controller Katmanı
SOLID ve Single Responsibility (SRP) gözetilerek yönlendirmeler bölündü:
- **`HQCentralController`**: Sadece Dashboard metriklerini hesaplar ve Dashboard Index sayfasını yükler.
- **`HQSystemController`**: Sisteme bağlı SaaS sistemlerinin genel listesini (`index`) ve tekil detaylı inceleme (`show`) sayfalarını sağlar.
- **`HQTenantController`**: Ana müşteri organizasyonlarının listelenmesi, yenilerinin eklenmesi (`store`) ve düzenlenmesi (`update`) sorumluluklarını alır.

### 4. Kullanıcı Arayüzü (UI - Blade)
Dark Mode uyumlu, responsive ve premium UX tasarım diline sadık kalınarak 4 yeni Blade arayüzü inşa edildi:
- **`admin.hq.index`**: 5 farklı genel bakış widget'ı ve alt kısımda özet sistem tablosu.
- **`admin.hq.systems.index`**: Pagination ile tüm instance'ların versiyon, tenant, çevre ve durum verilerinin listelendiği genel sistem defteri.
- **`admin.hq.systems.show`**: Özel bir sistemin son 5 telemetri snapshot'ını, veritabanı loglarını, komut geçmişini ve sağlık istatistiklerini izole gösteren detay sayfası.
- **`admin.hq.tenants.index`**: Tenant Create/Edit işlemlerinin sağlandığı Javascript destekli form tabanlı dinamik arayüz.

### 5. Test Senaryoları (`HQDashboardTest`)
Aşağıdaki kritik senaryolar başarılı şekilde testten geçirilmiştir:
1. `test_dashboard_access_and_authorization`: Yetkisiz / standart kullanıcı engellenmesi ve Super Admin yetkilendirilmesi (Status 403 ve Status 200).
2. `test_metrics_calculation_and_offline_detection`: 15 dakika kuralının tespiti ve bir sistemin otomatik Offline durumuna alınarak metric'lere %100 doğruluğunun yansıması.
3. `test_system_listing`: Eklenen dummy ERP kaydının UUID ve Limit(8) standartlarıyla index sayfasında render olması.
4. `test_tenant_listing_and_creation`: Yeni Tenant POST işleminin veritabanına başarılı kaydedilmesi ve yönlendirme kontrolü.

## Sonuç
Dershane ERP sisteminin bir şubeden çıkarak **bir "HQ Merkezi" platformuna dönüşümü**, profesyonel bir Admin Dashboard ve Monitörizasyon modülü ile arayüz açısından tamamlanmıştır. Sistem artık aktif olarak çoklu-SaaS yapısını görsel ve fonksiyonel olarak yönetmeye tamamen hazırdır.

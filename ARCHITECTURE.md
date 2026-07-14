# Architecture Decisions (Mimari Kararlar)

Bu belge, SaaS projesinin genişletilmiş mimari yapısını ve tasarım kararlarını detaylandırır.

## 1. Servis Sağlayıcılar (Service Providers)
Sistem modülerliği ve başlatma (booting) hızını artırmak amacıyla sorumluluklarına göre ayrılmış 5 adet yeni servis sağlayıcı entegre edilmiştir:
- **`RepositoryServiceProvider`:** Veri erişim soyutlamalarını ve Eloquent somut sınıflarını (concrete classes) bağlar.
- **`FeatureServiceProvider`:** Özellik yönetimi ve `FeatureManager` servislerini bağlar.
- **`ThemeServiceProvider`:** Kurumsal white-label tema yönetimi ve `ThemeManager`, `BrandManager` sınıflarını yönetir.
- **`SettingsServiceProvider`:** Sistem genelindeki yapılandırmaları ve `SettingsManager`ı yönetir.
- **`DomainServiceProvider`:** Alt iş etki alanlarının (sub-domains) kayıt ve başlatılmasından sorumludur.

## 2. Temel Sınıf Sınırları (Base Classes)
Tüm katmanların standart davranışlar sergilemesi ve tip güvenliğinin (type safety) artırılması için `App\Core\Base\` altında temel sınıflar oluşturulmuştur:
- **`BaseService`:** İş mantığını barındıran tüm servislerin atasını oluşturur.
- **`BaseRepository`:** Veritabanı sorgulamalarını soyutlayan sınıfların ortak metodlarını barındırır.
- **`BaseAction`:** Tek sorumluluk taşıyan `Action` sınıflarının (Command Pattern) sınırlarını çizer.
- **`BaseDTO`:** Veri transfer nesnelerinin otomatik dizi dönüşümleri (`toArray()`) için kullanılır.
- **`BaseViewModel`:** View katmanına sunulacak verileri filtreleyen sınıfların (`Arrayable`) yapısını tanımlar.
- **`BaseException`:** Uygulamaya özgü özel hataların (custom exceptions) atasını oluşturur.
- **`BasePolicy`:** Yetkilendirme sınıfları için temel oluşturur.

## 3. Yönetici Servisleri (Managers)
Sistem genelindeki konfigürasyon dosyalarını okumak ve bunları tip güvenliğine uygun şekilde sarmalamak için **Manager** sınıfları geliştirilmiştir:
- `EditionManager` (Basic/Professional/Ultimate paket yönetimi)
- `FeatureManager` (Özellik kontrolü)
- `ThemeManager` (Tema stil ve sayfa kontrolü)
- `SettingsManager` (Sistem parametreleri yönetimi ve Whitelabel şirket verileri)
- `BrandManager` (White-label marka ayarları kontrolü)

## 4. Küresel Yardımcılar (Global Helpers)
Geliştiricilerin ortak işlevlere en kısa ve temiz yolla erişmesini sağlamak amacıyla global helper'lar (`Helpers.php` ve `SaaS.php`) eklenmiştir:
- `edition()` (SaaS EditionManager nesnesi)
- `edition_name()` (Aktif paket adı gösterimi)
- `edition_color()` (Pakete özgü CSS renk kodu)
- `edition_badge()` (Pakete özgü HTML rozet çıktısı)
- `feature()` (SaaS özellik durumu denetimi)
- `setting()` (Sistem konfigürasyon verisi)
- `brand()` (Marka bilgisi)
- `theme()` (Tema düzeni kontrolü)
- `tenant()` (Aktif tenant bilgisi - çoklu kiracılık için)
- `money()` (Para birimi biçimlendirme - `TRY` / `₺` uyumlu)
- `active_menu()` (Menü aktiflik durumu CSS sınıfı ekleyicisi)

## 5. Domain Odaklı Yapılanma (Domains)
İş kurallarının modülerleşmesini sağlamak amacıyla `app/Domains/` altında etki alanı klasörleri oluşturulmuştur:
- `Shared` (Ortak kullanılan iş kodları)
- `CMS` (İçerik yönetim sistemleri)
- `Education` (Eğitim, ders ve müfredat işleri)
- `CRM` (Müşteri ilişkileri yönetimi)
- `ERP` (Kurumsal kaynak planlama altyapısı)
- `Core` (Sistem alt yapısı)

## 6. Veritabanı ve Şema Mimarisi (Database & Schema Architecture)
Sistem veritabanı şeması tasarlanırken veri yalıtımı, bütünlük ve paket sürümlerinin ihtiyaçları doğrultusunda şu kararlar alınmıştır:
- **Şube Tabanlı Yalıtım:** Tüm eğitim profilleri ve kayıt verileri `branches` tablosuna yabancı anahtar (`branch_id`) ile bağlanarak şubeler arası veri izolasyonu sağlanmıştır.
- **PHP Enums Uyumu:** SQLite ve MySQL arasındaki geçişleri kolaylaştırmak amacıyla, veritabanı seviyesinde `enum` kolon tipi yerine `varchar` kullanılmış ve durum kontrolleri uygulama katmanında tip güvenli PHP Enums ile sınırlandırılmıştır.
- **Güvenli Silme (Soft Deletes):** Öğrenci, Veli, Öğretmen ve Kayıt gibi kritik tablolarda `deleted_at` kullanılarak veri kayıplarının önüne geçilmiştir.
- **Bütünlük Kısıtları (Constraints):** Şube, Derslik ve Kurs gibi ana tablolar silinirken ilişkili aktif verilerin bozulmaması için `ON DELETE RESTRICT` kullanılırken; ders saat planları veya ödevler silindiğinde alt kayıtların otomatik temizlenmesi amacıyla `ON DELETE CASCADE` tercih edilmiştir.

## 7. Arayüz ve Kullanıcı Deneyimi Mimarisi (UI/UX Architecture)
Uygulamanın arayüz ve kullanıcı deneyimi yerleşimleri (layouts) şu standartlara göre yönetilir:
- **Temiz Rota Dağılımı:** Web sitesi ve Admin rotaları modüler olarak ayrılmış olup, rota bazlı paket yetkilendirmesi `EDITION_STRATEGY.md` kılavuzuna göre middleware katmanı ile çözülür.
- **İçerik ve Bilgi Hiyerarşisi:** Sayfaların başlık, içerik bloğu, SEO meta alanları ve eylem çağrıları (CTA) standart şablon yerleşimlerine (Page Blueprint) göre yerleştirilir.
- **Görsel Standartlar:** Arayüze yüklenecek tüm medya ve görseller `MEDIA_GUIDE.md` dosyasında yer alan en-boy oranlarına ve WebP format standartlarına tabi tutulur.
- **Marka Özelleştirmeleri (White-label):** Şirket adı, logosu, favicon, birincil/ikincil renkler ve iletişim verileri doğrudan kod içerisine yazılmak yerine `SettingsManager` üzerinden okunarak dinamik olarak yüklenir.




## Data Access Layer
- **Repositories:** Abstract DB queries.
- **Services:** Handle business logic.
- **DTOs:** Standardize data passing.

## Authentication Domain
- **AuthManager:** Post-login context loading.
- **Actions:** Encapsulate auth logic (LoginAction, LogoutAction).

## Authorization & RBAC
- **PermissionCache:** Eliminates DB queries during checks.
- **AuthorizationService:** Central point for permission verification.

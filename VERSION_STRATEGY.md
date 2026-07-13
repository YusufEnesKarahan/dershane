# Version Strategy (Sürüm Stratejisi)

Bu proje tek bir kod tabanında (single codebase) geliştirilmekte olup 3 farklı ticari paket sürümünü destekleyecek şekilde tasarlanmıştır:
- **Version 1 (V1):** Kurumsal Web Sitesi (Tanıtım, İletişim, Kayıt Altyapısı)
- **Version 2 (V2):** Kurumsal + Temel Yönetim Paneli
- **Version 3 (V3):** Full Eğitim ERP (Rehberlik, Finans, Ders Planlama vb.)

## Sürüm Yönetimi Mimarı Yaklaşımı

### 1. Feature Flags (Özellik Bayrakları)
- Uygulama genelinde hangi özelliklerin aktif olacağı `config/features.php` içerisindeki flag'ler ile belirlenir.
- Her paket sürümü, belirli bir flag kümesini aktif kılar.
- Kod içerisinde doğrudan paket isimleri kontrol edilmek yerine, özellik bazlı flag'ler kontrol edilir:
  ```php
  if (Feature::isActive('finance-management')) {
      // V3'e özel finans işlemleri
  }
  ```

### 2. Middleware Sınırlandırması
- Route grupları, ilgili pakete veya özelliğe göre middleware korumasına alınır.
- `FeatureFlagMiddleware` ile lisanslı olmayan paket sayfalarına erişim engellenir ve kullanıcı uygun bir hata sayfasına (403/upgrade) yönlendirilir.

### 3. Route Yapılandırması
Route dosyaları temizlik ve modülerlik açısından ayrılmıştır:
- `routes/web.php` (Ortak ve V1 Kurumsal Site route'ları)
- `routes/admin.php` (V2 ve V3 Yönetim/ERP route'ları)

### 4. Veritabanı Şeması
- Tek bir veritabanı şeması kullanılır.
- V2 ve V3 paketlerine özel tablolar veya sütunlar, ilgili paket aktif olmadığında sorgulanmaz veya boş bırakılır.
- Veritabanı tablolarında paket ayrımını sağlayacak veya isteğe bağlı (nullable) alanlar mimariye uygun şekilde tasarlanacaktır.

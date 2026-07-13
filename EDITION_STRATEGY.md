# Edition Strategy (Paket ve Lisans Stratejisi)

Bu proje tek bir ortak kod tabanında (single codebase) geliştirilmekte olup 3 farklı ticari paket seviyesini (Edition) destekleyecek şekilde tasarlanmıştır:
- **Basic Edition:** Kurumsal Web Sitesi (Tanıtım, İletişim, Online Ön Kayıt Altyapısı)
- **Professional Edition:** Kurumsal Web Sitesi + Yönetim Modülleri (Öğrenci, Öğretmen, CRM ve Kurs Planlama)
- **Ultimate Edition:** Tam Eğitim ERP Paketi (Ödev Takibi, Yoklama, Belge Yönetimi ve Bildirim Altyapısı)

---

## 1. Feature -> Edition Eşleşme Tablosu

Aşağıdaki tablo, hangi özelliğin hangi paket seviyesinde aktif olduğunu gösteren lisans matrisidir:

| Modül / Özellik | Basic Edition | Professional Edition | Ultimate Edition | Açıklama |
| :--- | :---: | :---: | :---: | :--- |
| **Website** | ✅ | ✅ | ✅ | Kurumsal tanıtım sayfaları, hakkımızda ve dinamik SEO uyumlu tasarım. |
| **Blog** | ✅ | ✅ | ✅ | Duyurular, makaleler, rehberlik içerikleri. |
| **Gallery** | ✅ | ✅ | ✅ | Fiziki ortam, butik sınıflar ve laboratuvar görsel galerisi. |
| **Contact** | ✅ | ✅ | ✅ | İletişim bilgileri, şubeler ve harita entegrasyonu. |
| **Registration** | ✅ | ✅ | ✅ | Ziyaretçiler için online ön kayıt başvuru altyapısı. |
| **Student** | ❌ | ✅ | ✅ | Öğrenci kayıt kabul, detaylı profil kartları ve arşivi. |
| **Teacher** | ❌ | ✅ | ✅ | Öğretmen atamaları, zümre yönetimi ve ders yükü takibi. |
| **Course** | ❌ | ✅ | ✅ | Ders programları (YKS, LGS, ara sınıflar) ve grup tanımları. |
| **Lesson** | ❌ | ✅ | ✅ | Haftalık ders programı planlama ve derslik atamaları. |
| **CRM** | ❌ | ✅ | ✅ | Ön kayıt aday takipleri, arama kayıtları ve satış hunisi. |
| **Attendance** | ❌ | ❌ | ✅ | Günlük yoklama takibi, devamsızlık istatistikleri ve veli raporlama. |
| **Homework** | ❌ | ❌ | ✅ | Ödev dağıtımı, teslim kontrolü ve öğretmen değerlendirmeleri. |
| **Documents** | ❌ | ❌ | ✅ | Çalışma yaprakları, pdf kaynakları ve ders notları paylaşım kütüphanesi. |
| **Notifications** | ❌ | ❌ | ✅ | SMS, e-posta ve anlık sistem içi bilgilendirme servisleri. |

---

## 2. Edition Yönetim Mimarisi

### 2.1 EditionManager & Feature Flags
- Uygulama genelinde hangi paket düzeyinin aktif olacağı `config/features.php` içerisindeki `edition` anahtarından okunur.
- `EditionManager` sınıfı (`app/Core/Services/EditionManager.php`) aracılığıyla aktif paketin Basic, Professional veya Ultimate olduğu doğrulanır:
  ```php
  if (edition()->isProfessional()) {
      // Professional pakete özel ekranlar
  }
  ```
- Özellik kısıtlamaları doğrudan paket adı yerine modül ismiyle kontrol edilir (Feature Flags):
  ```php
  if (edition()->has('crm')) {
      // CRM özellikleri
  }
  ```

### 2.2 Blade Directives
Bileşenlerin veya HTML bölümlerinin paket yetkilerine göre render edilmesi için `@edition` directive'leri kullanılır:
```html
@edition('professional')
    <a href="/crm">CRM Paneli</a>
@endedition

@edition('ultimate')
    <div class="alert">Yoklama Raporları</div>
@endedition
```

### 2.3 Rota ve Middleware Yönetimi
- Route grupları `FeatureFlagMiddleware` ile koruma altına alınır.
- Paket harici bir rotaya girildiğinde, sistem HTTP 403 (Upgrade required) hata sayfasını gösterir.

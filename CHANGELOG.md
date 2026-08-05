# Changelog (Değişiklik Günlüğü)

Tüm önemli değişiklikler bu dosyada belgelenecektir.

---

## [1.0.0] — 2026-08-05

### Added (Eklenenler)
- **Multi-Tenant & Branch İzolasyonu**: Her dershane için ayrı veritabanı benzeri veri izolasyonu ve şube bazlı veri koruma (Eloquent global scopes ve Spatie Permission integration).
- **Onboarding Sihirbazı (5 Adım)**: Yeni dershaneler için adımlı kurulum akışı (Kurum bilgileri, Yönetici hesabı, İlk şube kuruluşu, Paket seçimi ve otomatik Demo Veri yükleme).
- **Yönetici ve Şube Dashboard'ları**: Admin, Öğretmen, Öğrenci ve Veli portalleri için özelleştirilmiş, anlık KPI istatistikleri sunan gelişmiş paneller.
- **Rol ve Yetkilendirme (RBAC)**: Super Admin, Branch Admin, Teacher, Student ve Parent rolleri ve bunlara ait ayrıntılı Spatie izin şemaları.
- **Akademik Yönetim Suite**: Öğrenci ve Öğretmen yaşam döngüsü yönetimi, Ders programları (Lesson Schedules), Sınıf ve Ders yönetimi.
- **Sınav ve Ödev Modülleri**: Sınav yönetimi, sınav sonuç karneleri (kapsamlı net ve puan hesaplamalarıyla), ödev dağıtımı ve öğrenci teslim değerlendirme akışı.
- **Manuel Lisanslama & Limit Kısıtlamaları**: Tamamen manuel yönetilen paket limitasyonları (maksimum öğrenci, öğretmen, sınıf sayıları) ve lisans süresi denetimleri.
- **Platform Loglama**: Kullanıcı ve sistem işlemlerinin takip edilebildiği Activity Logs altyapısı.
- **Production-Ready Cache & Güvenlik Ayarları**: HTTPOnly cookie'ler, Lax SameSite, JSON oturum serileştirme ve 100% route/config önbellekleme yeteneği.

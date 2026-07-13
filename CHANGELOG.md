# Changelog (Değişiklik Günlüğü)

Tüm önemli değişiklikler bu dosyada belgelenecektir.

## [Sprint 0] - 2026-07-13

### Added
- Proje temeli Laravel 12 ve PHP 8.4 ile kuruldu.
- Feature-First ve Domain-Driven mimariye uygun klasör yapısı oluşturuldu (`app/Core`, `app/Features`, `app/Domains` vb.).
- Teknik dokümantasyonlar hazırlandı (`README.md`, `PROJECT_RULES.md`, `DEVELOPMENT_GUIDE.md` vb.).
- White-label theming altyapısı için `config/brand.php` dosyası oluşturuldu.
- Paket sürümlerini yönetmek için Feature Flag altyapısı ve `config/features.php` oluşturuldu.
- Blade şablon yapısı ve boş frontend/admin/auth/guest layout'ları hazırlandı.
- Blade temel arayüz bileşenleri oluşturuldu (button, card, modal, input vb.).
- Dokümantasyon klasörleri ve alt dizinleri yapılandırıldı.

### Removed
- Laravel varsayılan hoş geldiniz (`welcome.blade.php`) sayfası silindi.
- Gereksiz varsayılan içerikler temizlendi.

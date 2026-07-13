# Coding Standards (Kodlama Standartları)

Bu doküman, projede yazılan tüm kodların tutarlılığını ve kalitesini güvence altına almak için uyulması gereken standartları detaylandırır.

## 1. PHP Standartları
- **Strict Types:** Tüm PHP dosyaları kesinlikle `declare(strict_types=1);` ile başlamalıdır.
- **Tip Belirtimi (Type Hinting):** Metot parametreleri ve dönüş değerleri (return types) kesinlikle tip belirtilerek yazılmalıdır. Nullable değerler için uygun `?type` veya `type|null` yapıları kullanılmalıdır.
- **PHP 8.4 Özellikleri:** PHP 8.4 ile gelen yeni sözdizimi özellikleri (Property Promotion, Constructor Property Promotion, Match ifadeleri vb.) aktif olarak kullanılmalıdır.

## 2. İsimlendirme Felsefesi
- **Anlamlı İsimlendirme:** Kod içerisinde satır içi açıklayıcı yorumlar yazmak yerine, değişken, metot ve sınıf isimleri işlevini tam olarak belirtecek şekilde seçilmelidir (Self-documenting code).
  - *Kötü:* `public function calc($d); // indirim hesaplar`
  - *İyi:* `public function calculateDiscountedPrice(float $originalPrice): float;`
- **Kısaltmalardan Kaçınma:** Anlaşılmayı zorlaştıran kısaltmalar yerine kelimelerin tam halleri tercih edilmelidir.

## 3. Kod Biçimlendirme ve Araçlar
- **Laravel Pint:** Kod tabanındaki tüm dosyalar Laravel Pint standartlarına (default Laravel preset) göre biçimlendirilmelidir.
- **PHPStan Seviyesi:** Statik analizde Seviye 8 hedeflenmeli, tip uyumsuzlukları ve potansiyel hatalar geliştirme aşamasında çözülmelidir.

## 4. Blade & CSS Standartları
- **Blade Dosyaları:** HTML elemanları hiyerarşik ve düzgün girintilenmiş olmalıdır. Blade direktifleri (`@if`, `@foreach`, `@extends`) standart şekilde kullanılmalıdır.
- **Tailwind CSS:** Dynamic CSS class'ları oluştururken Tailwind sınıfları doğrudan yazılmalıdır. Mümkün olduğunca `@apply` kullanımından kaçınılmalı, tasarım sistemi CSS değişkenleri üzerinden yönetilmelidir.

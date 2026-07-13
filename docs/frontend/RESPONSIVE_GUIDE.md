# Responsive Guide (Duyarlı Tasarım Kılavuzu)

Arayüzlerin mobil cihazlardan büyük ekranlı masaüstü bilgisayarlara kadar kusursuz görünmesi için **Mobile-First** geliştirme yaklaşımı benimsenmiştir.

## 1. Breakpoint Standartları (Responsive Eşik Değerleri)
Kullanılan ekran genişlik kırılımları (Tailwind default değerleri):

- **Mobile (Varsayılan):** `< 640px` (Tek sütunlu dikey yerleşimler).
- **Tablet (sm):** `640px` (Çift sütunlu kartlar, daralan tablolar).
- **Notebook (md):** `768px` (Navigasyon menüsü açılımı, 3 sütunlu yapılar).
- **Desktop (lg):** `1024px` (Standart masaüstü, yan menüler, 4 sütunlu kartlar).
- **Wide Desktop (xl):** `1280px` (Maksimum içerik genişlik alanı).

## 2. Maksimum Genişlik ve Hizalamalar
- Genel içerik kapsayıcıları için `.max-w-7xl` (`1280px`) kullanılacaktır.
- Sayfa yan marjları mobil cihazlarda `px-4`, tablet ve üzerinde ise `sm:px-6 lg:px-8` olarak ayarlanır.

## 3. Tablo ve Veri Gösterim Kuralları
- Geniş veri tabloları mobil ekranlarda kırılmayı önlemek amacıyla mutlaka yatay kaydırma kapsayıcısına (`.overflow-x-auto`) alınacaktır.
- Çok sütunlu listeler mobil cihazlarda dikey akışa (flex-direction: column) dönecektir.

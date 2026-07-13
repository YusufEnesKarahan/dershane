# Color System (Renk Sistemi)

Renk paleti, kurumsal kimliği yansıtırken aynı zamanda kurumların kendi renklerini kullanabilmesi (White-Label) için CSS Değişkenleri (`CSS Variables`) tabanlı olarak tasarlanmıştır.

## 1. Dinamik White-Label Renkleri
Birincil ve ikincil renkler doğrudan veritabanından veya kurum config'inden okunarak HTML `:root` seviyesinde enjekte edilir.

- **Primary (Birincil):** `var(--brand-primary, #4f46e5)` (varsayılan: Indigo-600)
  - Uygulamanın ana eylem butonlarında, aktif menülerde ve marka vurgularında kullanılır.
- **Secondary (İkincil):** `var(--brand-secondary, #0891b2)` (varsayılan: Cyan-600)
  - Tamamlayıcı eylemlerde, ikincil odak alanlarında ve görsel detaylarda kullanılır.

## 2. Durum Renkleri (Status Colors)
Durum bildiren mesajlar ve rozetler için kullanılan sabit renk paleti:

- **Success (Başarı):** `#10b981` (Yeşil-500)
  - Olumlu durumlar, başarıyla tamamlanan işlemler, aktif kayıtlar.
- **Warning (Uyarı):** `#f59e0b` (Sarı-500)
  - Dikkat edilmesi gereken durumlar, bekleyen onaylar.
- **Danger (Tehlike/Hata):** `#ef4444` (Kırmızı-500)
  - Hatalı işlemler, iptal edilmiş veya silinmiş kayıtlar.
- **Info (Bilgi):** `#3b82f6` (Mavi-500)
  - Kullanıcıya gösterilen sistem bilgilendirmeleri.

## 3. Nötr Renkler (Neutral & Backgrounds)
Sitenin iskeletini ve yazı kontrastını oluşturan gri tonları:

- **Neutral (Koyu Gri):** `#1f2937` (Zemin yazıları ve koyu başlıklar).
- **Muted (Açık Gri Yazı):** `#6b7280` (Yardımcı yazılar ve pasif açıklamalar).
- **Surface (Yüzey):** `#ffffff` (Kartların, modalların ve formların arka planı).
- **Background (Arka Plan):** `#f9fafb` (Sayfaların genel arka plan rengi).

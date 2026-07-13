# Design System (Tasarım Sistemi)

Bu doküman, projenin arayüzlerinde (frontend & admin paneli) görsel bütünlüğü, tutarlılığı ve white-label esnekliğini sağlamak üzere kurgulanan tasarım sisteminin çekirdek kurallarını açıklar.

## 1. Tasarım Felsefesi
Sistem; modern, kurumsal, premium ve hızlı bir kullanıcı deneyimi sunmayı hedefler. Apple, Stripe, Linear ve Vercel gibi modern dijital platformların sade, geniş boşluklu (whitespace), yumuşak gölgeli ve tipografi odaklı tasarım dillerinden ilham almaktadır.

## 2. Spacing Scale (Boşluk Ölçeği)
Boşluklar, **4px grid sistemine** dayanır. Bu ölçek, tüm elemanlar arasındaki dış boşlukları (margin) ve iç boşlukları (padding) düzenler:

- **1 (4px):** Mikro hizalamalar, çok sıkışık detaylar.
- **2 (8px):** Küçük öğeler arası boşluklar.
- **3 (12px):** Form elemanı ve etiket arası boşluklar.
- **4 (16px):** Küçük kart iç boşlukları, standart buton padding'leri.
- **5 (20px):** Orta seviye grup boşlukları.
- **6 (24px):** Standart kart iç boşlukları, düzen kenar marjları.
- **8 (32px):** Büyük bileşen grupları.
- **10 (40px) / 12 (48px) / 16 (64px) / 20 (80px) / 24 (96px):** Bölüm (Section) marjları.

## 3. Border Radius (Kenar Yumuşatma)
Modern bir görünüm sunmak amacıyla kenar köşe yuvarlatmaları aşağıdaki ölçekte sabitlenmiştir:

- **xs (4px):** Çok küçük elemanlar (etiketler, küçük rozetler).
- **sm (8px):** Küçük form girdileri, küçük butonlar.
- **md (12px):** Standart butonlar, form elemanları, küçük kartlar.
- **lg (16px):** Standart kartlar, uyarı kutuları, modallar.
- **xl (24px):** Büyük tanıtım kartları, bannerlar.
- **full (9999px):** Tam yuvarlak (profil resimleri, hap butonlar).

## 4. Shadow System (Gölgeler)
Premium derinlik hissi kazandırmak amacıyla çok hafif ve yumuşak gölgelendirmeler kullanılır:

- **sm:** `0 1px 2px 0 rgba(0, 0, 0, 0.05)` (butona hafif taban derinliği).
- **md:** `0 4px 6px -1px rgba(0, 0, 0, 0.05), ...` (standart kartlar için).
- **lg:** `0 10px 15px -3px rgba(0, 0, 0, 0.04), ...` (üzerine gelinen kartlar).
- **xl:** `0 20px 25px -5px rgba(0, 0, 0, 0.05), ...` (modallar ve açılır menüler).

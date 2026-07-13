# Typography System (Tipografi Sistemi)

Projenin tipografisi, metinlerin hiyerarşik olarak taranabilirliğini (scannability) kolaylaştıracak şekilde tasarlanmıştır.

## 1. Yazı Tipi Ailesi (Font Family)
- **Ana Yazı Tipi:** `Inter` (varsayılan kurumsal font).
- **Yedek Yazı Tipi:** `ui-sans-serif, system-ui, sans-serif` (tarayıcı uyumluluğu için).
- Marka ayarlarından (`config/brand.php`) gelen `--brand-font` değeri ile dinamik olarak değiştirilebilir.

## 2. Tipografi Ölçeği

| Ölçek Adı | Font Boyutu | Line Height | Font Weight | Kullanım Alanı |
| :--- | :--- | :--- | :--- | :--- |
| **Display** | 3.75rem (60px) | 1.0 | 800 (Extrabold) | Hero başlıkları, büyük bannerlar |
| **Heading** | 2.25rem (36px) | 1.1 | 700 (Bold) | Sayfa ana başlıkları |
| **Title** | 1.25rem (20px) | 1.2 | 600 (Semibold) | Kart başlıkları, modal başlıkları |
| **Subtitle**| 1.0rem (16px) | 1.4 | 400 (Regular) | Başlık altı yardımcı açıklamalar |
| **Body** | 0.875rem (14px)| 1.5 | 400 (Regular) | Paragraflar, form metinleri, listeler |
| **Small** | 0.75rem (12px) | 1.5 | 500 (Medium) | Rozetler, küçük etiketler, dipnotlar |
| **Caption** | 0.625rem (10px)| 1.5 | 500 (Medium) | Meta bilgiler, çok küçük açıklamalar |

## 3. Harf Boşluğu (Letter Spacing)
- Büyük başlıklar (Display, Heading) için harfler arası mesafe hafif daraltılır: `tracking-tight` (`-0.025em`).
- Rozet ve küçük etiketler için harfler arası mesafe hafif açılır: `tracking-wider` (`0.05em`).

# SEO Strategy (SEO Stratejisi)

Bu doküman, kurumsal web sitesinin arama motorlarında görünürlüğünü en üst seviyeye taşımak amacıyla uygulanacak SEO kurallarını tanımlar.

## 1. URL Standartları ve Slug Yapısı
- Rotalar her zaman okunabilir, anlamlı ve küçük harflerle (kebab-case) yazılacaktır.
- Özel Türkçe karakterler otomatik olarak İngilizce karşılıklarına dönüştürülür (örn: `ı -> i`, `ş -> s`, `ğ -> g`).
  - *Doğru:* `https://domain.com/kurslar/tyt-sayisal-hazirlik`
  - *Yanlış:* `https://domain.com/Kurslar/tyt_sayisal_hazirlik?id=12`
- URL yapılarında hiyerarşik derinlik 3 seviyeyi geçmemelidir: `/kategori/alt-kategori/sayfa-slug`

---

## 2. Meta Alanları ve Dinamik Yapı
Her dinamik veya statik sayfa için aşağıdaki meta verileri veritabanından veya sayfa bazlı kontrol edilerek HTML `<head>` içerisine basılır:

- **Meta Title:** Maksimum 60 karakter olmalıdır. Sayfa başlığı ve kurum adını içerir: `[Sayfa Başlığı] | [Kurum Adı]`
- **Meta Description:** Maksimum 155 karakter olmalıdır. Sayfa içeriğini özetleyen, tıklamaya teşvik edici bir açıklama barındırır.
- **Keywords:** Arama motorları tarafından artık dikkate alınmadığı için şablonda yer almayacaktır (gereksiz kod yükü).

---

## 3. OpenGraph ve Sosyal Paylaşım Entegrasyonu
Sosyal ağlarda (Facebook, Twitter, WhatsApp vb.) paylaşıldığında zengin kart görünümü sunabilmek için şu OpenGraph meta etiketleri kullanılacaktır:

```html
<meta property="og:title" content="[Dinamik Sayfa Başlığı]" />
<meta property="og:description" content="[Dinamik Açıklama]" />
<meta property="og:image" content="[Dinamik Görsel URL'si (Blog Kapak / Şube Görseli)]" />
<meta property="og:url" content="[Mevcut Sayfa URL'si]" />
<meta property="og:type" content="website" />
```

---

## 4. Canonical ve Yinelenen İçerik Yönetimi
- Her sayfanın en üstünde mutlaka o sayfanın orijinal adresini gösteren canonical etiketi yer almalıdır:
  ```html
  <link rel="canonical" href="{{ request()->url() }}" />
  ```
- Sayfalama (`?page=2`) veya arama filtreleri içeren sayfalarda canonical her zaman ana listeleme sayfasını göstermelidir.

---

## 5. Yapılandırılmış Veri (JSON-LD Schemas)
Arama sonuçlarında zengin snippet'lar (rich snippets) elde etmek amacıyla sayfalara gömülü JSON-LD şemaları eklenecektir:
- **LocalBusiness:** Ana sayfa ve iletişim sayfasına kurum adresini, telefonunu ve çalışma saatlerini arama motorlarına bildirmek için eklenir.
- **Course:** Kurs detay sayfalarına kurs müfredatını, süresini ve eğitici bilgilerini eklemek için entegre edilir.
- **Article (BlogPosting):** Blog detay sayfalarında yazar, yayınlanma tarihi ve makale başlığını arama motorlarına sunar.

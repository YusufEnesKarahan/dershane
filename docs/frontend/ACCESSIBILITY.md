# Accessibility Guide (Erişilebilirlik Kılavuzu)

Sistemin, engelli kullanıcılar dahil herkes tarafından kolayca kullanılabilmesi için **WCAG 2.1 AA** erişilebilirlik standartları temel alınmıştır.

## 1. Klavye Navigasyonu ve Odaklanma (Focus State)
- Fare kullanmayan kullanıcıların klavye (Tab tuşu) ile tüm sayfa elemanlarında gezinebilmesi güvence altına alınmıştır.
- Odaklanan (focus) her eleman için belirgin bir görsel odak halkası (`focus:ring-2 focus:ring-offset-2`) sağlanmıştır. Tarayıcının varsayılan odak halkasını gizleyen `outline-none` sınıfları mutlaka özel bir focus stili ile ikame edilmelidir.

## 2. ARIA Rolleri ve Nitelikleri
- İnteraktif bileşenler (Modal, Alert, Dropdown vb.) doğru ARIA rolleri ile donatılacaktır.
- Modallar için `role="dialog"` ve `aria-modal="true"` kullanılmalıdır.
- Hata mesajları veya dinamik uyarılar için `role="alert"` ve `aria-live="polite"` tanımlanacaktır.
- Form elemanlarında `id` ve `aria-describedby` eşleşmesi ile ekran okuyucuların hata mesajlarını okuması sağlanacaktır.

## 3. Renk Kontrastı (Contrast Ratio)
- Metin renkleri ile arka plan renkleri arasındaki kontrast oranı en az **4.5:1** (büyük metinler için **3:1**) olmalıdır. 
- Rozetler (Badge) veya Alert bileşenlerinde kullanılan arka plan renkleri kontrast kaybına uğramayacak şekilde koyu yazı renkleri ile eşleştirilmiştir (`bg-green-100 text-green-800` gibi).

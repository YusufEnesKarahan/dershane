# UI Rules (Arayüz Kuralları)

Bu doküman, arayüz tasarlanırken boşluk yönetimi, sayfa hiyerarşisi ve kurumsal kimlik tutarlılığı için uyulması gereken temel görsel kuralları açıklar.

## 1. Sayfa Yerleşimi ve Kılavuz Çizgiler (Layout Rules)
- **Grid Container:** Tüm içerik sayfaları `.max-w-7xl` (`1280px`) sınırları içerisine ortalanacaktır.
- **Section Boşlukları:** Sayfadaki dikey bölümler (sections) arasına `py-12` (`48px`) veya `py-16` (`64px`) boşluk verilecektir.
- **Kart Gridleri:** Kart listeleri (örn: Kurs listesi, öğretmen listesi) masaüstü ekranlarda `grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6` şeklinde konumlandırılır.

## 2. Buton ve Eylem Kuralları
- **Hizalama:** Formların sonundaki onay butonları her zaman sağa hizalanacaktır (flex justify-end).
- **Renk Hiyerarşisi:** Sayfada veya formda yalnızca bir adet "Primary" buton yer alabilir. İkincil eylemler için mutlaka "Outline" veya "Ghost" butonlar tercih edilmelidir.

## 3. Form Düzenleri (Form Layouts)
- **Tek Sütun:** Form elemanları karmaşıklığı önlemek adına dikey olarak tek sütunda alt alta listelenmelidir.
- **Gruplama:** İlişkili form alanları (örn: kişisel bilgiler, veli bilgileri) belirgin başlıklar ve hafif çizgilerle (`border-neutral-100`) ayrılmış gruplar halinde sunulmalıdır.

# Entity List (Varlık Listesi)

Bu doküman, sistemin modüler yapısına göre gruplandırılmış ana varlıkların (entities) detaylı listesini içerir.

## 1. Education Modülü
Eğitim kurumunun ana süreçlerini, ders programlarını ve öğrenci gelişim takibini yöneten etki alanıdır.

- **Student:** Kuruma kayıtlı olan öğrenciler.
- **Parent:** Öğrencinin velisi. Veli bilgileri ve acil durum iletişim noktalarını temsil eder.
- **Teacher:** Kurumda ders veren öğretmenler.
- **Classroom:** Derslerin işlendiği sınıflar/derslikler (örn: A Sınıfı, Fizik Laboratuvarı).
- **Course:** Sunulan ders programları/kurslar (örn: TYT Hazırlık, 10. Sınıf Matematik).
- **Lesson:** Ders programındaki belirli bir konu alanı (örn: Matematik, Fizik, Türkçe).
- **LessonSchedule:** Haftalık ders programı planlaması (Hangi gün, hangi saatte, hangi sınıf ve öğretmen ile ders yapılacağı).
- **Attendance:** Ders yoklama kayıtları (Öğrenci derse katıldı mı, geç mi kaldı, izinli mi).
- **Homework:** Öğretmenler tarafından verilen ödev tanımları.
- **HomeworkSubmission:** Öğrencilerin teslim ettiği ödevler ve öğretmenlerin verdiği puan/değerlendirmeler.
- **Registration:** Öğrencinin kuruma kayıt işlemi.

## 2. CMS Modülü
Kurumsal web sitesinin (Version 1) yönetimi için içeriklerin barındırıldığı etki alanıdır.

- **Page:** Dinamik veya statik kurumsal sayfalar (örn: Hakkımızda, Vizyon-Misyon).
- **Slider:** Ana sayfadaki kayan görseller ve başlıklar.
- **Blog:** Kurumsal haberler, makaleler ve duyurular.
- **BlogCategory:** Blog yazılarının kategorileri.
- **Gallery:** Kurum içi görsellerin veya etkinlik fotoğraflarının sergilendiği albümler.
- **Event:** Kurumun düzenleyeceği etkinlik takvimi (örn: Veli Toplantısı, Seminer).
- **Announcement:** Genel duyurular (örn: Bayram tatili duyurusu).

## 3. CRM Modülü
Aday öğrenci takibi ve kurum ile dış dünya arasındaki ilk iletişimi yöneten etki alanıdır.

- **Lead:** Aday öğrenci/kayıt potansiyeli olan başvurular (Ön kayıt/iletişim talepleri).
- **ContactMessage:** İletişim formu üzerinden gelen genel ziyaretçi mesajları.

## 4. System Modülü
Sistem yönetimi, güvenlik, yetkilendirme ve genel ayarları kapsayan teknik etki alanıdır.

- **User:** Sisteme giriş yetkisi olan tüm kimliklerin ortak tablosu (Öğrenci, Öğretmen, Veli, Personel, Admin).
- **Role:** Sistemdeki rolleri tanımlar (örn: Süper Admin, Öğretmen, Veli).
- **Permission:** İnce ayarlı yetki izinlerini tanımlar (örn: `students.create`, `attendance.take`).
- **Setting:** Sistem parametreleri (örn: Uygulama adı, logo, para birimi).
- **ActivityLog:** Sistemde yapılan kritik işlemlerin log kayıtları (Audit Log).

## 5. Shared Modülü
Modüller arasında paylaşılan ve ortak kullanılan yardımcı varlıklardır.

- **Branch:** Kurumun sahip olduğu şubeler (örn: Kadıköy Şubesi, Beşiktaş Şubesi).
- **Document:** Öğrenci, öğretmen veya ders materyallerine ait belgeler.
- **Media:** Görsel, video veya dosya referansları.
- **Notification:** Kullanıcılara gönderilen sistem içi bildirimler.

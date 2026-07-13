<?php

declare(strict_types=1);

namespace App\Core\Services;

class DemoContentService
{
    protected SettingsManager $settings;

    public function __construct(SettingsManager $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Get Hero section content.
     */
    public function getHeroContent(): array
    {
        return [
            'badge' => '2026 Türkiye Derece Kadrosu & Sınırlı Kontenjan',
            'title' => 'Hedeflerinize Sınır Tanımayan Derece Kadrosuyla Ulaşın',
            'subtitle' => 'Maksimum 12 kişilik butik sınıflar, haftalık birebir mentörlük takipleri ve Türkiye geneli deneme sınavlarıyla YKS ve LGS sınavlarında hayalinizdeki okulu kazanın.',
        ];
    }

    /**
     * Get statistics metrics.
     */
    public function getStatistics(): array
    {
        return [
            'Başarılı Mezun' => '4.800+',
            'Yerleştirme Oranı' => '%98.4',
            'Maksimum Sınıf' => '12 Kişi',
            'Uzman Eğitmen' => '80+',
        ];
    }

    /**
     * Get "Why Choose Us" features list.
     */
    public function getFeatures(): array
    {
        return [
            [
                'title' => 'Kişiye Özel Mentörlük',
                'description' => 'Her öğrencimizin haftalık gelişimini takip eden, ders programlarını planlayan ve ders çalışma verimliliğini artıran özel rehberlik mentörü.',
            ],
            [
                'title' => 'Maksimum 12 Kişilik Sınıflar',
                'description' => 'Kalabalık içinde kaybolmayan öğrenciler. Öğretmenlerin her soruya ve konuya odaklanmasını sağlayan modern butik sınıflar.',
            ],
            [
                'title' => 'Derece Çıkaran Eğitmen Kadrosu',
                'description' => 'Yılların verdiği sınav tecrübesine sahip, yeni nesil soru kalıplarına hakim, alanında uzman seçkin öğretmen kadrosu.',
            ],
            [
                'title' => 'Haftalık Gerçek Sınav Provaları',
                'description' => 'Her Cumartesi Türkiye geneli farklı yayınlarla uygulanan, öğrencilerin eksiklerini nokta atışı tespit eden deneme sınavları.',
            ],
            [
                'title' => 'Soru Çözüm Ofisleri',
                'description' => 'Anlaşılmayan konuların veya çözülemeyen soruların anında öğretmen eşliğinde çözüldüğü günlük birebir ofis saatleri.',
            ],
            [
                'title' => 'Akıllı Veli Bilgilendirme',
                'description' => 'Öğrencinin devamsızlık, yoklama, deneme sonuçları ve ödev takip durumlarını velilere anlık sunan dijital bilgi sistemi.',
            ],
        ];
    }

    /**
     * Get educational programs.
     */
    public function getPrograms(): array
    {
        return [
            [
                'title' => 'YKS (TYT-AYT) Hazırlık Kursu',
                'subtitle' => '12. Sınıf ve Mezunlar İçin',
                'description' => 'Yoğunlaştırılmış müfredat, haftalık 28 saat canlı ders, soru çözüm ofisleri ve 60 adet Türkiye geneli deneme sınavı.',
                'footer_text' => 'Sayısal - Eşit Ağırlık',
                'link' => '/kurslar/yks-hazirlik',
            ],
            [
                'title' => 'LGS (8. Sınıf) Hazırlık Kursu',
                'subtitle' => 'Fen ve Anadolu Liselerine Hazırlık',
                'description' => 'Yeni nesil mantık-muhakeme sorularına odaklı müfredat, haftalık rehberlik takibi ve LGS formatında deneme provaları.',
                'footer_text' => 'Maks. 12 Kişilik Sınıf',
                'link' => '/kurslar/lgs-hazirlik',
            ],
            [
                'title' => 'Ara Sınıf (9-10-11. Sınıf) Kursları',
                'subtitle' => 'Okul Başarısı ve Temel Güçlendirme',
                'description' => 'Yazılı sınavlarına tam hazırlık, eksik konuların kapatılması ve YKS sınavı öncesi sağlam bir akademik temel oluşturma.',
                'footer_text' => 'Okul Yazılı Desteği',
                'link' => '/kurslar/ara-siniflar',
            ],
        ];
    }

    /**
     * Get instructors list.
     */
    public function getTeachers(): array
    {
        return [
            [
                'name' => 'Kemal Yıldız',
                'role' => 'Matematik Zümre Başkanı',
                'bio' => 'Boğaziçi Üniversitesi mezunu, 15+ yıllık sınav tecrübesi ve yeni nesil soru yazarı.',
            ],
            [
                'name' => 'Derya Kaya',
                'role' => 'Fizik Öğretmeni',
                'bio' => 'ODTÜ Fizik mezunu, YKS derece öğrencilerinin mentörü ve kavramsal fizik uzmanı.',
            ],
            [
                'name' => 'Murat Demir',
                'role' => 'Kimya Öğretmeni',
                'bio' => 'Hacettepe Üniversitesi mezunu, görselleştirilmiş hafıza teknikleriyle kimya eğitimi uzmanı.',
            ],
            [
                'name' => 'Zeynep Yılmaz',
                'role' => 'Türk Dili ve Edebiyatı',
                'bio' => 'Marmara Üniversitesi mezunu, paragraf taktikleri ve sınav odaklı dil bilgisi koordinatörü.',
            ],
        ];
    }

    /**
     * Get success/achievement stories.
     */
    public function getSuccessStories(): array
    {
        return [
            [
                'badge' => 'YKS Sayısal Türkiye 42.si',
                'student' => 'Ali Eren Şahin',
                'destination' => 'Koç Üniversitesi Bilgisayar Mühendisliği (Tam Burslu)',
            ],
            [
                'badge' => 'YKS Eşit Ağırlık Türkiye 115.si',
                'student' => 'Merve Yurtseven',
                'destination' => 'Galatasaray Üniversitesi Hukuk Fakültesi',
            ],
            [
                'badge' => 'LGS Tam Puan 500',
                'student' => 'Caner Karabulut',
                'destination' => 'Galatasaray Lisesi',
            ],
        ];
    }

    /**
     * Get parent/student testimonials.
     */
    public function getTestimonials(): array
    {
        return [
            [
                'author' => 'Hakan Yıldız',
                'role' => 'Veli (YKS Öğrenci Babası)',
                'quote' => '"Oğlumun ders çalışma alışkanlığı neredeyse yoktu. Limit VIP rehberlik mentörü sayesinde düzenli ders çalışma rutini kazandı ve bu yıl İTÜ Makine Mühendisliğini kazandı. Tüm öğretmenlerimize teşekkür ederiz."',
            ],
            [
                'author' => 'Elif Sönmez',
                'role' => 'Mezun Öğrenci',
                'quote' => '"Kalabalık dershanelerde kaybolmak yerine buradaki 12 kişilik sınıfları tercih ettim. Soru çözüm ofisleri ve birebir etütler sayesinde yapamadığım hiçbir soru kalmadı. Tıp fakültesi hayalimi gerçekleştirdim."',
            ],
            [
                'author' => 'Ayşe Karaca',
                'role' => 'Veli (LGS Öğrenci Annesi)',
                'quote' => '"LGS sürecindeki yoğun stres dönemini buradaki rehberlik ekibinin profesyonel psikolojik desteği ve sınav simülasyonları sayesinde çok rahat atlattık. Kızım Kabataş Erkek Lisesini kazandı."',
            ],
        ];
    }

    /**
     * Get gallery categories.
     */
    public function getGallery(): array
    {
        return [
            [
                'title' => 'Butik Sınıflar',
                'subtitle' => 'Maksimum 12 kişilik akıllı tahtalı sınıflar',
            ],
            [
                'title' => 'Sessiz Kütüphane',
                'subtitle' => 'Bireysel çalışma ve etüt alanları',
            ],
            [
                'title' => 'Birebir Ofisler',
                'subtitle' => 'Özel ders ve soru çözüm odaları',
            ],
        ];
    }

    /**
     * Get FAQ.
     */
    public function getFaq(): array
    {
        return [
            'Butik Sınıf Kontenjanları Kaç Kişidir?' => 'Öğrencilerimizin derslere odaklanabilmesi ve öğretmenlerin her öğrenciyle birebir ilgilenebilmesi için sınıflarımız maksimum 12 kişi ile sınırlandırılmıştır.',
            'Birebir Soru Çözüm Saatleri Nasıl Yapılır?' => 'Öğrencilerimiz haftalık kişisel çalışma programları dahilinde rehber öğretmenleri eşliğinde diledikleri dersten birebir soru çözüm randevusu alabilirler.',
            'Erken Kayıt İndirim Dönemleri Ne Zamana Kadar Sürecek?' => 'Yeni eğitim dönemi erken kayıt indirim avantajları Temmuz ayı sonuna kadar geçerlidir. Bu dönemde kayıt olan velilerimize taksit ve eğitim materyalleri desteği sunulur.',
            'Deneme Sınavları Hangi Sıklıkta Yapılır?' => 'Kurumumuzda her hafta sonu Türkiye geneli yayınların katılımıyla gerçek sınav provası formatında deneme sınavları uygulanır ve anlık optik analiz raporları çıkarılır.',
        ];
    }

    /**
     * Get blog posts.
     */
    public function getBlogs(): array
    {
        return [
            [
                'title' => 'YKS Son 3 Ay Çalışma Stratejisi',
                'meta' => '12 Mart 2026 | Rehberlik Servisi',
                'snippet' => 'Netlerinizi artıracak konu analizleri, deneme sıklığı planlaması ve sınav stresi yönetimi için öneriler.',
                'read_time' => 'Okuma Süresi: 4 dk',
                'link' => '/blog/yks-son-3-ay-stratejisi',
            ],
            [
                'title' => 'LGS Sayısal Bölüm Taktikleri',
                'meta' => '05 Mart 2026 | Matematik Zümresi',
                'snippet' => 'Matematik dersinde yeni nesil mantık-muhakeme sorularını hızlı çözme yöntemleri ve formül kullanım kılavuzu.',
                'read_time' => 'Okuma Süresi: 5 dk',
                'link' => '/blog/lgs-sayisal-taktikleri',
            ],
            [
                'title' => 'Verimli Ders Çalışma Teknikleri',
                'meta' => '20 Şubat 2026 | Rehberlik Servisi',
                'snippet' => 'Pomodoro tekniği, aktif tekrar yöntemi ve kişisel ders çalışma takvimi hazırlama aşamaları.',
                'read_time' => 'Okuma Süresi: 3 dk',
                'link' => '/blog/verimli-ders-calisma',
            ],
        ];
    }

    /**
     * Get Whitelabel Contact Information.
     */
    public function getContact(): array
    {
        return [
            'company_name' => $this->settings->getCompanyName(),
            'phone' => $this->settings->getPhone(),
            'mail' => $this->settings->getMail(),
            'address' => $this->settings->getAddress(),
            'socials' => $this->settings->getSocials(),
        ];
    }

    /**
     * Get Branches.
     */
    public function getBranches(): array
    {
        return [
            [
                'name' => 'Kadıköy Merkez Şube',
                'address' => 'Caddebostan Mah. Bağdat Caddesi No:245/4 Kadıköy/İstanbul',
                'phone' => '+90 216 555 12 34',
                'manager' => 'Ahmet Yılmaz',
                'image' => '/assets/branding/og-image.jpg',
            ],
            [
                'name' => 'Beşiktaş Şube',
                'address' => 'Barbaros Bulvarı No: 123 Beşiktaş/İstanbul',
                'phone' => '+90 212 555 12 34',
                'manager' => 'Ayşe Demir',
                'image' => '/assets/branding/og-image.jpg',
            ],
        ];
    }

    /**
     * Get About Us content.
     */
    public function getAbout(): array
    {
        return [
            'story' => 'Kurumumuz 2010 yılında eğitimde fırsat eşitliği ve butik eğitim anlayışıyla yola çıkmıştır.',
            'mission' => 'Her öğrencinin kendi potansiyelini keşfetmesini sağlamak.',
            'vision' => 'Türkiye\'nin en yenilikçi ve başarılı eğitim kurumu olmak.',
            'values' => ['Öğrenci Odaklılık', 'Şeffaflık', 'Yenilikçilik', 'Sürekli Gelişim'],
            'timeline' => [
                ['year' => '2010', 'title' => 'Kuruluş', 'desc' => 'İlk şubemiz Kadıköy\'de açıldı.'],
                ['year' => '2015', 'title' => 'Büyüme', 'desc' => 'İkinci şube ve yeni eğitim modelleri.'],
                ['year' => '2026', 'title' => 'SaaS Dönüşümü', 'desc' => 'Dijital altyapı ile güçlenen yönetim sistemi.'],
            ]
        ];
    }

    /**
     * Get Events.
     */
    public function getEvents(): array
    {
        return [
            [
                'title' => 'YKS Motivasyon Semineri',
                'date' => '15 Nisan 2026',
                'location' => 'Merkez Şube Konferans Salonu',
                'desc' => 'Sınav kaygısını azaltma ve motivasyon semineri.',
            ],
            [
                'title' => 'Veli Toplantısı (LGS Grubu)',
                'date' => '20 Nisan 2026',
                'location' => 'Online (Zoom)',
                'desc' => 'Deneme sınav sonuçlarının değerlendirilmesi.',
            ],
        ];
    }

    /**
     * Get Announcements.
     */
    public function getAnnouncements(): array
    {
        return [
            [
                'title' => 'Erken Kayıt Dönemi Başladı',
                'date' => '01 Mart 2026',
                'content' => 'Yeni eğitim dönemi için sınırlı kontenjanlı erken kayıt avantajlarını kaçırmayın.',
            ],
            [
                'title' => 'Türkiye Geneli Deneme Sınavı Sonuçları Açıklandı',
                'date' => '10 Mart 2026',
                'content' => 'Öğrencilerimiz sonuç karnelerine portal üzerinden ulaşabilirler.',
            ],
        ];
    }

    /**
     * Get Legal text based on type.
     */
    public function getLegal(string $type): array
    {
        $content = match ($type) {
            'kvkk' => '6698 sayılı Kişisel Verilerin Korunması Kanunu ("KVKK") uyarınca...',
            'cerez' => 'Web sitemizde size daha iyi hizmet sunabilmek için çerezler (cookies) kullanılmaktadır...',
            'gizlilik' => 'Kişisel bilgilerinizin gizliliği ve güvenliği kurumumuz için en önemli önceliktir...',
            default => 'İlgili metin bulunamadı.'
        };

        return [
            'title' => strtoupper($type) . ' Metni',
            'content' => $content,
        ];
    }
}

# Version Page Matrix (Sürüm Sayfa Matrisi)

Bu matris, kurumsal web sitesi ve yönetim panelindeki sayfaların/rotaların hangi ticari paket kapsamında (V1, V2, V3) erişilebilir olacağını gösterir.

| Sayfa / Rota | Rota Takısı (Route Name) | V1: Kurumsal | V2: Yönetim | V3: Tam ERP |
| :--- | :--- | :---: | :---: | :---: |
| **Ana Sayfa** | `frontend.home` | ✔ | ✔ | ✔ |
| **Hakkımızda** | `frontend.about` | ✔ | ✔ | ✔ |
| **Kurs Listesi & Detay** | `frontend.courses.*` | ✔ | ✔ | ✔ |
| **Duyurular & Blog** | `frontend.blogs.*` | ✔ | ✔ | ✔ |
| **İletişim Formu & Sayfa**| `frontend.contact` | ✔ | ✔ | ✔ |
| **Online Ön Kayıt Sayfası**| `frontend.pre-register` | ✔ | ✔ | ✔ |
| **Admin Login/Giriş** | `auth.login` | ✖ | ✔ | ✔ |
| **Admin Dashboard** | `admin.dashboard` | ✖ | ✔ | ✔ |
| **Ön Kayıt Talepleri** | `admin.leads.*` | ✖ | ✔ | ✔ |
| **İletişim Mesajları** | `admin.messages.*` | ✖ | ✔ | ✔ |
| **Sayfa Yönetimi (CMS)** | `admin.pages.*` | ✖ | ✔ | ✔ |
| **Blog & Haber Yönetimi** | `admin.blogs.*` | ✖ | ✔ | ✔ |
| **Öğrenci Yönetimi** | `admin.students.*` | ✖ | ✔ | ✔ |
| **Veli Yönetimi** | `admin.parents.*` | ✖ | ✔ | ✔ |
| **Öğretmen Yönetimi** | `admin.teachers.*` | ✖ | ✔ | ✔ |
| **Kurs & Derslik Yönetimi**| `admin.courses.*` / `classrooms.*`| ✖ | ✔ | ✔ |
| **Ders Programı Takvimi**| `admin.schedules.*` | ✖ | ✖ | ✔ |
| **Yoklama Girişi & Takip**| `admin.attendance.*` | ✖ | ✖ | ✔ |
| **Ödev Yönetimi** | `admin.homeworks.*` | ✖ | ✖ | ✔ |
| **Kullanıcılar & Roller** | `admin.users.*` / `roles.*` | ✖ | ✔ | ✔ |
| **Genel Sistem Ayarları** | `admin.settings.*` | ✖ | ✔ | ✔ |

## Rota Kısıtlama Mekanizması
Lisanssız erişimleri engellemek için rota gruplarında paket kontrolü yapan `FeatureFlagMiddleware` middleware'i kullanılacaktır.
- **V1 Rotaları:** Herhangi bir kısıtlama içermez.
- **V2 Rotaları:** `middleware('feature:admin-panel')` ile korunacaktır.
- **V3 Rotaları (ERP):** `middleware('feature:erp-finance-management')` (veya `erp-full` yeteneği) ile korunacaktır.

# Coding Standards (Kodlama Standartları)

Bu doküman, SaaS projesinde yazılan kodların kalitesini ve sürdürülebilirliğini korumak amacıyla genişletilmiştir.

## 1. Tip Güvenliği (Type Safety) ve Enums
- **Enums Kullanımı:** Durumlar (status), tipler (types), roller (roles), para birimleri (currencies) ve diller (languages) gibi sabit listeler için kesinlikle `App\Core\Enums` altındaki PHP 8.4 uyumlu backing enum'lar kullanılacaktır. Kod tabanında kesinlikle magic string veya magic integer kullanılmayacaktır.
  - *Doğru:* `UserType::ADMIN->value`
  - *Yanlış:* `'admin'`
- **Strict Types:** Tüm PHP dosyalarının ilk satırı `declare(strict_types=1);` olmak zorundadır.

## 2. Veri Taşıma Nesneleri (DTOs)
- Kontrolcülerden (Controllers) servislere veya katmanlar arasına veri taşınırken diziler yerine `App\Core\DTO` altındaki tip güvenli DTO'lar kullanılacaktır.
- Tüm DTO'lar `BaseDTO`'yu kalıtım almalı ve constructor property promotion özelliğiyle yazılmalıdır:
  ```php
  class PaginationDTO extends BaseDTO {
      public function __construct(
          public int $page = 1,
          public int $perPage = 15,
      ) {}
  }
  ```

## 3. Tek Sorumluluk Prensipli Eylemler (Actions)
- İş mantığını (business logic) bölmek için `App\Actions` altındaki single-action sınıflar kullanılacaktır.
- Her action sınıfı `BaseAction` sınıfından türetilmeli ve tek bir `execute()` metodu barındırmalıdır. Bu sayede kodun test edilebilirliği ve tekrar kullanılabilirliği maksimuma çıkarılır.

## 4. Kod Biçimlendirme (Formatting)
- **Laravel Pint:** Geliştiriciler kodlarını commitlemeden önce mutlaka `php vendor/bin/pint` komutu ile biçimlendirmelidir.
- **PHPStan:** Yapılan geliştirmelerin PHPStan Seviye 8 statik analiz kurallarına tam uyum göstermesi zorunludur.

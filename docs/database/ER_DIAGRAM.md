# Entity Relationship Diagram (ER Diyagramı)

Bu diyagram, sistemin çekirdek veri modelleri arasındaki ilişkileri ASCII formatında gösterir.

```text
========================================================================================
                                     SYSTEM & ROLES
========================================================================================

    [permissions] <====== (Many-to-Many) ======> [roles]
                                                    ||
                                                    || (Many-to-Many / user_has_roles)
                                                    \/
                                                 [users]
                                                    ||
                                     +--------------+--------------+
                                     | (1-to-1)                    | (1-to-1)
                                     \/                            \/
                                 [teachers]                    [students]
                                                                   ||
                                                                   || (Many-to-Many / family)
                                                                   \/
                                                                [parents]


========================================================================================
                                       EDUCATION
========================================================================================

     [branches] <---(1-to-Many)---+
          |                       |
          | (1-to-Many)           | (1-to-Many)
          \/                      \/
      [classrooms]            [courses] <======== (Many-to-Many) ========> [lessons]
          |                                                                   |
          | (1-to-Many)                                                       | (1-to-Many)
          +-----------------------+---------------------+---------------------+
                                  |                     |
                                  \/                    \/
                         [lesson_schedules]       [homeworks]
                                  |                     |
                                  | (1-to-Many)         | (1-to-Many)
                                  \/                    \/
                            [attendances]      [homework_submissions]
                                                        |
                                                        | (Many-to-One)
                                                        \/
                                                    [students]


========================================================================================
                                REGISTRATIONS & SHARED
========================================================================================

      [students] <---(1-to-Many)--- [registrations] --->(Many-to-One)---> [branches]
                                           |
                                           +--->(Many-to-One)---> [courses]

      [users] <---(1-to-Many)--- [activity_logs]
      [users] <---(1-to-Many)--- [notifications]

      [any_model (Polymorphic)] <---(1-to-Many)--- [media]
      [any_model (Polymorphic)] <---(1-to-Many)--- [documents]


========================================================================================
                                      CMS & CRM
========================================================================================

      [blog_categories] <---(1-to-Many)--- [blogs]
      
      [slider]               [gallery]               [events]
      
      [leads]                [contact_messages]
```

## Notasyon Açıklamaları
- `(1-to-Many) / <--- / --->` : Bire çok ilişkiyi temsil eder. Ok ucu, "çok" (Many) tarafını işaret eder.
- `(Many-to-Many) / <======>` : Çoklu ilişki tablosu (pivot table) kullanan çoktan çoğa ilişkiyi temsil eder.
- `(Polymorphic)` : Laravel Polymorphic Relation kullanılarak birden fazla varlığa bağlanabilen yapıları temsil eder.

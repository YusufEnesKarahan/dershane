<?php
namespace App\Domain\Auth\Dictionaries;

class PermissionDictionary
{
    public const DICTIONARY = [
        'dashboard.view' => ['label' => 'Dashboard Görüntüleme', 'group' => 'System'],
        
        'users.view' => ['label' => 'Kullanıcıları Görüntüle', 'group' => 'Users'],
        'users.create' => ['label' => 'Kullanıcı Oluştur', 'group' => 'Users'],
        'users.update' => ['label' => 'Kullanıcı Güncelle', 'group' => 'Users'],
        'users.delete' => ['label' => 'Kullanıcı Sil', 'group' => 'Users'],
        'users.restore' => ['label' => 'Kullanıcı Geri Yükle', 'group' => 'Users'],
        'users.export' => ['label' => 'Kullanıcı Dışa Aktar', 'group' => 'Users'],

        'roles.view' => ['label' => 'Rolleri Görüntüle', 'group' => 'Roles'],
        'roles.create' => ['label' => 'Rol Oluştur', 'group' => 'Roles'],
        'roles.update' => ['label' => 'Rol Güncelle', 'group' => 'Roles'],
        'roles.delete' => ['label' => 'Rol Sil', 'group' => 'Roles'],
        'roles.assign' => ['label' => 'Rol Ata', 'group' => 'Roles'],

        'permissions.view' => ['label' => 'Yetkileri Görüntüle', 'group' => 'Roles'],
        'permissions.assign' => ['label' => 'Yetki Ata', 'group' => 'Roles'],

        'pages.view' => ['label' => 'Sayfaları Görüntüle', 'group' => 'CMS'],
        'pages.create' => ['label' => 'Sayfa Oluştur', 'group' => 'CMS'],
        'pages.update' => ['label' => 'Sayfa Güncelle', 'group' => 'CMS'],
        'pages.delete' => ['label' => 'Sayfa Sil', 'group' => 'CMS'],

        'blogs.view' => ['label' => 'Yazıları Görüntüle', 'group' => 'CMS'],
        'blogs.create' => ['label' => 'Yazı Oluştur', 'group' => 'CMS'],
        'blogs.update' => ['label' => 'Yazı Güncelle', 'group' => 'CMS'],
        'blogs.delete' => ['label' => 'Yazı Sil', 'group' => 'CMS'],

        'teachers.view' => ['label' => 'Öğretmenleri Görüntüle', 'group' => 'Education'],
        'teachers.create' => ['label' => 'Öğretmen Oluştur', 'group' => 'Education'],
        'teachers.update' => ['label' => 'Öğretmen Güncelle', 'group' => 'Education'],
        'teachers.delete' => ['label' => 'Öğretmen Sil', 'group' => 'Education'],

        'courses.view' => ['label' => 'Kursları Görüntüle', 'group' => 'Education'],
        'courses.create' => ['label' => 'Kurs Oluştur', 'group' => 'Education'],
        'courses.update' => ['label' => 'Kurs Güncelle', 'group' => 'Education'],
        'courses.delete' => ['label' => 'Kurs Sil', 'group' => 'Education'],

        'gallery.view' => ['label' => 'Galeri Görüntüle', 'group' => 'CMS'],
        'gallery.create' => ['label' => 'Galeri Ekle', 'group' => 'CMS'],
        'gallery.delete' => ['label' => 'Galeri Sil', 'group' => 'CMS'],

        'media.view' => ['label' => 'Medya Görüntüle', 'group' => 'Media'],
        'media.create' => ['label' => 'Medya Ekle', 'group' => 'Media'],
        'media.delete' => ['label' => 'Medya Sil', 'group' => 'Media'],

        'settings.view' => ['label' => 'Ayarları Görüntüle', 'group' => 'Settings'],
        'settings.update' => ['label' => 'Ayarları Güncelle', 'group' => 'Settings'],

        'crm.view' => ['label' => 'CRM Görüntüle', 'group' => 'CRM'],
        'crm.manage' => ['label' => 'CRM Yönet', 'group' => 'CRM'],

        'attendance.view' => ['label' => 'Yoklama Görüntüle', 'group' => 'Education'],
        'attendance.manage' => ['label' => 'Yoklama Yönet', 'group' => 'Education'],

        'homeworks.view' => ['label' => 'Ödevleri Görüntüle', 'group' => 'Education'],
        'homeworks.manage' => ['label' => 'Ödevleri Yönet', 'group' => 'Education'],

        'classrooms.view' => ['label' => 'Sınıfları Görüntüle', 'group' => 'Education'],
        'classrooms.manage' => ['label' => 'Sınıfları Yönet', 'group' => 'Education'],

        'students.view' => ['label' => 'Öğrencileri Görüntüle', 'group' => 'Education'],
        'students.create' => ['label' => 'Öğrenci Oluştur', 'group' => 'Education'],
        'students.update' => ['label' => 'Öğrenci Güncelle', 'group' => 'Education'],
        'students.delete' => ['label' => 'Öğrenci Sil', 'group' => 'Education'],

        'leads.view' => ['label' => 'Aday Öğrencileri Görüntüle', 'group' => 'CRM'],
        'leads.create' => ['label' => 'Aday Öğrenci Oluştur', 'group' => 'CRM'],
        'leads.update' => ['label' => 'Aday Öğrenci Güncelle', 'group' => 'CRM'],
        'leads.delete' => ['label' => 'Aday Öğrenci Sil', 'group' => 'CRM'],

        'contacts.view' => ['label' => 'İletişim Mesajlarını Görüntüle', 'group' => 'CRM'],
        'contacts.create' => ['label' => 'İletişim Mesajı Oluştur', 'group' => 'CRM'],
        'contacts.update' => ['label' => 'İletişim Mesajı Güncelle', 'group' => 'CRM'],
        'contacts.delete' => ['label' => 'İletişim Mesajı Sil', 'group' => 'CRM'],

        'registrations.view' => ['label' => 'Kayıtları Görüntüle', 'group' => 'Education'],
        'registrations.create' => ['label' => 'Kayıt Oluştur', 'group' => 'Education'],
        'registrations.update' => ['label' => 'Kayıt Güncelle', 'group' => 'Education'],
        'registrations.delete' => ['label' => 'Kayıt Sil', 'group' => 'Education'],

        'announcements.view' => ['label' => 'Duyuruları Görüntüle', 'group' => 'CMS'],
        'announcements.create' => ['label' => 'Duyuru Oluştur', 'group' => 'CMS'],
        'announcements.update' => ['label' => 'Duyuru Güncelle', 'group' => 'CMS'],
        'announcements.delete' => ['label' => 'Duyuru Sil', 'group' => 'CMS'],

        'branches.view' => ['label' => 'Şubeleri Görüntüle', 'group' => 'System'],
        'branches.manage' => ['label' => 'Şubeleri Yönet', 'group' => 'System'],
    ];

    public static function all(): array
    {
        return array_keys(self::DICTIONARY);
    }
}

<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SettingGroup;
use App\Models\PlatformSetting;

class PlatformSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            'brand' => ['name' => 'Brand Assets', 'desc' => 'Company identity and branding logos.', 'icon' => 'photograph', 'sort' => 1],
            'theme' => ['name' => 'Theme Colors', 'desc' => 'Dashboard interface styles and CSS variables.', 'icon' => 'color-swatch', 'sort' => 2],
            'mail' => ['name' => 'Mail (SMTP)', 'desc' => 'Outgoing e-mail mailer host options.', 'icon' => 'mail', 'sort' => 3],
            'storage' => ['name' => 'Storage Config', 'desc' => 'Digital asset storage configuration.', 'icon' => 'database', 'sort' => 4],
            'security' => ['name' => 'Security Settings', 'desc' => 'Force HTTPS and strict session controls.', 'icon' => 'shield', 'sort' => 5],
            'maintenance' => ['name' => 'Maintenance Mode', 'desc' => 'Put the web application into maintenance mode.', 'icon' => 'ban', 'sort' => 6],
            'localization' => ['name' => 'Localization', 'desc' => 'System locale and timezone properties.', 'icon' => 'globe-alt', 'sort' => 7],
        ];

        $groupIds = [];
        foreach ($groups as $slug => $cfg) {
            $group = SettingGroup::firstOrCreate(['slug' => $slug], [
                'name' => $cfg['name'],
                'description' => $cfg['desc'],
                'icon' => $cfg['icon'],
                'sort_order' => $cfg['sort'],
            ]);
            $groupIds[$slug] = $group->id;
        }

        $settings = [
            // Brand
            ['group' => 'brand', 'key' => 'brand.company_name', 'val' => 'Dershane', 'type' => 'text', 'enc' => false],
            ['group' => 'brand', 'key' => 'brand.short_name', 'val' => 'Dershane', 'type' => 'text', 'enc' => false],
            ['group' => 'brand', 'key' => 'brand.legal_name', 'val' => 'Dershane Online A.Ş.', 'type' => 'text', 'enc' => false],
            ['group' => 'brand', 'key' => 'brand.logo', 'val' => '', 'type' => 'file', 'enc' => false],
            ['group' => 'brand', 'key' => 'brand.dark_logo', 'val' => '', 'type' => 'file', 'enc' => false],
            ['group' => 'brand', 'key' => 'brand.favicon', 'val' => '', 'type' => 'file', 'enc' => false],

            // Theme
            ['group' => 'theme', 'key' => 'theme.primary_color', 'val' => '#4f46e5', 'type' => 'text', 'enc' => false],
            ['group' => 'theme', 'key' => 'theme.secondary_color', 'val' => '#06b6d4', 'type' => 'text', 'enc' => false],
            ['group' => 'theme', 'key' => 'theme.accent_color', 'val' => '#f59e0b', 'type' => 'text', 'enc' => false],
            ['group' => 'theme', 'key' => 'theme.background_color', 'val' => '#f8fafc', 'type' => 'text', 'enc' => false],
            ['group' => 'theme', 'key' => 'theme.sidebar_color', 'val' => '#0f172a', 'type' => 'text', 'enc' => false],
            ['group' => 'theme', 'key' => 'theme.border_radius', 'val' => '12px', 'type' => 'text', 'enc' => false],
            ['group' => 'theme', 'key' => 'theme.spacing', 'val' => '16px', 'type' => 'text', 'enc' => false],
            ['group' => 'theme', 'key' => 'theme.typography', 'val' => 'Instrument Sans, sans-serif', 'type' => 'text', 'enc' => false],

            // Mail
            ['group' => 'mail', 'key' => 'mail.host', 'val' => 'smtp.mailtrap.io', 'type' => 'text', 'enc' => false],
            ['group' => 'mail', 'key' => 'mail.port', 'val' => '2525', 'type' => 'text', 'enc' => false],
            ['group' => 'mail', 'key' => 'mail.username', 'val' => '', 'type' => 'text', 'enc' => false],
            ['group' => 'mail', 'key' => 'mail.password', 'val' => '', 'type' => 'text', 'enc' => true],
            ['group' => 'mail', 'key' => 'mail.encryption', 'val' => 'tls', 'type' => 'text', 'enc' => false],
            ['group' => 'mail', 'key' => 'mail.from_address', 'val' => 'noreply@dershane.com', 'type' => 'text', 'enc' => false],
            ['group' => 'mail', 'key' => 'mail.from_name', 'val' => 'Dershane', 'type' => 'text', 'enc' => false],

            // Storage
            ['group' => 'storage', 'key' => 'storage.default_disk', 'val' => 'public', 'type' => 'text', 'enc' => false],

            // Security
            ['group' => 'security', 'key' => 'security.force_https', 'val' => '0', 'type' => 'boolean', 'enc' => false],

            // Maintenance
            ['group' => 'maintenance', 'key' => 'maintenance.enabled', 'val' => '0', 'type' => 'boolean', 'enc' => false],
            ['group' => 'maintenance', 'key' => 'maintenance.bypass_ip', 'val' => '', 'type' => 'text', 'enc' => false],

            // Localization
            ['group' => 'localization', 'key' => 'localization.default_locale', 'val' => 'tr', 'type' => 'text', 'enc' => false],
        ];

        foreach ($settings as $idx => $s) {
            PlatformSetting::firstOrCreate(['key' => $s['key']], [
                'group_id' => $groupIds[$s['group']],
                'value' => $s['val'],
                'type' => $s['type'],
                'is_encrypted' => $s['enc'],
                'sort_order' => $idx,
            ]);
        }
    }
}

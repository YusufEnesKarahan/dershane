<?php
namespace App\Domain\Platform\Services;

class MailConfigurationService
{
    public function __construct(protected PlatformSettingsService $settingsService) {}

    public function testConnection(): bool
    {
        $host = $this->settingsService->get('mail.host');
        $port = (int) $this->settingsService->get('mail.port', '587');
        
        if (empty($host)) {
            return false;
        }

        // Socket testing trigger
        try {
            $connection = @fsockopen($host, $port, $errno, $errstr, 3);
            if (is_resource($connection)) {
                fclose($connection);
                return true;
            }
        } catch (\Exception $e) {
            return false;
        }

        return false;
    }
}

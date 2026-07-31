<?php

namespace App\Domain\HQ\Services\Extension;

use App\Models\HQExtension;
use App\Models\HQExtensionVersion;

class ExtensionRegistryService
{
    /**
     * Register a new extension or update its catalog entry.
     */
    public function registerExtension(array $data): HQExtension
    {
        $extension = HQExtension::updateOrCreate(
            ['slug' => $data['slug']],
            [
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'vendor' => $data['vendor'],
                'version' => $data['version'] ?? '1.0.0',
                'status' => $data['status'] ?? 'active',
                'type' => $data['type'] ?? 'plugin',
                'metadata' => $data['metadata'] ?? [],
            ]
        );

        return $extension;
    }

    /**
     * Register a new version for an extension.
     */
    public function registerVersion(HQExtension $extension, string $version, array $details = []): HQExtensionVersion
    {
        return HQExtensionVersion::updateOrCreate(
            [
                'extension_id' => $extension->id,
                'version' => $version,
            ],
            [
                'release_notes' => $details['release_notes'] ?? null,
                'requirements' => $details['requirements'] ?? [],
                'dependencies' => $details['dependencies'] ?? [],
                'status' => $details['status'] ?? 'stable',
            ]
        );
    }

    /**
     * Find an extension by slug.
     */
    public function getExtension(string $slug): ?HQExtension
    {
        return HQExtension::where('slug', $slug)->first();
    }
}

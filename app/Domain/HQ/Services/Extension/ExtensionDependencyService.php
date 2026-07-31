<?php

namespace App\Domain\HQ\Services\Extension;

use App\Models\HQExtensionVersion;

class ExtensionDependencyService
{
    /**
     * Check if a specific version meets system requirements and dependencies.
     */
    public function checkCompatibility(HQExtensionVersion $version, array $systemContext = []): array
    {
        $issues = [];
        
        // Check core requirements (e.g., PHP, HQ Central Version)
        if (!empty($version->requirements)) {
            foreach ($version->requirements as $key => $requiredValue) {
                $actualValue = $systemContext[$key] ?? null;
                if (!$actualValue) {
                    $issues[] = "Missing core requirement: {$key}";
                } else {
                    // Primitive version check logic (can be expanded with Semver library)
                    if (version_compare($actualValue, ltrim($requiredValue, '>=<'), '<')) {
                        $issues[] = "Requirement failed for {$key}: Requires {$requiredValue}, found {$actualValue}";
                    }
                }
            }
        }
        
        return [
            'is_compatible' => empty($issues),
            'issues' => $issues,
        ];
    }
}

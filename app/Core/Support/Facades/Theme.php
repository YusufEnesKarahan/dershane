<?php

declare(strict_types=1);

namespace App\Core\Support\Facades;

use App\Core\Services\ThemeService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string getPrimaryColor()
 * @method static string getSecondaryColor()
 * @method static string getLogoPath(string $mode = 'light')
 * @method static string getFaviconPath()
 * @method static string getThemeName()
 * @method static string renderCssVariables()
 *
 * @see ThemeService
 */
class Theme extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'saas.theme';
    }
}

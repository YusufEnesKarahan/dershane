<?php

use App\Providers\AppServiceProvider;
use App\Providers\DomainServiceProvider;
use App\Providers\FeatureServiceProvider;
use App\Providers\RepositoryServiceProvider;
use App\Providers\SaaSServiceProvider;
use App\Providers\SettingsServiceProvider;
use App\Providers\ThemeServiceProvider;

return [
    AppServiceProvider::class,
    SaaSServiceProvider::class,
    RepositoryServiceProvider::class,
    FeatureServiceProvider::class,
    ThemeServiceProvider::class,
    SettingsServiceProvider::class,
    DomainServiceProvider::class,
];

<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    App\Providers\Filament\MasterPanelProvider::class,
    App\Providers\FortifyServiceProvider::class,
    // Only registered when Telescope is installed (dev environments only)
    ...(class_exists(\Laravel\Telescope\TelescopeApplicationServiceProvider::class)
        ? [App\Providers\TelescopeServiceProvider::class]
        : []),
];

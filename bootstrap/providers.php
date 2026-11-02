<?php

use App\Providers\AppServiceProvider;
use Modules\CRM\Providers\CrmServiceProvider;
use Modules\Inventory\Providers\InventoryServiceProvider;
use Modules\Setting\Providers\SettingServiceProvider;

return [
    AppServiceProvider::class,
    SettingServiceProvider::class,
    InventoryServiceProvider::class,
    CrmServiceProvider::class,
];

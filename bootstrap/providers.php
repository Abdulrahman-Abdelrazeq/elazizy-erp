<?php

use App\Providers\AppServiceProvider;
use Modules\CRM\Providers\CrmServiceProvider;
use Modules\Inventory\Providers\InventoryServiceProvider;
use Modules\Invoice\Providers\InvoiceServiceProvider;
use Modules\POS\Providers\PosServiceProvider;
use Modules\Setting\Providers\SettingServiceProvider;
use Modules\Treasury\Providers\TreasuryServiceProvider;
use Modules\Accounting\Providers\AccountingServiceProvider;
use Modules\FinancialReports\Providers\FinancialReportsServiceProvider;
use Modules\HR\Providers\HRServiceProvider;
use Modules\Manufacturing\Providers\ManufacturingServiceProvider;

return [
    AppServiceProvider::class,
    SettingServiceProvider::class,
    InventoryServiceProvider::class,
    CrmServiceProvider::class,
    InvoiceServiceProvider::class,
    PosServiceProvider::class,
    TreasuryServiceProvider::class,
    AccountingServiceProvider::class,
    FinancialReportsServiceProvider::class,
    HRServiceProvider::class,
    ManufacturingServiceProvider::class,
];

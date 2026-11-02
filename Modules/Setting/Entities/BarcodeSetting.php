<?php

namespace Modules\Setting\Entities;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarcodeSetting extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'barcode_settings';

    protected $fillable = [
        'tenant_id',
        'name',
        'barcode_type',
        'label_width',
        'label_height',
        'show_product_name',
        'show_price',
        'show_company_name',
        'show_barcode_number',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'label_width' => 'integer',
            'label_height' => 'integer',
            'show_product_name' => 'boolean',
            'show_price' => 'boolean',
            'show_company_name' => 'boolean',
            'show_barcode_number' => 'boolean',
            'is_default' => 'boolean',
        ];
    }
}


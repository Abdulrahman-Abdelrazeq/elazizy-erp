<?php

namespace Modules\Setting\Entities;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrintTemplate extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'print_templates';

    protected $fillable = [
        'tenant_id',
        'title',
        'invoice_type',
        'paper_size',
        'show_logo',
        'show_qr',
        'show_tax_number',
        'show_terms',
        'terms_and_conditions',
        'footer_text',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'show_logo' => 'boolean',
            'show_qr' => 'boolean',
            'show_tax_number' => 'boolean',
            'show_terms' => 'boolean',
            'is_default' => 'boolean',
        ];
    }
}


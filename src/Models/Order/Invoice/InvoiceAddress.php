<?php

namespace Shopen\Models\Order\Invoice;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'first_name',
        'last_name',
        'company',
        'company_nip',
        'address_line',
        'city',
        'postal_code',
        'country',
        'phone',
        'email',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
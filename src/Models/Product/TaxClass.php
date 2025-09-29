<?php

namespace Shopen\Models\Product;

use Illuminate\Database\Eloquent\Model;

class TaxClass extends Model
{
    protected $fillable = [
        'name',
        'code',
        'rate',
        'description',
    ];

    protected $casts = [
        'rate' => 'int'
    ];

    // Relacja z produktami
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Oblicz VAT z kwoty netto
    public function calculateTax($netAmount)
    {
        return round($netAmount * ($this->rate / 100), 2);
    }

    // Oblicz kwotę brutto z netto
    public function calculateGross($netAmount)
    {
        return $netAmount + $this->calculateTax($netAmount);
    }

    // Oblicz kwotę netto z brutto
    public function calculateNet($grossAmount)
    {
        return round($grossAmount / (1 + ($this->rate / 100)), 2);
    }
}

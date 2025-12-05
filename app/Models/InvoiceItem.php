<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $table = 'invoice_items';

    protected $fillable = [
        'invoice_id',
        'designation',
        'days',
        'unit_price',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function getTotalAttribute()
    {
        return $this->days * $this->unit_price;
    }
}

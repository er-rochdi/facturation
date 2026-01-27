<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;
    protected $table = 'invoices';
    protected $fillable = [
        'client_id',
        'invoice_number',
        'invoice_date',
        'amount',
        'due_date',
        'status',
        'notes',
        'pdf_path',
        'company_id',
        'month',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}

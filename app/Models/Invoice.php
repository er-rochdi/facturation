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
        'invoice_date',
        'amount',
        'due_date',
        'status',
        'notes',
        'pdf_path'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}

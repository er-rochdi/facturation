<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;

    protected $table = 'companies';

    protected $fillable = ['name', 'address', 'email', 'phone', 'website', 'logo', 'ice', 'if', 'rc', 'patente', 'cnie', 'type'];

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'company_id');
    }
}

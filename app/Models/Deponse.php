<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deponse extends Model
{
    use SoftDeletes;

    protected $table = 'deponses';

    protected $fillable = [
        'company_id',
        'category',
        'description',
        'amount',
        'date',
        'payment_method',
        'reference',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Catégories de dépenses disponibles
     */
    public static function categories(): array
    {
        return [
            'salaires' => 'Salaires',
            'loyer' => 'Loyer',
            'electricite' => 'Électricité',
            'eau' => 'Eau',
            'internet' => 'Internet & Téléphone',
            'fournitures' => 'Fournitures de bureau',
            'transport' => 'Transport',
            'maintenance' => 'Maintenance & Réparations',
            'marketing' => 'Marketing & Publicité',
            'assurance' => 'Assurance',
            'impots' => 'Impôts & Taxes',
            'banque' => 'Frais bancaires',
            'autres' => 'Autres',
        ];
    }

    /**
     * Méthodes de paiement disponibles
     */
    public static function paymentMethods(): array
    {
        return [
            'especes' => 'Espèces',
            'virement' => 'Virement bancaire',
            'cheque' => 'Chèque',
            'carte' => 'Carte bancaire',
        ];
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('deponses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->string('category'); // salaires, loyer, electricite, etc.
            $table->string('description');
            $table->decimal('amount', 12, 2);
            $table->date('date');
            $table->string('payment_method')->default('especes'); // especes, virement, cheque, carte
            $table->string('reference')->nullable(); // numéro de référence/facture
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Index pour les recherches fréquentes
            $table->index('category');
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deponses');
    }
};

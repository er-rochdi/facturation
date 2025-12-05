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
        // ICE column already exists from previous migration
        // Schema::table('clients', function (Blueprint $table) {
        //     $table->string('ice')->nullable()->after('address');
        // });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->string('designation');
            $table->integer('days')->default(1); // Nbr de jours
            $table->decimal('unit_price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
        
        // Schema::table('clients', function (Blueprint $table) {
        //     $table->dropColumn('ice');
        // });
    }
};

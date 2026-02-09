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
        Schema::create('financial_mutations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // Akun mana yang terpengaruh (misal: Uang masuk ke BCA)
            $table->uuid('financial_account_id')->index();

            // Kategori transaksi (misal: Penjualan)
            $table->uuid('financial_category_id')->index();

            // Polymorphic: Bisa terhubung ke Order (jika penjualan) atau null (jika bayar listrik)
            // Kolom ini akan menjadi: reference_id (UUID) dan reference_type (String)
            $table->nullableUuidMorphs('reference');

            $table->enum('flow', ['in', 'out']); // Uang masuk atau keluar
            $table->decimal('amount', 15, 2);

            $table->date('transaction_date');
            $table->text('description')->nullable();
            $table->string('proof_image')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('financial_account_id')->references('id')->on('financial_accounts')->onDelete('cascade');
            $table->foreign('financial_category_id')->references('id')->on('financial_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_mutations');
    }
};

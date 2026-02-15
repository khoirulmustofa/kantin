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
        Schema::create('tailor_uni_form_users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->constrained('users')->onDelete('cascade');
            $table->uuid('uniform_id')->constrained('uniforms')->onDelete('cascade');
            $table->enum('status', ['new', 'on_process', 'finished'])->default('new');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tailor_uni_form_users');
    }
};

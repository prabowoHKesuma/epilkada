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
        Schema::create('voters', function (Blueprint $table) {
            $table->id();
            $table->string('voter_code', 50)->unique();
        $table->foreignId('organization_id')->constrained('organizations');
        $table->foreignId('region_id')->constrained('regions');
        $table->string('name', 120);
        $table->string('nik_hash', 255);
        $table->string('kk_hash', 255);
        $table->text('address')->nullable();
        $table->string('phone', 30)->nullable();
        $table->string('rt', 10)->nullable();
        $table->string('rw', 10)->nullable();
        $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voters');
    }
};

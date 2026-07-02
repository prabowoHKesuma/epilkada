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
        Schema::create('elections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations');
        $table->foreignId('region_id')->constrained('regions');
        $table->string('title', 150);
        $table->text('description')->nullable();
        $table->enum('status', ['draft','open','closed','finished'])->default('draft');
        $table->dateTime('start_at')->nullable();
        $table->dateTime('end_at')->nullable();
        $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elections');
    }
};

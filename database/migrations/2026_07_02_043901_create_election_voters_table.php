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
        Schema::create('election_voters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_id')->constrained('elections');
        $table->foreignId('voter_id')->constrained('voters');
        $table->enum('allowed_channel', ['tps','remote','both'])->default('tps');
        $table->boolean('has_voted')->default(false);
        $table->dateTime('voted_at')->nullable();
            $table->timestamps();

            $table->unique(['election_id', 'voter_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('election_voters');
    }
};

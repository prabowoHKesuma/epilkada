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
        Schema::create('tps_booth_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_id')->constrained('elections');
        $table->foreignId('election_voter_id')->constrained('election_voters');
        $table->string('token_hash', 255);
        $table->dateTime('expires_at');
        $table->dateTime('used_at')->nullable();
        $table->dateTime('revoked_at')->nullable();
        $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tps_booth_tokens');
    }
};

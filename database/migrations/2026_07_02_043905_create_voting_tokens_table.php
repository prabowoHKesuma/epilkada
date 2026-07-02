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
        Schema::create('voting_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_id')->constrained('elections');
        $table->foreignId('voter_id')->constrained('voters');
        $table->foreignId('remote_verification_id')->nullable()->constrained('remote_verifications');
        $table->string('token_hash', 255);
        $table->dateTime('expires_at');
        $table->dateTime('used_at')->nullable();
        $table->dateTime('revoked_at')->nullable();
        $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voting_tokens');
    }
};

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
        Schema::create('remote_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_id')->constrained('elections');
        $table->foreignId('voter_id')->constrained('voters');
        $table->string('verification_code', 30);
        $table->string('upload_token_hash', 64)->nullable();
        $table->dateTime('upload_token_expires_at')->nullable();
        $table->dateTime('upload_uploaded_at')->nullable();
        $table->string('ktp_photo_path', 255)->nullable();
        $table->string('selfie_photo_path', 255)->nullable();
        $table->boolean('consent_accepted')->default(false);
        $table->dateTime('consent_at')->nullable();
        $table->enum('status', ['pending','approved','rejected'])->default('pending');
        $table->foreignId('verified_by_1')->nullable()->constrained('users');
        $table->foreignId('verified_by_2')->nullable()->constrained('users');
        $table->dateTime('verified_at')->nullable();
        $table->text('reject_reason')->nullable();
        $table->dateTime('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remote_verifications');
    }
};

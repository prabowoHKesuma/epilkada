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
        Schema::table('election_voters', function (Blueprint $table) {
            $table->string('invitation_token', 64)->nullable()->unique()->after('allowed_channel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('election_voters', function (Blueprint $table) {
            $table->dropColumn('invitation_token');
        });
    }
};

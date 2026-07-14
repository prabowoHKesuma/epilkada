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
        Schema::table('ballots', function (Blueprint $collection) {
            // Mengubah kolom pilihan kandidat menjadi TEXT agar bisa menampung string hasil enkripsi
            // Sesuaikan nama kolom dengan struktur asli Anda (misal: 'candidate_id' atau 'choice')
            $collection->text('encrypted_vote')->after('election_id');
            
            // Jika sebelumnya ada kolom candidate_id yang bersifat integer plain, kita bisa buat menjadi nullable atau menghapusnya demi keamanan
            $collection->unsignedBigInteger('candidate_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ballots', function (Blueprint $collection) {
            $collection->dropColumn('encrypted_vote');
            $collection->unsignedBigInteger('candidate_id')->nullable(false)->change();
        });
    }
};

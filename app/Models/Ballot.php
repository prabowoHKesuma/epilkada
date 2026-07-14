<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ballot extends Model
{
    use HasFactory;

    protected $table = 'ballots';
// app/Models/Ballot.php (lengkapi yang sudah ada)
protected $fillable = ['election_id',
'ballot_code', 
'vote_channel', 
'encrypted_vote'];
public $timestamps = false; // tabel ini cuma punya created_at, bukan updated_at

public function election() { return $this->belongsTo(Election::class); }
public function candidate() { return $this->belongsTo(Candidate::class); }
// TETAP SENGAJA tidak ada relasi ke Voter -- jaga kerahasiaan suara

}

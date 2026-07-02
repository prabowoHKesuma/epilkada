<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ballot extends Model
{
// app/Models/Ballot.php (lengkapi yang sudah ada)
protected $fillable = ['election_id', 'candidate_id', 'ballot_code', 'vote_channel'];
public $timestamps = false; // tabel ini cuma punya created_at, bukan updated_at

public function election() { return $this->belongsTo(Election::class); }
public function candidate() { return $this->belongsTo(Candidate::class); }
// TETAP SENGAJA tidak ada relasi ke Voter -- jaga kerahasiaan suara

}

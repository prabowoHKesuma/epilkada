<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TpsBoothToken extends Model
{
    // app/Models/TpsBoothToken.php (lengkapi yang sudah ada)
protected $fillable = ['election_id', 'election_voter_id', 'token_hash', 'expires_at', 'used_at', 'revoked_at', 'created_by'];
protected $casts = [
    'expires_at' => 'datetime',
    'used_at' => 'datetime',
    'revoked_at' => 'datetime',
];
public function election() { return $this->belongsTo(Election::class); }
public function electionVoter() { return $this->belongsTo(ElectionVoter::class); }
public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}

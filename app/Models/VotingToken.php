<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VotingToken extends Model
{
    // app/Models/VotingToken.php
protected $fillable = [
    'election_id', 'voter_id', 'remote_verification_id',
    'token_hash', 'raw_token_temp', 'expires_at', 'used_at', 'revoked_at', 'created_by',
];

protected $casts = [
    'expires_at' => 'datetime',
    'used_at' => 'datetime',
    'revoked_at' => 'datetime',
];

public function election() { return $this->belongsTo(Election::class); }
public function voter() { return $this->belongsTo(Voter::class); }
public function remoteVerification() { return $this->belongsTo(RemoteVerification::class); }
public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}

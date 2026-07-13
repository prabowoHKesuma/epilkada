<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElectionVoter extends Model
{
    protected $fillable = ['election_id', 'voter_id', 'allowed_channel', 'has_voted', 'voted_at', 'invitation_token'];
    public function election() { return $this->belongsTo(Election::class); }
public function voter() { return $this->belongsTo(Voter::class); }
public function tpsBoothToken() { return $this->hasOne(TpsBoothToken::class); }
public function latestToken()
{
    return $this->hasOne(TpsBoothToken::class)->latestOfMany();
}
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\RegionScope;

class Voter extends Model
{
    // app/Models/Voter.php (lengkapi yang sudah ada)
protected $fillable = ['voter_code', 'organization_id', 'region_id', 'name', 'nik_hash', 'kk_hash', 'address', 'phone', 'rt', 'rw', 'is_active'];
protected $hidden = ['nik_hash', 'kk_hash'];

public function organization() { return $this->belongsTo(Organization::class); }
public function region() { return $this->belongsTo(Region::class); }
public function electionVoters() { return $this->hasMany(ElectionVoter::class); }
public function remoteVerifications() { return $this->hasMany(RemoteVerification::class); }
public function votingTokens() { return $this->hasMany(VotingToken::class); }
protected static function booted(): void
{
    static::addGlobalScope(new RegionScope);
}
}

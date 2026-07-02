<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Election extends Model
{
    // app/Models/Election.php
public function organization() { return $this->belongsTo(Organization::class); }
public function region() { return $this->belongsTo(Region::class); }
public function creator() { return $this->belongsTo(User::class, 'created_by'); }
public function candidates() { return $this->hasMany(Candidate::class); }
public function electionVoters() { return $this->hasMany(ElectionVoter::class); }
public function ballots() { return $this->hasMany(Ballot::class); }
}

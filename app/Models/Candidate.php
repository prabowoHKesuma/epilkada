<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    // app/Models/Candidate.php
public function election() { return $this->belongsTo(Election::class); }
public function ballots() { return $this->hasMany(Ballot::class); }
}

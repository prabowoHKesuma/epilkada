<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    protected $fillable = [
    'election_id',
    'number_order',
    'name',
    'photo',
    'vision',
    'mission',
    'is_active',
];
    // app/Models/Candidate.php
public function election() { return $this->belongsTo(Election::class); }
public function ballots() { return $this->hasMany(Ballot::class); }
}

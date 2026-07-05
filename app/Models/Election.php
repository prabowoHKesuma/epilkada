<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\RegionScope;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Election extends Model
{
    protected $fillable = [
        'organization_id',
        'region_id',
        'title',
        'description',
        'status',
        'start_at',
        'end_at',
        'created_by',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];
    // app/Models/Election.php
public function organization() { return $this->belongsTo(Organization::class); }
public function region() { return $this->belongsTo(Region::class); }
public function creator() { return $this->belongsTo(User::class, 'created_by'); }
public function candidates() { return $this->hasMany(Candidate::class); }
public function electionVoters() { return $this->hasMany(ElectionVoter::class); }
public function ballots() { return $this->hasMany(Ballot::class); }
protected static function booted(): void
{
    static::addGlobalScope(new RegionScope);
}
public function eligibleVoters(): HasMany
    {
        return $this->hasMany(ElectionVoter::class, 'election_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    // app/Models/AuditLog.php
protected $fillable = [
    'user_id', 'organization_id', 'region_id', 'election_id',
    'action', 'description', 'ip_address', 'user_agent', 'created_at',
];
public $timestamps = false; // hanya created_at
protected $casts = [
    'created_at' => 'datetime',
];

public function user() { return $this->belongsTo(User::class); }
public function organization() { return $this->belongsTo(Organization::class); }
public function region() { return $this->belongsTo(Region::class); }
public function election() { return $this->belongsTo(Election::class); }
}

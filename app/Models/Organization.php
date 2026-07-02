<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    // app/Models/Organization.php
protected $fillable = ['name', 'type', 'description', 'is_active'];

public function regions() { return $this->hasMany(Region::class); }
public function users() { return $this->hasMany(User::class); }
public function elections() { return $this->hasMany(Election::class); }
public function voters() { return $this->hasMany(Voter::class); }
}

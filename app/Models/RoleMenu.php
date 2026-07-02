<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class RoleMenu extends Model
{
    // app/Models/RoleMenu.php
protected $fillable = ['role_id', 'menu_id'];

public function role() { return $this->belongsTo(Role::class); }
public function menu() { return $this->belongsTo(Menu::class); }
}

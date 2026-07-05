<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Menu extends Model
{
    use HasFactory;
    // app/Models/Menu.php
protected $fillable = ['parent_id', 'menu_key', 'title', 'url', 'icon_class', 'group_name', 'sort_order', 'is_active'];

public function parent() { return $this->belongsTo(Menu::class, 'parent_id'); }
public function children() { return $this->hasMany(Menu::class, 'parent_id'); }
public function roleMenus() { return $this->hasMany(RoleMenu::class); }
}

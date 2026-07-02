<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
   protected $fillable = ['organization_id', 'parent_id', 'level', 'code', 'name'];

    // Region ini milik organization mana
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    // Region ini punya induk (parent) region apa -- misal RT induknya RW
    public function parent()
    {
        return $this->belongsTo(Region::class, 'parent_id');
    }

    // Region ini punya anak-anak region apa saja -- misal kelurahan punya banyak RW
    public function children()
    {
        return $this->hasMany(Region::class, 'parent_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildingImages extends Model
{
    use HasFactory;

    protected $table='building_images';
    protected $fillable = [
        'building_id','image_path'
    ];

    public function building()
    {
        return $this->belongsTo(Building::class, 'building_id');
    }
}

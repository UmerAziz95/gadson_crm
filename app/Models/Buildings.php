<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buildings extends Model
{
    use HasFactory;
    protected $table = 'buildings';
    protected $fillable = [
        'building_name','building_type','building_address','number_of_appartments','number_of_floors','country','state','city','building_number','total_parkings','owner_name','building_size','building_description','status'
    ];

    public function images()
    {
        return $this->hasMany(BuildingImages::class, 'building_id');
    }
    public function appartments()
    {
        return $this->hasMany(Appartment::class, 'building_id');
    }
    public function tasks()
    {
        return $this->hasMany(Tasks::class, 'building');
    }
}

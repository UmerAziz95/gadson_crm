<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appartment extends Model
{
    use HasFactory;
    protected $table = 'apartments';
    protected $fillable = [
        'building_id',
        'apartment_no',
        'apartment_name',
        'category',
        'apartment_type',
        'number_of_rooms',
        'apartment_size',
        'unit_purchase_price',
        'landlord_name',
        'landlord_contact_number',
        'reference_number',
        'description',
        'status',
    ];

    public function building()
    {
        return $this->belongsTo(Buildings::class, 'building_id');
    }
    public function images()
    {
        return $this->hasMany(AppartmentImages::class, 'appartment_id');
    }
    public function tasks()
    {
        return $this->hasMany(Tasks::class, 'apartment');
    }
}

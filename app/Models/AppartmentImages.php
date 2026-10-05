<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppartmentImages extends Model
{
    use HasFactory;
    protected $table = 'appartment_images';
    protected $fillable = [
        'building_id','image_path','created_by'
    ];

    public function appartment()
    {
        return $this->belongsTo(Appartment::class, 'appartment_id');
    }
}

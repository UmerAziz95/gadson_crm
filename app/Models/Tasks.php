<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tasks extends Model
{
    use HasFactory;
    protected $table='tasks';
    protected $fillable = [

        'task_number',
        'task_title',
        'priority',
        'document',
        'document_type',
        'building',
        'appartment',
        'manager',
        'description',
        'document_status',
        'status',
        'created_by',
        'updated_by'
    ];

    public function building()
    {
        return $this->belongsTo(Buildings::class, 'building');
    }
    public function appartment()
    {
        return $this->belongsTo(Appartment::class, 'apartment');
    }
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager');
    }
    // TaskNotifications get latest notification for task
    public function taskNotifications()
    {
        return $this->hasMany(TaskNotifications::class, 'task_id')->latest();
    }
    // createdBy
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    // updatedBy
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}

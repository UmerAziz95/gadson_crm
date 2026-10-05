<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskNotifications extends Model
{
    use HasFactory;
    protected $table = 'task_notifications';
    protected $fillable = [
        'task_id','admin_email','manager_email','comment','created_by','task_status','attachment','action','created_by','manager_id'
    ];
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
}

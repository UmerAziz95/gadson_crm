<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskToDoList extends Model
{
    use HasFactory;
    protected $table = 'task_to_do_list';
    protected $fillable = [
        'task_id','to_do_item','created_by'
    ];
}

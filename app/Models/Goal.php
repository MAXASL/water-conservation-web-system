<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'target_usage']; // Ensure user_id is fillable

    protected $table = 'goals'; // Define table name if necessary
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class HomeUsage extends Model
{
    use HasFactory;

    protected $table = 'sensor';
    // app/Models/HomeUsage.php
protected $fillable = [
    'user_id', 'area', 'usage', 'flow_rate', 'total_used', 'date', 'created_at', 'updated_at'
];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}



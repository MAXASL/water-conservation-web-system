<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leak extends Model
{
    use HasFactory;

    protected $fillable = [
        'location', 'description', 'severity', 'image', 'contact_info', 'status'
    ];
}


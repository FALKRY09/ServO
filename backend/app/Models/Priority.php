<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Priority extends Model
{
    protected $fillable = ['name', 'level', 'response_target_minutes', 'resolution_target_minutes', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
}

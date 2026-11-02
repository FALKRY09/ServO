<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SlaPolicy extends Model
{
    protected $table = 'sla_policies';
    protected $fillable = ['name', 'description', 'priority_id', 'category_id', 'response_target_minutes', 'resolution_target_minutes', 'business_hours_only', 'is_active'];
    protected $casts = ['business_hours_only' => 'boolean', 'is_active' => 'boolean'];

    public function priority() { return $this->belongsTo(Priority::class); }
    public function category() { return $this->belongsTo(Category::class); }
}

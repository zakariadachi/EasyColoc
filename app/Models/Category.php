<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    public $timestamps = false;
    
    protected $fillable = ['name', 'icon', 'color', 'colocation_id'];

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function colocation(): BelongsTo
    {
        return $this->belongsTo(Colocation::class);
    }
}

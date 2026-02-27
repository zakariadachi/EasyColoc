<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Colocation extends Model
{
    protected $fillable = ['name', 'status', 'owner_id'];

    public $timestamps = false;

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'colocation_user')
            ->withPivot('role', 'joined_at', 'left_at');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'type',
        'color',
        'icon',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope to filter categories for a specific user.
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where($this->getTable() . '.user_id', $userId);
    }

    /**
     * Scope to filter income categories.
     */
    public function scopeIncome(Builder $query): Builder
    {
        return $query->where($this->getTable() . '.type', 'income');
    }

    /**
     * Scope to filter expense categories.
     */
    public function scopeExpense(Builder $query): Builder
    {
        return $query->where($this->getTable() . '.type', 'expense');
    }
}

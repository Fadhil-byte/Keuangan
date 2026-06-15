<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'category_id',
        'type',
        'amount',
        'description',
        'transaction_date',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Format amount as Indonesian Rupiah.
     */
    protected function formattedAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => 'Rp ' . number_format((float) $this->amount, 0, ',', '.'),
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where($this->getTable() . '.user_id', $userId);
    }

    public function scopeIncome(Builder $query): Builder
    {
        return $query->where($this->getTable() . '.type', 'income');
    }

    public function scopeExpense(Builder $query): Builder
    {
        return $query->where($this->getTable() . '.type', 'expense');
    }

    /**
     * Filter transactions within a date range.
     */
    public function scopeDateRange(Builder $query, ?string $from, ?string $to): Builder
    {
        if ($from) {
            $query->where($this->getTable() . '.transaction_date', '>=', $from);
        }
        if ($to) {
            $query->where($this->getTable() . '.transaction_date', '<=', $to);
        }
        return $query;
    }
}

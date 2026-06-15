<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Bill extends Model
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
        'title',
        'amount',
        'description',
        'due_date',
        'status',
        'recurrence',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'due_date' => 'date',
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
     * Check if the bill is overdue.
     */
    protected function isOverdue(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === 'unpaid' && $this->due_date->isPast(),
        );
    }

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

    public function scopePaid(Builder $query): Builder
    {
        return $query->where($this->getTable() . '.status', 'paid');
    }

    public function scopeUnpaid(Builder $query): Builder
    {
        return $query->where($this->getTable() . '.status', 'unpaid');
    }

    /**
     * Bills due within the next N days.
     */
    public function scopeUpcoming(Builder $query, int $days = 7): Builder
    {
        return $query->where($this->getTable() . '.status', 'unpaid')
                     ->whereBetween($this->getTable() . '.due_date', [Carbon::today(), Carbon::today()->addDays($days)]);
    }

    /**
     * Overdue unpaid bills.
     */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where($this->getTable() . '.status', 'unpaid')
                     ->where($this->getTable() . '.due_date', '<', Carbon::today());
    }
}

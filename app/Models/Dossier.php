<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dossier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'created_by',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Status constants
    const STATUS_DRAFT      = 'draft';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED  = 'completed';
    const STATUS_CANCELLED  = 'cancelled';

    const STATUSES = [
        self::STATUS_DRAFT      => 'Nháp',
        self::STATUS_PROCESSING => 'Đang xử lý',
        self::STATUS_COMPLETED  => 'Hoàn thành',
        self::STATUS_CANCELLED  => 'Đã hủy',
    ];

    const STATUS_COLORS = [
        self::STATUS_DRAFT      => 'secondary',
        self::STATUS_PROCESSING => 'warning',
        self::STATUS_COMPLETED  => 'success',
        self::STATUS_CANCELLED  => 'danger',
    ];

    // Relationships
    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(\App\Models\Contract::class)->orderBy('created_at', 'desc');
    }

    public function activeContracts(): HasMany
    {
        return $this->hasMany(\App\Models\Contract::class)->where('status', 'completed');
    }

    public function draftContracts(): HasMany
    {
        return $this->hasMany(\App\Models\Contract::class)->where('status', 'draft');
    }

    // Accessors
    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'secondary';
    }

    public function getContractsCountAttribute(): int
    {
        return $this->contracts()->count();
    }

    public function getTotalTransactionValueAttribute(): float
    {
        return $this->contracts()
            ->whereNotNull('transaction_value')
            ->sum('transaction_value');
    }

    public function getFormattedTotalTransactionValueAttribute(): string
    {
        return number_format($this->total_transaction_value, 0, ',', '.') . ' VNĐ';
    }

    // Scopes
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByCreator($query, int $userId)
    {
        return $query->where('created_by', $userId);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
                ->orWhere('description', 'LIKE', "%{$search}%");
        });
    }

    // Helper methods
    public function canBeUpdated(): bool
    {
        return $this->status !== self::STATUS_CANCELLED;
    }

    public function canBeCompleted(): bool
    {
        return $this->status === self::STATUS_PROCESSING &&
               $this->contracts()->where('status', 'completed')->count() > 0;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_PROCESSING]);
    }

    public function markAsProcessing(): bool
    {
        if ($this->status === self::STATUS_DRAFT) {
            return $this->update(['status' => self::STATUS_PROCESSING]);
        }
        return false;
    }

    public function markAsCompleted(): bool
    {
        if (in_array($this->status, [self::STATUS_DRAFT, self::STATUS_PROCESSING])) {
            return $this->update(['status' => self::STATUS_COMPLETED]);
        }
        return false;
    }

    public function markAsCancelled(): bool
    {
        if ($this->canBeCancelled()) {
            return $this->update(['status' => self::STATUS_CANCELLED]);
        }
        return false;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'dossier_id',
        'contract_template_id',
        'contract_number',
        'contract_date',
        'transaction_value',
        'content',
        'clauses',
        'testimonial_content',
        'notary_fee',
        'notary_number',
        'book_number',
        'additional_info',
        'status'
    ];

    protected $casts = [
        'contract_date' => 'date',
        'transaction_value' => 'decimal:2',
        'clauses' => 'array',
        'additional_info' => 'array',
    ];

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_COMPLETED = 'completed';

    const STATUSES = [
        self::STATUS_DRAFT => 'Nháp',
        self::STATUS_COMPLETED => 'Hoàn thành',
    ];

    const STATUS_COLORS = [
        self::STATUS_DRAFT => 'warning',
        self::STATUS_COMPLETED => 'success',
    ];

    // Relationships
    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(ContractTemplate::class, 'contract_template_id');
    }

    public function parties(): HasMany
    {
        return $this->hasMany(ContractParty::class)->orderBy('group_name')->orderBy('order_in_group');
    }

    public function assets(): BelongsToMany
    {
        return $this->belongsToMany(Asset::class, 'contract_asset')
                   ->withPivot('notes')
                   ->withTimestamps();
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

    public function getFormattedTransactionValueAttribute(): string
    {
        if (!$this->transaction_value) {
            return 'Chưa xác định';
        }
        return number_format($this->transaction_value, 0, ',', '.') . ' VNĐ';
    }

    public function getPartiesByGroupAttribute()
    {
        return $this->parties->groupBy('group_name');
    }

    public function getPartiesCountAttribute(): int
    {
        return $this->parties()->count();
    }

    public function getAssetsCountAttribute(): int
    {
        return $this->assets()->count();
    }

    public function getClausesAttribute($value): array
    {
        return json_decode($value, true) ?: [];
    }

    public function getAdditionalInfoAttribute($value): array
    {
        return json_decode($value, true) ?: [];
    }

    // Scopes
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByDossier($query, int $dossierId)
    {
        return $query->where('dossier_id', $dossierId);
    }

    public function scopeByTemplate($query, int $templateId)
    {
        return $query->where('contract_template_id', $templateId);
    }

    public function scopeWithTransactionValue($query)
    {
        return $query->whereNotNull('transaction_value')->where('transaction_value', '>', 0);
    }

    // Helper methods
    public function canBeUpdated(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function canBeCompleted(): bool
    {
        return $this->status === self::STATUS_DRAFT &&
               $this->parties()->count() >= $this->template->getRequiredPartiesMin() &&
               $this->assets()->count() >= $this->template->getRequiredAssetsMin();
    }

    public function markAsCompleted(): bool
    {
        if ($this->canBeCompleted()) {
            $success = $this->update(['status' => self::STATUS_COMPLETED]);

            // Auto update dossier status if needed
            if ($success && $this->dossier->status === Dossier::STATUS_DRAFT) {
                $this->dossier->markAsProcessing();
            }

            return $success;
        }
        return false;
    }

    public function generateContractNumber(): string
    {
        $year = $this->contract_date->format('Y');
        $month = $this->contract_date->format('m');

        // Count contracts in the same month
        $count = static::whereYear('contract_date', $year)
                      ->whereMonth('contract_date', $month)
                      ->count() + 1;

        return sprintf('%s%s-%04d', $year, $month, $count);
    }

    public function getProcessedContent(): string
    {
        $content = $this->content ?: $this->template->content;

        // Replace template variables
        $variables = [
            'current_date' => $this->contract_date->format('d/m/Y'),
            'transaction_value' => $this->formatted_transaction_value,
            'contract_number' => $this->contract_number,
            'notary_fee' => $this->notary_fee,
            'notary_number' => $this->notary_number,
            'book_number' => $this->book_number,
        ];

        // Merge with template info
        $templateInfo = $this->template->template_info;
        $allVariables = array_merge($templateInfo, $variables);

        foreach ($allVariables as $key => $value) {
            $placeholder = "{{" . $key . "}}";
            $content = str_replace($placeholder, $value, $content);
        }

        return $content;
    }

    public function hasRequiredData(): bool
    {
        return $this->parties()->count() >= $this->template->getRequiredPartiesMin() &&
               $this->assets()->count() >= $this->template->getRequiredAssetsMin();
    }
}

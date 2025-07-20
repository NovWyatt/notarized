<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class ContractType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function templates(): HasMany
    {
        return $this->hasMany(ContractTemplate::class)->orderBy('sort_order');
    }

    public function activeTemplates(): HasMany
    {
        return $this->hasMany(ContractTemplate::class)
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    public function contracts(): HasManyThrough
    {
        return $this->hasManyThrough(Contract::class, ContractTemplate::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Accessors
    public function getTemplatesCountAttribute(): int
    {
        return $this->templates()->count();
    }

    public function getActiveTemplatesCountAttribute(): int
    {
        return $this->activeTemplates()->count();
    }

    public function getContractsCountAttribute(): int
    {
        return $this->contracts()->count();
    }

    // Helper methods
    public function hasActiveTemplates(): bool
    {
        return $this->activeTemplates()->exists();
    }

    public function canBeDeleted(): bool
    {
        return $this->contracts()->count() === 0;
    }
}

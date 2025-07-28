<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractParty extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'litigant_id',
        'party_type',
        'group_name',
        'order_in_group',
        'notes',
    ];

    // Party type constants
    const PARTY_TYPES = [
        'transferor' => 'Bên chuyển giao',
        'transferee' => 'Bên nhận chuyển giao',
        'buyer' => 'Bên mua',
        'seller' => 'Bên bán',
        'lender' => 'Bên cho vay',
        'borrower' => 'Bên đi vay',
        'lessor' => 'Bên cho thuê',
        'lessee' => 'Bên thuê',
        'guarantor' => 'Bên bảo lãnh',
        'witness' => 'Người chứng kiến',
        'other' => 'Khác',
    ];

    // Group name constants
    const GROUP_NAMES = [
        'Bên A',
        'Bên B',
        'Bên C',
        'Bên thứ nhất',
        'Bên thứ hai',
        'Bên thứ ba',
        'Bên chuyển giao',
        'Bên nhận chuyển giao',
        'Bên mua',
        'Bên bán',
    ];

    // Relationships
    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function litigant(): BelongsTo
    {
        return $this->belongsTo(Litigant::class);
    }

    // Accessors
    public function getPartyTypeLabelAttribute(): string
    {
        return self::PARTY_TYPES[$this->party_type] ?? $this->party_type;
    }

    public function getFullInfoAttribute(): string
    {
        return "{$this->group_name} - {$this->litigant->full_name} ({$this->party_type_label})";
    }

    // Scopes
    public function scopeByContract($query, int $contractId)
    {
        return $query->where('contract_id', $contractId);
    }

    public function scopeByGroup($query, string $groupName)
    {
        return $query->where('group_name', $groupName);
    }

    public function scopeByPartyType($query, string $partyType)
    {
        return $query->where('party_type', $partyType);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('group_name')->orderBy('order_in_group');
    }

    // Helper methods
    public function moveToGroup(string $newGroupName, int $newOrder = 1): bool
    {
        return $this->update([
            'group_name' => $newGroupName,
            'order_in_group' => $newOrder,
        ]);
    }

    public function changePartyType(string $newPartyType): bool
    {
        if (array_key_exists($newPartyType, self::PARTY_TYPES)) {
            return $this->update(['party_type' => $newPartyType]);
        }
        return false;
    }

    public function getDisplayOrder(): string
    {
        $groupParties = static::byContract($this->contract_id)
            ->byGroup($this->group_name)
            ->ordered()
            ->get();

        $position = $groupParties->search(fn($party) => $party->id === $this->id) + 1;

        return $position > 1 ? " (người thứ {$position})" : '';
    }

    public function isFirstInGroup(): bool
    {
        return $this->order_in_group === 1;
    }

    public function canBeReordered(): bool
    {
        return static::byContract($this->contract_id)
            ->byGroup($this->group_name)
            ->count() > 1;
    }
}

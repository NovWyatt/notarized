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
        'notes'
    ];

    protected $casts = [
        'order_in_group' => 'integer',
    ];

    // Party type constants
    const TYPE_TRANSFEROR = 'transferor';
    const TYPE_TRANSFEREE = 'transferee';
    const TYPE_BUYER = 'buyer';
    const TYPE_SELLER = 'seller';
    const TYPE_LESSOR = 'lessor';
    const TYPE_LESSEE = 'lessee';
    const TYPE_OTHER = 'other';

    const PARTY_TYPES = [
        self::TYPE_TRANSFEROR => 'Bên chuyển nhượng',
        self::TYPE_TRANSFEREE => 'Bên nhận chuyển nhượng',
        self::TYPE_BUYER => 'Bên mua',
        self::TYPE_SELLER => 'Bên bán',
        self::TYPE_LESSOR => 'Bên cho thuê',
        self::TYPE_LESSEE => 'Bên thuê',
        self::TYPE_OTHER => 'Khác',
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

    // Scopes
    public function scopeByGroup($query, string $groupName)
    {
        return $query->where('group_name', $groupName);
    }

    public function scopeByType($query, string $partyType)
    {
        return $query->where('party_type', $partyType);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('group_name')->orderBy('order_in_group');
    }

    // Helper methods
    public function getDisplayName(): string
    {
        return $this->litigant->full_name;
    }

    public function getIdentityInfo(): string
    {
        $identityDoc = $this->litigant->identityDocuments->first();
        if ($identityDoc) {
            return strtoupper($identityDoc->document_type) . ': ' . $identityDoc->document_number;
        }
        return '';
    }

    public function getFullInfo(): array
    {
        return [
            'name' => $this->litigant->full_name,
            'party_type' => $this->party_type_label,
            'group_name' => $this->group_name,
            'identity' => $this->getIdentityInfo(),
            'notes' => $this->notes,
        ];
    }
}

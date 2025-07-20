<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContractTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contract_type_id',
        'content',
        'template_settings',
        'template_info',
        'default_clauses',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'template_settings' => 'array',
        'template_info' => 'array',
        'default_clauses' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Default template settings
    const DEFAULT_SETTINGS = [
        'show_parties' => true,
        'show_assets' => true,
        'show_clauses' => true,
        'show_testimonial' => true,
        'show_transaction_value' => true,
        'show_signatures' => true,
        'show_notary_info' => true,
        'required_parties_min' => 2,
        'required_assets_min' => 1,
    ];

    // Relationships
    public function contractType(): BelongsTo
    {
        return $this->belongsTo(ContractType::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    // Accessors
    public function getTemplateSettingsAttribute($value): array
    {
        $settings = json_decode($value, true) ?: [];
        return array_merge(self::DEFAULT_SETTINGS, $settings);
    }

    public function getTemplateInfoAttribute($value): array
    {
        $info = json_decode($value, true) ?: [];

        // Trả về template info với giá trị mặc định
        return array_merge([
            'current_user' => auth()->user()->name ?? 'Công chứng viên',
            'current_date' => now()->format('d/m/Y'),
            'office_name' => 'Văn phòng công chứng Nguyễn Thị Như Trang',
            'office_address' => '320, đường ĐT743A, khu phố Trung Thắng, phường Bình Thắng, thành phố Dĩ An, tỉnh Bình Dương',
            'province' => 'tỉnh Bình Dương',
        ], $info);
    }

    public function getDefaultClausesAttribute($value): array
    {
        return json_decode($value, true) ?: [];
    }

    public function getContractsCountAttribute(): int
    {
        return $this->contracts()->count();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, int $contractTypeId)
    {
        return $query->where('contract_type_id', $contractTypeId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Helper methods
    public function shouldShow(string $section): bool
    {
        $settings = $this->template_settings;
        return $settings["show_{$section}"] ?? false;
    }

    public function getRequiredPartiesMin(): int
    {
        return $this->template_settings['required_parties_min'] ?? 2;
    }

    public function getRequiredAssetsMin(): int
    {
        return $this->template_settings['required_assets_min'] ?? 1;
    }

    public function canBeDeleted(): bool
    {
        return $this->contracts()->count() === 0;
    }

    public function generateContent(array $variables = []): string
    {
        $content = $this->content;
        $templateInfo = $this->template_info;

        // Merge template info with custom variables
        $allVariables = array_merge($templateInfo, $variables);

        // Replace template variables
        foreach ($allVariables as $key => $value) {
            $placeholder = "{{" . $key . "}}";
            $content = str_replace($placeholder, $value, $content);
        }

        return $content;
    }

    public function getProcessedDefaultClauses(): array
    {
        $clauses = $this->default_clauses;

        foreach ($clauses as &$clause) {
            if (isset($clause['content'])) {
                // Replace variables in clause content
                $clause['content'] = str_replace(
                    '{{transaction_value}}',
                    '[Giá trị giao dịch]',
                    $clause['content']
                );
            }
        }

        return $clauses;
    }
}

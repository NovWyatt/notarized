<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

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
        'sort_order',
    ];

    protected $casts = [
        'template_settings' => 'array',
        'template_info'     => 'array',
        // 'default_clauses'   => 'array',
        'is_active'         => 'boolean',
        'sort_order'        => 'integer',
    ];

    // Default template settings
    const DEFAULT_SETTINGS = [
        'show_parties'           => true,
        'show_assets'            => true,
        'show_clauses'           => true,
        'show_testimonial'       => true,
        'show_transaction_value' => true,
        'show_signatures'        => true,
        'show_notary_info'       => true,
        'required_parties_min'   => 2,
        'required_assets_min'    => 1,
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
        if (empty($value) || $value === '[]' || $value === 'null') {
            $settings = [];
        } else {
            $settings = json_decode($value, true);
            if (! is_array($settings)) {
                $settings = [];
            }
        }

        $defaults = [
            'show_parties'           => true,
            'show_assets'            => true,
            'show_clauses'           => true,
            'show_testimonial'       => true,
            'show_transaction_value' => true,
            'show_signatures'        => true,
            'show_notary_info'       => true,
            'required_parties_min'   => 2,
            'required_assets_min'    => 1,
        ];

        return array_merge($defaults, $settings);
    }

    public function getTemplateInfoAttribute($value): array
    {
        if (empty($value) || $value === 'null') {
            $info = [];
        } else {
            $info = json_decode($value, true);
            if (! is_array($info)) {
                $info = [];
            }
        }

        // CHỈ trả về dữ liệu từ database, KHÔNG merge với giá trị mặc định
        // Vì accessor này được gọi khi hiển thị template, không phải khi tạo hợp đồng
        return $info;
    }

    public function getTemplateInfoWithDefaults(): array
    {
        $info = $this->template_info; // Gọi accessor trên

        // Merge với default values chỉ khi cần thiết (ví dụ: khi tạo hợp đồng)
        return array_merge([
            'current_user'   => auth()->user()->name ?? 'Công chứng viên',
            'office_name'    => 'Văn phòng công chứng Nguyễn Thị Như Trang',
            'office_address' => '320, đường ĐT743A, khu phố Trung Thắng, phường Bình Thắng, thành phố Dĩ An, tỉnh Bình Dương',
            'province'       => 'tỉnh Bình Dương',
        ], $info);
    }

    public function getDefaultClausesAttribute($value): array
    {
        if (empty($value) || $value === 'null') {
            return [];
        }

        // BƯỚC 1: Decode lần đầu (vì bị double-encoded)
        $firstDecode = json_decode($value, true);

        // if (json_last_error() !== JSON_ERROR_NONE) {
        //     Log::warning('First JSON decode failed:', ['error' => json_last_error_msg()]);
        //     return [];
        // }

        // BƯỚC 2: Nếu kết quả là string, decode lần nữa
        if (is_string($firstDecode)) {
            $secondDecode = json_decode($firstDecode, true);

            // if (json_last_error() !== JSON_ERROR_NONE) {
            //     Log::warning('Second JSON decode failed:', ['error' => json_last_error_msg()]);
            //     return [];
            // }

            $decoded = $secondDecode;
        } else {
            $decoded = $firstDecode;
        }

        if (! is_array($decoded)) {
            return [];
        }

        // BƯỚC 3: Chuyển object với key string thành indexed array
        $result = [];
        foreach ($decoded as $key => $clause) {
            if (is_array($clause)) {
                // Đảm bảo is_required là boolean
                if (isset($clause['is_required'])) {
                    $clause['is_required'] = (bool) $clause['is_required'];
                }
                $result[] = $clause;
            }
        }

        // Log::info('Successfully decoded clauses:', ['count' => count($result)]);
        return $result;
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

        // Sử dụng method mới để lấy template info với defaults
        $templateInfo = $this->getTemplateInfoWithDefaults();

        // Merge template info với custom variables
        $allVariables = array_merge($templateInfo, $variables);

        // Replace template variables
        foreach ($allVariables as $key => $value) {
            $placeholder = "{{" . $key . "}}";
            $content     = str_replace($placeholder, $value, $content);
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

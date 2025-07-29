<?php
namespace App\Services;

use App\Models\Contract;
use App\Models\ContractTemplate;
use App\Models\Dossier;
use App\Models\Litigant;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;

class ContractService
{
    /**
     * Tạo hợp đồng từ template với thông tin đương sự và tài sản
     */
    public function createContractFromTemplate(
        Dossier $dossier,
        ContractTemplate $template,
        array $contractData,
        array $parties = [],
        array $assets = []
    ): Contract {

        // Tạo hợp đồng
        $contract = Contract::create([
            'dossier_id'           => $dossier->id,
            'contract_template_id' => $template->id,
            'contract_number'      => $contractData['contract_number'] ?? null,
            'contract_date'        => $contractData['contract_date'],
            'transaction_value'    => $contractData['transaction_value'] ?? null,
            'notary_fee'           => $contractData['notary_fee'] ?? null,
            'notary_number'        => $contractData['notary_number'] ?? null,
            'book_number'          => $contractData['book_number'] ?? null,
            'clauses'              => $contractData['clauses'] ?? $template->default_clauses,
            'additional_info'      => $contractData['additional_info'] ?? [],
            'status'               => Contract::STATUS_DRAFT,
        ]);

        // Thêm đương sự
        if (! empty($parties)) {
            $this->attachPartiesToContract($contract, $parties);
        }

        // Thêm tài sản
        if (! empty($assets)) {
            $this->attachAssetsToContract($contract, $assets);
        }

        // Sinh nội dung hợp đồng từ template
        $this->generateContractContent($contract);

        return $contract;
    }

    /**
     * Gắn đương sự vào hợp đồng
     */
    private function attachPartiesToContract(Contract $contract, array $parties): void
    {
        foreach ($parties as $partyData) {
            $contract->parties()->create([
                'litigant_id'    => $partyData['litigant_id'],
                'party_type'     => $partyData['party_type'],
                'group_name'     => $partyData['group_name'],
                'order_in_group' => $partyData['order_in_group'] ?? 1,
                'notes'          => $partyData['notes'] ?? null,
            ]);
        }
    }

    /**
     * Gắn tài sản vào hợp đồng
     */
    private function attachAssetsToContract(Contract $contract, array $assets): void
    {
        foreach ($assets as $assetData) {
            $contract->assets()->attach($assetData['asset_id'], [
                'notes' => $assetData['notes'] ?? null,
            ]);
        }
    }

    /**
     * Sinh nội dung hợp đồng từ template
     */
    public function generateContractContent(Contract $contract): void
    {
        $template        = $contract->template;
        $templateContent = $template->content;

        // Lấy dữ liệu để thay thế
        $replacementData = $this->getReplacementData($contract);

        // Thay thế placeholders trong template
        $processedContent = $this->processTemplate($templateContent, $replacementData);

        // Cập nhật nội dung hợp đồng
        $contract->update(['content' => $processedContent]);
    }

    /**
     * Lấy dữ liệu để thay thế trong template
     */
    private function getReplacementData(Contract $contract): array
    {
        $dossier = $contract->dossier;
        $parties = $contract->parties()->with('litigant')->get();
        $assets  = $contract->assets()->get();

        // Nhóm đương sự theo group
        $partiesByGroup = $parties->groupBy('group_name');

        // Dữ liệu cơ bản
        $data = [
            // Thông tin hợp đồng
            'contract_number'     => $contract->contract_number,
            'contract_date'       => $contract->contract_date->format('d/m/Y'),
            'transaction_value'   => $contract->formatted_transaction_value,
            'notary_fee'          => $contract->notary_fee,
            'notary_number'       => $contract->notary_number,
            'book_number'         => $contract->book_number,

            // Thông tin hồ sơ
            'dossier_name'        => $dossier->name,
            'dossier_description' => $dossier->description,

            // Thời gian hiện tại
            'current_date'        => now()->format('d/m/Y'),
            'current_time'        => now()->format('H:i'),
            'current_year'        => now()->format('Y'),
        ];

        // Thêm thông tin đương sự theo nhóm
        foreach ($partiesByGroup as $groupName => $groupParties) {
            $data["parties_{$groupName}"] = $this->formatPartiesGroup($groupParties);

            // Thêm từng đương sự riêng lẻ
            foreach ($groupParties as $index => $party) {
                $key        = strtolower(str_replace(' ', '_', $groupName)) . '_' . ($index + 1);
                $data[$key] = $this->formatPartyInfo($party);
            }
        }

        // Thêm thông tin tài sản
        $data['assets_list'] = $this->formatAssetsList($assets);
        foreach ($assets as $index => $asset) {
            $data["asset_" . ($index + 1)] = $this->formatAssetInfo($asset);
        }

        return $data;
    }

    /**
     * Định dạng thông tin nhóm đương sự
     */
    private function formatPartiesGroup($parties): string
    {
        return $parties->map(function ($party, $index) {
            $order = $index > 0 ? " (người thứ " . ($index + 1) . ")" : "";
            return $this->formatPartyInfo($party) . $order;
        })->implode('; ');
    }

/**
 * Định dạng thông tin đương sự
 */
    private function formatPartyInfo($party): string
    {
        $litigant = $party->litigant;

        // Lấy thông tin chi tiết tùy theo loại đương sự
        switch ($litigant->type) {
            case 'individual':
                // SỬA: Đổi từ $litigant->individual thành $litigant->individualLitigant
                $individual = $litigant->individualLitigant;
                if ($individual) {
                    // Lấy thông tin giấy tờ tùy thân đầu tiên (nếu có)
                    $identityInfo = $litigant->identity_info ?: 'Chưa có CCCD/CMND';

                    // Lấy địa chỉ thường trú (nếu có)
                    $address     = $litigant->addresses->where('address_type', 'permanent')->first();
                    $addressText = $address ? $address->full_address : 'Chưa có địa chỉ';

                    return "{$litigant->full_name}, {$identityInfo}, ĐC: {$addressText}";
                }
                return $litigant->full_name;

            case 'organization':
                $organization = $litigant->organization;
                if ($organization) {
                    $address     = $litigant->addresses->first();
                    $addressText = $address ? $address->full_address : 'Chưa có địa chỉ';
                    return "{$litigant->full_name}, MST: {$organization->tax_code}, ĐC: {$addressText}";
                }
                return $litigant->full_name;

            case 'credit_institution':
                $creditInstitution = $litigant->creditInstitution;
                if ($creditInstitution) {
                    $address     = $litigant->addresses->first();
                    $addressText = $address ? $address->full_address : 'Chưa có địa chỉ';
                    return "{$litigant->full_name}, loại hình: {$creditInstitution->organization_type}, ĐC: {$addressText}";
                }
                return $litigant->full_name;

            default:
                return $litigant->full_name;
        }
    }

    /**
     * Định dạng danh sách tài sản
     */
    private function formatAssetsList($assets): string
    {
        return $assets->map(function ($asset, $index) {
            return ($index + 1) . ". " . $this->formatAssetInfo($asset);
        })->implode("\n");
    }

    /**
     * Định dạng thông tin tài sản
     */
    private function formatAssetInfo($asset): string
    {
        $info = ["Loại: " . $asset->asset_type];

        // Thêm thông tin chi tiết tùy theo loại tài sản
        switch ($asset->asset_type) {
            case 'real_estate_house':
            case 'real_estate_apartment':
            case 'real_estate_land_only':
                $realEstate = $asset->realEstate;
                if ($realEstate) {
                    $info[] = "Địa chỉ: {$realEstate->address}";
                    $info[] = "Diện tích: {$realEstate->area} m²";
                    if ($realEstate->certificate_number) {
                        $info[] = "Số GCN: {$realEstate->certificate_number}";
                    }
                }
                break;

            case 'movable_property_car':
            case 'movable_property_motorcycle':
                $movableProperty = $asset->movableProperty;
                if ($movableProperty) {
                    $info[] = "Biển số: {$movableProperty->license_plate}";
                    $info[] = "Hiệu: {$movableProperty->brand} {$movableProperty->model}";
                    $info[] = "Năm SX: {$movableProperty->manufacture_year}";
                }
                break;
        }

        if ($asset->notes) {
            $info[] = "Ghi chú: {$asset->notes}";
        }

        return implode(", ", $info);
    }

    /**
     * Xử lý template với dữ liệu thay thế
     */
    private function processTemplate(string $template, array $data): string
    {
        $content = $template;

        // Thay thế các placeholder {{key}}
        foreach ($data as $key => $value) {
            $placeholder = '{{' . $key . '}}';
            $content     = str_replace($placeholder, $value, $content);
        }

        // Xử lý các điều kiện IF
        $content = $this->processConditionals($content, $data);

        // Xử lý các vòng lặp
        $content = $this->processLoops($content, $data);

        return $content;
    }

    /**
     * Xử lý điều kiện trong template
     */
    private function processConditionals(string $content, array $data): string
    {
        // Pattern: {{#if key}}content{{/if}}
        $pattern = '/\{\{#if\s+(\w+)\}\}(.*?)\{\{\/if\}\}/s';

        return preg_replace_callback($pattern, function ($matches) use ($data) {
            $key                = $matches[1];
            $conditionalContent = $matches[2];

            // Kiểm tra điều kiện
            if (isset($data[$key]) && ! empty($data[$key])) {
                return $conditionalContent;
            }

            return '';
        }, $content);
    }

    /**
     * Xử lý vòng lặp trong template
     */
    private function processLoops(string $content, array $data): string
    {
        // Pattern: {{#each key}}content{{/each}}
        $pattern = '/\{\{#each\s+(\w+)\}\}(.*?)\{\{\/each\}\}/s';

        return preg_replace_callback($pattern, function ($matches) use ($data) {
            $key         = $matches[1];
            $loopContent = $matches[2];

            if (! isset($data[$key]) || ! is_array($data[$key])) {
                return '';
            }

            $result = '';
            foreach ($data[$key] as $index => $item) {
                $itemContent = $loopContent;

                // Thay thế {{@index}} và {{@item}}
                $itemContent = str_replace('{{@index}}', $index, $itemContent);
                $itemContent = str_replace('{{@item}}', $item, $itemContent);

                $result .= $itemContent;
            }

            return $result;
        }, $content);
    }

    /**
     * Xuất hợp đồng ra PDF
     */
    public function exportToPdf(Contract $contract): string
    {
        // Đảm bảo nội dung đã được sinh
        if (empty($contract->content)) {
            $this->generateContractContent($contract);
            $contract->refresh();
        }

        // Chuẩn bị HTML để chuyển đổi
        $html = $this->prepareHtmlForPdf($contract->content);

        // Tạo PDF
        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');

        // Tạo tên file
        $filename = $this->generateFileName($contract, 'pdf');

        // Lưu file
        $path = "exports/contracts/{$filename}";
        Storage::put($path, $pdf->output());

        return $path;
    }

    /**
     * Xuất hợp đồng ra Word
     */
    public function exportToWord(Contract $contract): string
    {
        // Đảm bảo nội dung đã được sinh
        if (empty($contract->content)) {
            $this->generateContractContent($contract);
            $contract->refresh();
        }

        // Tạo document Word
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        // Chuyển đổi HTML sang Word
        Html::addHtml($section, $contract->content);

        // Tạo tên file
        $filename = $this->generateFileName($contract, 'docx');

        // Lưu file
        $path   = "exports/contracts/{$filename}";
        $writer = IOFactory::createWriter($phpWord, 'Word2007');

        $tempPath = storage_path("app/{$path}");
        if (! file_exists(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }

        $writer->save($tempPath);

        return $path;
    }

    /**
     * Chuẩn bị HTML cho PDF
     */
    private function prepareHtmlForPdf(string $html): string
    {
        // Thêm CSS cho PDF
        $css = '
        <style>
        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 12px;
            line-height: 1.6;
        }
        .header { text-align: center; margin-bottom: 20px; }
        .content { margin: 20px 0; }
        .footer { margin-top: 20px; }
        .signature { margin-top: 40px; }
        .party-info { margin: 10px 0; }
        .asset-info { margin: 10px 0; }
        </style>';

        return $css . $html;
    }

    /**
     * Tạo tên file để xuất
     */
    private function generateFileName(Contract $contract, string $extension): string
    {
        $contractNumber = $contract->contract_number ?: 'HD_' . $contract->id;
        $contractNumber = Str::slug($contractNumber);
        $timestamp      = now()->format('YmdHis');

        return "{$contractNumber}_{$timestamp}.{$extension}";
    }

    /**
     * Lấy preview nội dung hợp đồng
     */
    public function getContractPreview(Contract $contract): array
    {
        if (empty($contract->content)) {
            $this->generateContractContent($contract);
            $contract->refresh();
        }

        return [
            'content'       => $contract->content,
            'parties_count' => $contract->parties()->count(),
            'assets_count'  => $contract->assets()->count(),
            'status'        => $contract->status,
            'can_export'    => $contract->status === Contract::STATUS_COMPLETED,
        ];
    }
}

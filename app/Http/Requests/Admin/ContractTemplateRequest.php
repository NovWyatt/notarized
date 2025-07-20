<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContractTemplateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $templateId = $this->route('contractTemplate')?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('contract_templates', 'name')->where(function ($query) {
                    return $query->where('contract_type_id', $this->input('contract_type_id'));
                })->ignore($templateId)
            ],
            'contract_type_id' => [
                'required',
                'exists:contract_types,id'
            ],
            'content' => [
                'required',
                'string',
                'min:10'
            ],
            'template_settings' => [
                'nullable',
                'array'
            ],
            'template_settings.show_parties' => [
                'boolean'
            ],
            'template_settings.show_assets' => [
                'boolean'
            ],
            'template_settings.show_clauses' => [
                'boolean'
            ],
            'template_settings.show_testimonial' => [
                'boolean'
            ],
            'template_settings.show_transaction_value' => [
                'boolean'
            ],
            'template_settings.show_signatures' => [
                'boolean'
            ],
            'template_settings.show_notary_info' => [
                'boolean'
            ],
            'template_settings.required_parties_min' => [
                'integer',
                'min:1',
                'max:10'
            ],
            'template_settings.required_assets_min' => [
                'integer',
                'min:0',
                'max:10'
            ],
            'template_info' => [
                'nullable',
                'array'
            ],
            'template_info.office_name' => [
                'nullable',
                'string',
                'max:255'
            ],
            'template_info.office_address' => [
                'nullable',
                'string',
                'max:500'
            ],
            'template_info.province' => [
                'nullable',
                'string',
                'max:100'
            ],
            'template_info.current_user' => [
                'nullable',
                'string',
                'max:255'
            ],
            'default_clauses' => [
                'nullable',
                'array'
            ],
            'default_clauses.*' => [
                'array'
            ],
            'default_clauses.*.title' => [
                'required_with:default_clauses.*',
                'string',
                'max:255'
            ],
            'default_clauses.*.content' => [
                'required_with:default_clauses.*',
                'string'
            ],
            'default_clauses.*.order' => [
                'integer',
                'min:1'
            ],
            'default_clauses.*.is_required' => [
                'boolean'
            ],
            'is_active' => [
                'boolean'
            ],
            'sort_order' => [
                'integer',
                'min:0',
                'max:9999'
            ]
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'tên template',
            'contract_type_id' => 'loại hợp đồng',
            'content' => 'nội dung template',
            'template_settings' => 'cài đặt template',
            'template_settings.show_parties' => 'hiển thị các bên',
            'template_settings.show_assets' => 'hiển thị tài sản',
            'template_settings.show_clauses' => 'hiển thị điều khoản',
            'template_settings.show_testimonial' => 'hiển thị lời chứng',
            'template_settings.show_transaction_value' => 'hiển thị giá trị giao dịch',
            'template_settings.show_signatures' => 'hiển thị chữ ký',
            'template_settings.show_notary_info' => 'hiển thị thông tin công chứng',
            'template_settings.required_parties_min' => 'số bên tối thiểu',
            'template_settings.required_assets_min' => 'số tài sản tối thiểu',
            'template_info' => 'thông tin template',
            'template_info.office_name' => 'tên văn phòng',
            'template_info.office_address' => 'địa chỉ văn phòng',
            'template_info.province' => 'tỉnh/thành phố',
            'template_info.current_user' => 'người tạo',
            'default_clauses' => 'điều khoản mặc định',
            'default_clauses.*.title' => 'tiêu đề điều khoản',
            'default_clauses.*.content' => 'nội dung điều khoản',
            'default_clauses.*.order' => 'thứ tự điều khoản',
            'default_clauses.*.is_required' => 'điều khoản bắt buộc',
            'is_active' => 'trạng thái hoạt động',
            'sort_order' => 'thứ tự sắp xếp'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên template.',
            'name.unique' => 'Tên template đã tồn tại trong loại hợp đồng này.',
            'name.max' => 'Tên template không được vượt quá 255 ký tự.',
            'contract_type_id.required' => 'Vui lòng chọn loại hợp đồng.',
            'contract_type_id.exists' => 'Loại hợp đồng được chọn không hợp lệ.',
            'content.required' => 'Vui lòng nhập nội dung template.',
            'content.min' => 'Nội dung template phải có ít nhất 10 ký tự.',
            'template_settings.required_parties_min.min' => 'Số bên tối thiểu phải lớn hơn 0.',
            'template_settings.required_parties_min.max' => 'Số bên tối thiểu không được vượt quá 10.',
            'template_settings.required_assets_min.max' => 'Số tài sản tối thiểu không được vượt quá 10.',
            'template_info.office_name.max' => 'Tên văn phòng không được vượt quá 255 ký tự.',
            'template_info.office_address.max' => 'Địa chỉ văn phòng không được vượt quá 500 ký tự.',
            'template_info.province.max' => 'Tên tỉnh/thành phố không được vượt quá 100 ký tự.',
            'default_clauses.*.title.required_with' => 'Vui lòng nhập tiêu đề điều khoản.',
            'default_clauses.*.content.required_with' => 'Vui lòng nhập nội dung điều khoản.',
            'default_clauses.*.title.max' => 'Tiêu đề điều khoản không được vượt quá 255 ký tự.',
            'default_clauses.*.order.min' => 'Thứ tự điều khoản phải lớn hơn 0.',
            'sort_order.min' => 'Thứ tự sắp xếp phải lớn hơn hoặc bằng 0.',
            'sort_order.max' => 'Thứ tự sắp xếp không được vượt quá 9999.'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert checkboxes to boolean
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);

        // Ensure template_settings has boolean values
        if ($this->has('template_settings')) {
            $settings = $this->input('template_settings', []);

            $booleanFields = [
                'show_parties',
                'show_assets',
                'show_clauses',
                'show_testimonial',
                'show_transaction_value',
                'show_signatures',
                'show_notary_info'
            ];

            foreach ($booleanFields as $field) {
                if (isset($settings[$field])) {
                    $settings[$field] = filter_var($settings[$field], FILTER_VALIDATE_BOOLEAN);
                }
            }

            $this->merge(['template_settings' => $settings]);
        }

        // Process default clauses
        if ($this->has('default_clauses')) {
            $clauses = $this->input('default_clauses', []);

            foreach ($clauses as $index => $clause) {
                if (isset($clause['is_required'])) {
                    $clauses[$index]['is_required'] = filter_var($clause['is_required'], FILTER_VALIDATE_BOOLEAN);
                }
                if (isset($clause['order'])) {
                    $clauses[$index]['order'] = (int) $clause['order'];
                }
            }

            $this->merge(['default_clauses' => $clauses]);
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validate template content contains required placeholders
            $content = $this->input('content');

            if ($content) {
                $requiredPlaceholders = ['{{current_date}}'];
                $missingPlaceholders = [];

                foreach ($requiredPlaceholders as $placeholder) {
                    if (strpos($content, $placeholder) === false) {
                        $missingPlaceholders[] = $placeholder;
                    }
                }

                if (!empty($missingPlaceholders)) {
                    $validator->errors()->add(
                        'content',
                        'Nội dung template phải chứa các placeholder bắt buộc: ' . implode(', ', $missingPlaceholders)
                    );
                }
            }

            // Validate default clauses order uniqueness
            $clauses = $this->input('default_clauses', []);
            if (!empty($clauses)) {
                $orders = array_filter(array_column($clauses, 'order'));
                $duplicateOrders = array_diff_assoc($orders, array_unique($orders));

                if (!empty($duplicateOrders)) {
                    $validator->errors()->add(
                        'default_clauses',
                        'Thứ tự các điều khoản không được trùng lặp.'
                    );
                }
            }

            // Validate contract type is active
            $contractTypeId = $this->input('contract_type_id');
            if ($contractTypeId) {
                $contractType = \App\Models\ContractType::find($contractTypeId);
                if ($contractType && !$contractType->is_active) {
                    $validator->errors()->add(
                        'contract_type_id',
                        'Không thể tạo template cho loại hợp đồng đã bị vô hiệu hóa.'
                    );
                }
            }
        });
    }
}

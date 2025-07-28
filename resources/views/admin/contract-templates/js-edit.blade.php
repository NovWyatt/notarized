<script>
    $(document).ready(function() {
        // SỬA: Blade syntax với dấu ngoặc nhọn
        let clauseIndex = {{ count($contractTemplate->default_clauses) }};

        // Name validation (excluding current template)
        $('#name, #contract_type_id').on('change blur', function() {
            const name = $('#name').val();
            const contractTypeId = $('#contract_type_id').val();

            if (name && contractTypeId) {
                $.post('/admin/contract-templates/ajax/validate-name', {
                        _token: $('meta[name="csrf-token"]').attr('content')
                        , name: name
                        , contract_type_id: contractTypeId,
                        // SỬA: Blade syntax với dấu mũi tên
                        template_id: {{ $contractTemplate->id }}
                    })
                    .done(function(response) {
                        if (response.available) {
                            $('#name').removeClass('is-invalid').addClass('is-valid');
                            $('.name-validation-feedback').html(
                                '<small class="text-success">✓ Tên có thể sử dụng</small>');
                        } else {
                            $('#name').removeClass('is-valid').addClass('is-invalid');
                            $('.name-validation-feedback').html('<small class="text-danger">✗ ' +
                                response.message + '</small>');
                        }
                    });
            }
        });

        // Generate settings based on contract type
        $('#generateSettings').click(function() {
            const contractTypeId = $('#contract_type_id').val();

            if (!contractTypeId) {
                toastr.warning('Vui lòng chọn loại hợp đồng trước');
                return;
            }

            Swal.fire({
                title: 'Tự động tạo cài đặt'
                , text: 'Điều này sẽ ghi đè các cài đặt hiện tại. Bạn có chắc chắn?'
                , icon: 'warning'
                , showCancelButton: true
                , confirmButtonText: 'Tiếp tục'
                , cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('/admin/contract-templates/ajax/generate-settings', {
                            _token: $('meta[name="csrf-token"]').attr('content')
                            , contract_type_id: contractTypeId
                        })
                        .done(function(response) {
                            // Update template settings
                            Object.entries(response.template_settings).forEach(([key, value]) => {
                                $(`input[name="template_settings[${key}]"]`).prop('checked', value);
                            });

                            // Update template info
                            Object.entries(response.template_info).forEach(([key, value]) => {
                                $(`input[name="template_info[${key}]"], textarea[name="template_info[${key}]"]`)
                                    .val(value);
                            });

                            toastr.success('Đã cập nhật cài đặt tự động');
                        })
                        .fail(function() {
                            toastr.error('Có lỗi xảy ra khi tạo cài đặt');
                        });
                }
            });
        });

        // Add clause functionality
        $('#addClause').click(function() {
            addClause();
        });

        function addClause(clauseData = null) {
            clauseIndex++;
            const clause = clauseData || {
                title: ''
                , content: ''
                , order: clauseIndex
                , is_required: false
            };

            const clauseHtml = `
            <div class="clause-item border rounded p-3 mb-3" data-index="${clauseIndex}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">Điều khoản ${clauseIndex}</h6>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-clause">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Tiêu đề điều khoản</label>
                            <input type="text" class="form-control"
                                   name="default_clauses[${clauseIndex}][title]"
                                   value="${clause.title}" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Thứ tự</label>
                            <input type="number" class="form-control"
                                   name="default_clauses[${clauseIndex}][order]"
                                   value="${clause.order}" min="1">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input"
                                       id="required${clauseIndex}"
                                       name="default_clauses[${clauseIndex}][is_required]"
                                       value="1" ${clause.is_required ? 'checked' : ''}>
                                <label class="custom-control-label" for="required${clauseIndex}">
                                    Bắt buộc
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Nội dung điều khoản</label>
                    <textarea class="form-control"
                              name="default_clauses[${clauseIndex}][content]"
                              rows="3" required>${clause.content}</textarea>
                </div>
            </div>
        `;

            $('#clausesContainer').append(clauseHtml);
            $('#noClausesMessage').hide();
        }

        // Remove clause
        $(document).on('click', '.remove-clause', function() {
            $(this).closest('.clause-item').remove();
            if ($('.clause-item').length === 0) {
                $('#noClausesMessage').show();
            }
        });

        // Variable insertion
        $('#insertVariable').click(function() {
            $('#variableModal').modal('show');
        });

        $('.variable-item').click(function() {
            const variable = $(this).data('variable');
            insertTextAtCursor('content', variable);
        });

        $('#insertVariableBtn').click(function() {
            const variable = $('#variableSelect').val();
            insertTextAtCursor('content', variable);
            $('#variableModal').modal('hide');
        });

        function insertTextAtCursor(textareaId, text) {
            const textarea = document.getElementById(textareaId);
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const value = textarea.value;

            textarea.value = value.substring(0, start) + text + value.substring(end);
            textarea.selectionStart = textarea.selectionEnd = start + text.length;
            textarea.focus();
        }

        // Preview functionality
        $('#previewBtn').click(function() {
            const content = $('#content').val();
            if (!content) {
                toastr.warning('Vui lòng nhập nội dung template trước');
                return;
            }

            let previewContent = content;

            const sampleData = {
                '@{{current_date}}': new Date().toLocaleDateString('vi-VN')
                , '@{{contract_number}}': 'HĐ001/2025'
                , '@{{transaction_value}}': '1,000,000,000 VNĐ'
                , '@{{notary_fee}}': '500,000 VNĐ'
                , '@{{notary_number}}': 'CC001/2025'
                , '@{{book_number}}': 'Sổ 01'
                , '@{{office_name}}': $('#office_name').val() || 'Văn phòng công chứng'
                , '@{{office_address}}': $('#office_address').val() || 'Địa chỉ văn phòng'
                , '@{{province}}': $('#province').val() || 'Tỉnh/Thành phố'
            };

            Object.entries(sampleData).forEach(([variable, value]) => {
                previewContent = previewContent.replace(
                    new RegExp(variable.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'g')
                    , value
                );
            });

            $('#previewContent').html(previewContent);
            $('#previewModal').modal('show');
        });

        // Form validation
        $('#templateForm').on('submit', function(e) {
            let isValid = true;

            // Check required fields
            const requiredFields = ['contract_type_id', 'name', 'content'];
            requiredFields.forEach(field => {
                const value = $(`#${field}`).val();
                if (!value || value.trim() === '') {
                    $(`#${field}`).addClass('is-invalid');
                    isValid = false;
                } else {
                    $(`#${field}`).removeClass('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
                toastr.error('Vui lòng điền đầy đủ thông tin bắt buộc');
            }
        });

        // Warn about changes if template is used
        @if(($contractTemplate -> contracts_count) > 0)
        const originalFormData = $('#templateForm').serialize();
        $(window).on('beforeunload', function() {
            if ($('#templateForm').serialize() !== originalFormData) {
                return 'Bạn có thay đổi chưa được lưu. Bạn có chắc chắn muốn rời khỏi trang?';
            }
        });

        $('#templateForm').on('submit', function() {
            $(window).off('beforeunload');
        });
        @endif
    });

</script>

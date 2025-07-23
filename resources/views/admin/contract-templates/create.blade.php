@extends('layouts.app')

@section('content')
<div class="container-fluid p-3">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Tạo template hợp đồng</h1>
                    <p class="text-muted">Tạo template mới cho loại hợp đồng</p>
                </div>
                <div>
                    <a href="{{ route('admin.contract-templates.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>Quay lại
                    </a>
                </div>
            </div>

            <form action="{{ route('admin.contract-templates.store') }}" method="POST" id="templateForm">
                @csrf
                <div class="row">
                    <div class="col-lg-8">
                        <!-- Basic Information -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Thông tin cơ bản</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="sort_order" class="form-label">Thứ tự</label>
                                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                                            @error('sort_order')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Trạng thái</label>
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="is_active">
                                                    Kích hoạt template
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Template Settings -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">Cài đặt template</h6>
                                <button type="button" class="btn btn-sm btn-outline-info" id="generateSettings">
                                    <i class="fas fa-magic mr-1"></i>Tự động tạo
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="font-weight-bold mb-3">Hiển thị các thành phần:</h6>

                                        <div class="form-check mb-2">
                                            <input type="checkbox" class="form-check-input" id="show_parties" name="template_settings[show_parties]" value="1" {{ old('template_settings.show_parties', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="show_parties">
                                                Hiển thị các bên tham gia
                                            </label>
                                        </div>

                                        <div class="form-check mb-2">
                                            <input type="checkbox" class="form-check-input" id="show_assets" name="template_settings[show_assets]" value="1" {{ old('template_settings.show_assets', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="show_assets">
                                                Hiển thị tài sản
                                            </label>
                                        </div>

                                        <div class="form-check mb-2">
                                            <input type="checkbox" class="form-check-input" id="show_transaction_value" name="template_settings[show_transaction_value]" value="1" {{ old('template_settings.show_transaction_value', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="show_transaction_value">
                                                Hiển thị giá trị giao dịch
                                            </label>
                                        </div>

                                        <div class="form-check mb-2">
                                            <input type="checkbox" class="form-check-input" id="show_clauses" name="template_settings[show_clauses]" value="1" {{ old('template_settings.show_clauses', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="show_clauses">
                                                Hiển thị điều khoản
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="font-weight-bold mb-3">Cài đặt khác:</h6>

                                        <div class="form-check mb-2">
                                            <input type="checkbox" class="form-check-input" id="show_testimonial" name="template_settings[show_testimonial]" value="1" {{ old('template_settings.show_testimonial', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="show_testimonial">
                                                Hiển thị lời chứng
                                            </label>
                                        </div>

                                        <div class="form-check mb-2">
                                            <input type="checkbox" class="form-check-input" id="show_signatures" name="template_settings[show_signatures]" value="1" {{ old('template_settings.show_signatures', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="show_signatures">
                                                Hiển thị chữ ký
                                            </label>
                                        </div>

                                        <div class="form-check mb-2">
                                            <input type="checkbox" class="form-check-input" id="show_notary_info" name="template_settings[show_notary_info]" value="1" {{ old('template_settings.show_notary_info', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="show_notary_info">
                                                Hiển thị thông tin công chứng
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-3">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="required_parties_min">Số bên tối thiểu</label>
                                            <input type="number" class="form-control" id="required_parties_min" name="template_settings[required_parties_min]" value="{{ old('template_settings.required_parties_min', 2) }}" min="1" max="10">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="required_assets_min">Số tài sản tối thiểu</label>
                                            <input type="number" class="form-control" id="required_assets_min" name="template_settings[required_assets_min]" value="{{ old('template_settings.required_assets_min', 1) }}" min="0" max="10">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Template Content -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">Nội dung template</h6>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="insertVariable">
                                        <i class="fas fa-plus mr-1"></i>Chèn biến
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-info" id="previewBtn">
                                        <i class="fas fa-eye mr-1"></i>Xem trước
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="15" placeholder="Nhập nội dung template HTML..." required>{{ old('content') }}</textarea>
                                    @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Sử dụng các biến như: &#123;&#123;current_date&#125;&#125;,
                                        &#123;&#123;contract_number&#125;&#125;,
                                        &#123;&#123;transaction_value&#125;&#125;, etc.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Default Clauses -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">Điều khoản mặc định</h6>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="addClause">
                                    <i class="fas fa-plus mr-1"></i>Thêm điều khoản
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="clausesContainer">
                                    <div class="text-muted text-center py-3" id="noClausesMessage">
                                        <i class="fas fa-list-alt fa-2x mb-2"></i>
                                        <p>Chưa có điều khoản nào. Nhấn "Thêm điều khoản" để bắt đầu.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <!-- Office Information -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-info">Thông tin văn phòng</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="office_name">Tên văn phòng</label>
                                    <input type="text" class="form-control" id="office_name" name="template_info[office_name]" value="{{ old('template_info.office_name', 'Văn phòng công chứng Nguyễn Thị Như Trang') }}">
                                </div>

                                <div class="form-group">
                                    <label for="office_address">Địa chỉ văn phòng</label>
                                    <textarea class="form-control" id="office_address" name="template_info[office_address]" rows="3">{{ old('template_info.office_address', '320, đường ĐT743A, khu phố Trung Thắng, phường Bình Thắng, thành phố Dĩ An, tỉnh Bình Dương') }}</textarea>
                                </div>

                                <div class="form-group">
                                    <label for="province">Tỉnh/Thành phố</label>
                                    <input type="text" class="form-control" id="province" name="template_info[province]" value="{{ old('template_info.province', 'tỉnh Bình Dương') }}">
                                </div>

                                <div class="form-group">
                                    <label for="current_user">Người tạo</label>
                                    <input type="text" class="form-control" id="current_user" name="template_info[current_user]" value="{{ old('template_info.current_user', auth()->user()->name ?? 'Công chứng viên') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Available Variables -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-warning">
                                    <i class="fas fa-code mr-2"></i>Biến có sẵn
                                </h6>
                            </div>
                            <div class="card-body">
                                <small class="text-muted">Nhấp để chèn vào nội dung:</small>
                                <div class="mt-2">
                                    <span class="badge badge-secondary variable-item mb-1" data-variable="@{{current_date}}">
                                        @{{current_date}}
                                    </span>
                                    <span class="badge badge-secondary variable-item mb-1" data-variable="@{{contract_number}}">
                                        @{{contract_number}}
                                    </span>
                                    <span class="badge badge-secondary variable-item mb-1" data-variable="@{{transaction_value}}">
                                        @{{transaction_value}}
                                    </span>
                                    <span class="badge badge-secondary variable-item mb-1" data-variable="@{{notary_fee}}">
                                        @{{notary_fee}}
                                    </span>
                                    <span class="badge badge-secondary variable-item mb-1" data-variable="@{{notary_number}}">
                                        @{{notary_number}}
                                    </span>
                                    <span class="badge badge-secondary variable-item mb-1" data-variable="@{{book_number}}">
                                        @{{book_number}}
                                    </span>
                                    <span class="badge badge-secondary variable-item mb-1" data-variable="@{{office_name}}">
                                        @{{office_name}}
                                    </span>
                                    <span class="badge badge-secondary variable-item mb-1" data-variable="@{{office_address}}">
                                        @{{office_address}}
                                    </span>
                                    <span class="badge badge-secondary variable-item mb-1" data-variable="@{{province}}">
                                        @{{province}}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="card shadow">
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-save mr-2"></i>Lưu template
                                    </button>
                                    <a href="{{ route('admin.contract-templates.index') }}" class="btn btn-secondary btn-block">
                                        <i class="fas fa-times mr-2"></i>Hủy
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Variable Insert Modal -->
<div class="modal fade" id="variableModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chèn biến</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Chọn biến để chèn:</label>
                    <select class="form-control" id="variableSelect">
                        <option value="@{{current_date}}">Ngày hiện tại</option>
                        <option value="@{{contract_number}}">Số hợp đồng</option>
                        <option value="@{{transaction_value}}">Giá trị giao dịch</option>
                        <option value="@{{notary_fee}}">Phí công chứng</option>
                        <option value="@{{notary_number}}">Số công chứng</option>
                        <option value="@{{book_number}}">Số sổ</option>
                        <option value="@{{office_name}}">Tên văn phòng</option>
                        <option value="@{{office_address}}">Địa chỉ văn phòng</option>
                        <option value="@{{province}}">Tỉnh/Thành phố</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="insertVariableBtn">Chèn</button>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xem trước template</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="previewContent" style="max-height: 500px; overflow-y: auto;">
                    <!-- Preview content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    if (typeof $ !== 'undefined') {
        $(document).ready(function() {
            let clauseIndex = 0;

            const urlParams = new URLSearchParams(window.location.search);
            const contractTypeIdFromUrl = urlParams.get('contract_type_id');

            if (contractTypeIdFromUrl) {
                $('#contract_type_id').val(contractTypeIdFromUrl).trigger('change');
                console.log('Auto selected contract type:', contractTypeIdFromUrl);
            }

            // Name validation
            $('#name, #contract_type_id').on('change blur', function() {
                const name = $('#name').val();
                const contractTypeId = $('#contract_type_id').val();

                if (name && contractTypeId) {
                    $.post('/admin/contract-templates/ajax/validate-name', {
                            _token: $('meta[name="csrf-token"]').attr('content')
                            , name: name
                            , contract_type_id: contractTypeId
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
                // Lấy giá trị từ select element
                const contractTypeId = $('#contract_type_id').val();

                // Nếu không có trong select, thử lấy từ URL
                const finalContractTypeId = contractTypeId || contractTypeIdFromUrl;

                if (!finalContractTypeId) {
                    toastr.warning('Vui lòng chọn loại hợp đồng trước');
                    return;
                }

                console.log('Using contract type ID:', finalContractTypeId);

                $.post('/admin/contract-templates/ajax/generate-settings', {
                        _token: $('meta[name="csrf-token"]').attr('content')
                        , contract_type_id: finalContractTypeId
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

                        // Clear and add default clauses
                        $('#clausesContainer').empty();
                        clauseIndex = 0;
                        response.default_clauses.forEach(clause => {
                            addClause(clause);
                        });

                        toastr.success('Đã tạo cài đặt tự động');
                    })
                    .fail(function(xhr, status, error) {
                        console.error('Error:', xhr.responseText);
                        toastr.error('Có lỗi xảy ra khi tạo cài đặt');
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

                // Replace variables with sample data for preview
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
                    previewContent = previewContent.replace(new RegExp(variable.replace(
                        /[.*+?^${}()|[\]\\]/g, '\\$&'), 'g'), value);
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
        });
    } else {
        console.error('jQuery is not loaded');
    }

</script>
@endsection

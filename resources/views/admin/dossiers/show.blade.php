@extends('layouts.app')

@section('title', 'Hồ sơ: ' . $dossier->name)

@section('content')
<div class="container-fluid p-3">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.dossiers.index') }}">Hồ sơ</a></li>
            <li class="breadcrumb-item active">{{ $dossier->name }}</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h3 mb-2">{{ $dossier->name }}</h1>
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-{{ $dossier->status_color }} fs-6">{{ $dossier->status_label }}</span>
                <small class="text-muted">
                    <i class="fas fa-user me-1"></i>{{ $dossier->creator->name }}
                </small>
                <small class="text-muted">
                    <i class="fas fa-calendar me-1"></i>{{ $dossier->created_at->format('d/m/Y H:i') }}
                </small>
            </div>
            @if($dossier->description)
                <p class="text-muted mt-2 mb-0">{{ $dossier->description }}</p>
            @endif
        </div>
        <div class="d-flex gap-2">
            @if($dossier->canBeUpdated())
                <a href="{{ route('admin.dossiers.edit', $dossier) }}" class="btn btn-outline-primary">
                    <i class="fas fa-edit me-2"></i>Chỉnh sửa
                </a>
            @endif
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-cog me-2"></i>Thao tác
                </button>
                <ul class="dropdown-menu">
                    @if($dossier->status !== \App\Models\Dossier::STATUS_COMPLETED)
                        <li><a class="dropdown-item" href="#" onclick="updateStatus('completed')">
                            <i class="fas fa-check me-2"></i>Đánh dấu hoàn thành
                        </a></li>
                    @endif
                    @if($dossier->canBeCancelled())
                        <li><a class="dropdown-item text-danger" href="#" onclick="deleteDossier()">
                            <i class="fas fa-trash me-2"></i>Xóa hồ sơ
                        </a></li>
                    @endif
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Contracts List -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-contract me-2"></i>Hợp Đồng Trong Hồ Sơ
                        <span class="badge bg-primary ms-2">{{ $contractStats['total'] }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @if($dossier->contracts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Số HĐ</th>
                                        <th>Loại HĐ</th>
                                        <th>Template</th>
                                        <th>Ngày HĐ</th>
                                        <th>Giá trị</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dossier->contracts as $contract)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.dossiers.contracts.show', [$dossier, $contract]) }}"
                                                   class="text-decoration-none fw-medium">
                                                    {{ $contract->contract_number ?: 'HD_' . $contract->id }}
                                                </a>
                                            </td>
                                            <td>{{ $contract->template->contractType->name }}</td>
                                            <td>{{ $contract->template->name }}</td>
                                            <td>{{ $contract->contract_date->format('d/m/Y') }}</td>
                                            <td>{{ $contract->formatted_transaction_value }}</td>
                                            <td>
                                                <span class="badge bg-{{ $contract->status_color }}">
                                                    {{ $contract->status_label }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.dossiers.contracts.show', [$dossier, $contract]) }}"
                                                       class="btn btn-outline-primary btn-sm" title="Xem chi tiết">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.dossiers.contracts.export.pdf', [$dossier, $contract]) }}"
                                                       class="btn btn-outline-danger btn-sm" title="Xuất PDF">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
                                                    <a href="{{ route('admin.dossiers.contracts.export.word', [$dossier, $contract]) }}"
                                                       class="btn btn-outline-info btn-sm" title="Xuất Word">
                                                        <i class="fas fa-file-word"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-file-contract fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">Chưa có hợp đồng nào</h6>
                            <p class="text-muted">Chọn template bên phải để tạo hợp đồng đầu tiên</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Available Templates -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-plus-circle me-2"></i>Tạo Hợp Đồng Mới
                    </h5>
                </div>
                <div class="card-body">
                    <div class="accordion" id="contractTypesAccordion">
                        @foreach($contractTypes as $type)
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading{{ $type->id }}">
                                    <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}"
                                            type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapse{{ $type->id }}">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-folder-open me-3 text-primary"></i>
                                            <div>
                                                <div class="fw-medium">{{ $type->name }}</div>
                                                @if($type->description)
                                                    <small class="text-muted">{{ $type->description }}</small>
                                                @endif
                                            </div>
                                            <span class="badge bg-secondary ms-auto me-3">
                                                {{ $type->templates->count() }} templates
                                            </span>
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapse{{ $type->id }}"
                                     class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                     data-bs-parent="#contractTypesAccordion">
                                    <div class="accordion-body">
                                        @if($type->templates->count() > 0)
                                            <div class="row g-3">
                                                @foreach($type->templates as $template)
                                                    <div class="col-md-6">
                                                        <div class="card border-0 bg-light h-100">
                                                            <div class="card-body">
                                                                <h6 class="card-title">{{ $template->name }}</h6>
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <small class="text-muted">
                                                                        <i class="fas fa-sort me-1"></i>{{ $template->sort_order }}
                                                                    </small>
                                                                    <div class="btn-group btn-group-sm">
                                                                        <button type="button"
                                                                                class="btn btn-outline-secondary btn-sm"
                                                                                onclick="previewTemplate({{ $template->id }})"
                                                                                title="Xem trước">
                                                                            <i class="fas fa-eye"></i>
                                                                        </button>
                                                                        <button type="button"
                                                                                class="btn btn-primary btn-sm"
                                                                                onclick="selectTemplate({{ $template->id }})"
                                                                                title="Chọn template này">
                                                                            <i class="fas fa-plus"></i> Chọn
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-center py-3">
                                                <i class="fas fa-file-alt fa-2x text-muted mb-2"></i>
                                                <p class="text-muted mb-0">Chưa có template nào cho loại hợp đồng này</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Stats -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Thống Kê
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm rounded-circle bg-primary bg-opacity-10 me-3">
                                    <i class="fas fa-file-contract text-primary"></i>
                                </div>
                                <div>
                                    <div class="fw-medium">{{ $contractStats['total'] }}</div>
                                    <small class="text-muted">Tổng HĐ</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm rounded-circle bg-success bg-opacity-10 me-3">
                                    <i class="fas fa-check text-success"></i>
                                </div>
                                <div>
                                    <div class="fw-medium">{{ $contractStats['completed'] }}</div>
                                    <small class="text-muted">Hoàn thành</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm rounded-circle bg-warning bg-opacity-10 me-3">
                                    <i class="fas fa-money-bill text-warning"></i>
                                </div>
                                <div>
                                    <div class="fw-medium">{{ number_format($contractStats['total_value'], 0, ',', '.') }} VNĐ</div>
                                    <small class="text-muted">Tổng giá trị</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Info -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Thông Tin
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">Người tạo</small>
                        <div class="fw-medium">{{ $dossier->creator->name }}</div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Ngày tạo</small>
                        <div class="fw-medium">{{ $dossier->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Cập nhật cuối</small>
                        <div class="fw-medium">{{ $dossier->updated_at->format('d/m/Y H:i') }}</div>
                    </div>
                    <div class="mb-0">
                        <small class="text-muted d-block">Trạng thái</small>
                        <span class="badge bg-{{ $dossier->status_color }}">{{ $dossier->status_label }}</span>
                    </div>
                </div>
            </div>

            <!-- Available Resources -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-database me-2"></i>Tài Nguyên Có Sẵn
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Đương sự</span>
                        <span class="badge bg-secondary">{{ $litigants->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Tài sản</span>
                        <span class="badge bg-secondary">{{ $assets->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Templates</span>
                        <span class="badge bg-secondary">{{ $contractTypes->sum(fn($type) => $type->templates->count()) }}</span>
                    </div>

                    <hr>

                    <div class="d-grid gap-2">
                        <a href="{{ route('litigants.create') }}" class="btn btn-outline-primary btn-sm" target="_blank">
                            <i class="fas fa-plus me-2"></i>Thêm Đương Sự
                        </a>
                        <a href="{{ route('properties.create') }}" class="btn btn-outline-success btn-sm" target="_blank">
                            <i class="fas fa-plus me-2"></i>Thêm Tài Sản
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal tạo hợp đồng -->
<div class="modal fade" id="createContractModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tạo Hợp Đồng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createContractForm" action="{{ route('admin.dossiers.contracts.create', $dossier) }}" method="POST">
                @csrf
                <input type="hidden" name="contract_template_id" id="selectedTemplateId">

                <div class="modal-body">
                    <div class="row">
                        <!-- Template Info -->
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body" id="templateInfo">
                                    <div class="text-center text-muted py-4">
                                        <i class="fas fa-file-alt fa-3x mb-3"></i>
                                        <p>Chọn template để xem thông tin</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Data -->
                        <div class="col-md-8">
                            <div class="row">
                                <!-- Thông tin cơ bản -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Ngày Hợp Đồng <span class="text-danger">*</span></label>
                                    <input type="date" name="contract_date" class="form-control"
                                           value="{{ now()->format('Y-m-d') }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Giá Trị Giao Dịch (VNĐ)</label>
                                    <input type="number" name="transaction_value" class="form-control"
                                           min="0" step="1000">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Lệ Phí CC</label>
                                    <input type="text" name="notary_fee" class="form-control">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Số CC</label>
                                    <input type="text" name="notary_number" class="form-control">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Số Sổ</label>
                                    <input type="text" name="book_number" class="form-control">
                                </div>
                            </div>

                            <!-- Đương sự -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">Đương Sự <span class="text-danger">*</span></h6>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="addParty()">
                                        <i class="fas fa-plus me-1"></i>Thêm
                                    </button>
                                </div>
                                <div id="partiesContainer">
                                    <!-- Parties will be added here -->
                                </div>
                            </div>

                            <!-- Tài sản -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">Tài Sản</h6>
                                    <button type="button" class="btn btn-sm btn-success" onclick="addAsset()">
                                        <i class="fas fa-plus me-1"></i>Thêm
                                    </button>
                                </div>
                                <div id="assetsContainer">
                                    <!-- Assets will be added here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Tạo Hợp Đồng
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal preview template -->
<div class="modal fade" id="previewTemplateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xem Trước Template</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="templatePreviewContent" style="max-height: 500px; overflow-y: auto;">
                    <!-- Preview content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" onclick="useThisTemplate()">
                    <i class="fas fa-check me-2"></i>Sử Dụng Template Này
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 2.5rem;
    height: 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.card-hover {
    transition: transform 0.2s ease-in-out;
}

.card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.party-item, .asset-item {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 1rem;
    margin-bottom: 0.5rem;
    background-color: #f8f9fa;
}
</style>

<script>
let partiesData = @json($litigants);
let assetsData = @json($assets);
let partyIndex = 0;
let assetIndex = 0;
let selectedTemplateId = null;

// Chọn template để tạo hợp đồng
function selectTemplate(templateId) {
    selectedTemplateId = templateId;
    document.getElementById('selectedTemplateId').value = templateId;

    // Load template info
    loadTemplateInfo(templateId);

    // Reset form
    resetContractForm();

    // Show modal
    new bootstrap.Modal(document.getElementById('createContractModal')).show();
}

// Load thông tin template
function loadTemplateInfo(templateId) {
    fetch(`{{ route('admin.dossiers.ajax.template-info', $dossier->id) }}?template_id=${templateId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error('Error loading template info:', data.error);
                return;
            }

            document.getElementById('templateInfo').innerHTML = `
                <h6 class="text-primary">${data.name}</h6>
                <p class="text-muted mb-2">${data.contract_type}</p>
                <div class="small text-muted">
                    <div class="mb-1">
                        <i class="fas fa-users me-1"></i>
                        Yêu cầu tối thiểu: ${data.template_settings?.required_parties_min || 2} đương sự
                    </div>
                    <div>
                        <i class="fas fa-building me-1"></i>
                        Tài sản tối thiểu: ${data.template_settings?.required_assets_min || 0}
                    </div>
                </div>
            `;
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

// Preview template
function previewTemplate(templateId) {
    fetch(`{{ route('admin.dossiers.ajax.template-preview', $dossier->id) }}?template_id=${templateId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('templatePreviewContent').innerHTML = data.content;
                document.querySelector('#previewTemplateModal .modal-title').textContent =
                    `Xem Trước: ${data.template_name}`;

                // Store template ID for "Use This Template" button
                document.getElementById('previewTemplateModal').setAttribute('data-template-id', templateId);

                new bootstrap.Modal(document.getElementById('previewTemplateModal')).show();
            } else {
                alert('Không thể tải preview template');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi tải preview');
        });
}

// Sử dụng template từ modal preview
function useThisTemplate() {
    const templateId = document.getElementById('previewTemplateModal').getAttribute('data-template-id');
    if (templateId) {
        // Đóng preview modal
        bootstrap.Modal.getInstance(document.getElementById('previewTemplateModal')).hide();

        // Mở create contract modal
        selectTemplate(templateId);
    }
}

// Reset form tạo hợp đồng
function resetContractForm() {
    document.getElementById('partiesContainer').innerHTML = '';
    document.getElementById('assetsContainer').innerHTML = '';
    partyIndex = 0;
    assetIndex = 0;

    // Add default party
    addParty();
}

// Thêm đương sự
function addParty() {
    const container = document.getElementById('partiesContainer');
    const partyHtml = `
        <div class="party-item" data-index="${partyIndex}">
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">Đương sự</label>
                    <select name="parties[${partyIndex}][litigant_id]" class="form-select" required>
                        <option value="">-- Chọn đương sự --</option>
                        ${partiesData.map(party => `
                            <option value="${party.id}">
                                ${party.full_name}
                                ${party.individual ? ` - ${party.individual.id_number}` : ''}
                                ${party.organization ? ` - ${party.organization.tax_code}` : ''}
                            </option>
                        `).join('')}
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Vai trò</label>
                    <select name="parties[${partyIndex}][party_type]" class="form-select" required>
                        <option value="">-- Chọn vai trò --</option>
                        <option value="buyer">Bên mua</option>
                        <option value="seller">Bên bán</option>
                        <option value="transferor">Bên chuyển giao</option>
                        <option value="transferee">Bên nhận chuyển giao</option>
                        <option value="lender">Bên cho vay</option>
                        <option value="borrower">Bên đi vay</option>
                        <option value="other">Khác</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Nhóm</label>
                    <select name="parties[${partyIndex}][group_name]" class="form-select" required>
                        <option value="Bên A">Bên A</option>
                        <option value="Bên B">Bên B</option>
                        <option value="Bên C">Bên C</option>
                        <option value="Bên thứ nhất">Bên thứ nhất</option>
                        <option value="Bên thứ hai">Bên thứ hai</option>
                        <option value="Bên thứ ba">Bên thứ ba</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-danger btn-sm w-100"
                            onclick="removeParty(${partyIndex})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <label class="form-label">Ghi chú</label>
                    <input type="text" name="parties[${partyIndex}][notes]" class="form-control"
                           placeholder="Ghi chú thêm về đương sự...">
                </div>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', partyHtml);
    partyIndex++;
}

// Xóa đương sự
function removeParty(index) {
    const partyElement = document.querySelector(`[data-index="${index}"]`);
    if (partyElement) {
        partyElement.remove();
    }

    // Ensure at least one party
    if (document.querySelectorAll('.party-item').length === 0) {
        addParty();
    }
}

// Thêm tài sản
function addAsset() {
    const container = document.getElementById('assetsContainer');
    const assetHtml = `
        <div class="asset-item" data-index="${assetIndex}">
            <div class="row">
                <div class="col-md-8">
                    <label class="form-label">Tài sản</label>
                    <select name="assets[${assetIndex}][asset_id]" class="form-select" required>
                        <option value="">-- Chọn tài sản --</option>
                        ${assetsData.map(asset => `
                            <option value="${asset.id}">
                                ${asset.asset_type}
                                ${asset.real_estate ? ` - ${asset.real_estate.address}` : ''}
                                ${asset.movable_property ? ` - ${asset.movable_property.license_plate}` : ''}
                            </option>
                        `).join('')}
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-danger btn-sm w-100"
                            onclick="removeAsset(${assetIndex})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <label class="form-label">Ghi chú</label>
                    <input type="text" name="assets[${assetIndex}][notes]" class="form-control"
                           placeholder="Ghi chú về tài sản trong hợp đồng...">
                </div>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', assetHtml);
    assetIndex++;
}

// Xóa tài sản
function removeAsset(index) {
    const assetElement = document.querySelector(`[data-index="${index}"]`);
    if (assetElement) {
        assetElement.remove();
    }
}

// Cập nhật trạng thái hồ sơ
function updateStatus(status) {
    if (!confirm('Bạn có chắc chắn muốn thay đổi trạng thái hồ sơ?')) {
        return;
    }

    fetch(`{{ route('admin.dossiers.update-status', $dossier) }}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Có lỗi xảy ra');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi cập nhật trạng thái');
    });
}

// Xóa hồ sơ
function deleteDossier() {
    if (!confirm('Bạn có chắc chắn muốn xóa hồ sơ này? Thao tác này không thể hoàn tác!')) {
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `{{ route('admin.dossiers.destroy', $dossier) }}`;

    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const methodField = document.createElement('input');
    methodField.type = 'hidden';
    methodField.name = '_method';
    methodField.value = 'DELETE';

    form.appendChild(csrfToken);
    form.appendChild(methodField);
    document.body.appendChild(form);
    form.submit();
}

// Initialize
$(document).ready(function() {
    // Add first party by default when modal opens
    $('#createContractModal').on('show.bs.modal', function() {
        if (document.querySelectorAll('.party-item').length === 0) {
            addParty();
        }
    });
});
</script>
@endsection

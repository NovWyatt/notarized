@extends('layouts.app')

@section('content')
<div class="container-fluid p-3">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.dossiers.index') }}">Hồ sơ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.dossiers.show', $dossier) }}">{{ $dossier->name }}</a></li>
            <li class="breadcrumb-item active">{{ $contract->contract_number ?: 'HD_' . $contract->id }}</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h3 mb-2">{{ $contract->contract_number ?: 'HD_' . $contract->id }}</h1>
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-{{ $contract->status_color }} fs-6">{{ $contract->status_label }}</span>
                <small class="text-muted">
                    <i class="fas fa-file-contract me-1"></i>{{ $contract->template->name }}
                </small>
                <small class="text-muted">
                    <i class="fas fa-calendar me-1"></i>{{ $contract->contract_date->format('d/m/Y') }}
                </small>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.dossiers.show', $dossier) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Về Hồ Sơ
            </a>
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-download me-2"></i>Xuất File
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.dossiers.contracts.export.pdf', [$dossier, $contract]) }}">
                            <i class="fas fa-file-pdf me-2"></i>Xuất PDF
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.dossiers.contracts.export.word', [$dossier, $contract]) }}">
                            <i class="fas fa-file-word me-2"></i>Xuất Word
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Contract Preview -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-eye me-2"></i>Nội Dung Hợp Đồng
                    </h5>
                </div>
                <div class="card-body">
                    @if(!empty($preview['content']))
                        <div id="contractContent" class="contract-preview">
                            {!! $preview['content'] !!}
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-file-alt fa-3x mb-3"></i>
                            <h6>Chưa có nội dung</h6>
                            <p>Nội dung hợp đồng đang được tạo từ template...</p>
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Nội dung được tạo từ template: <strong>{{ $contract->template->name }}</strong>
                        </small>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="printContract()">
                                <i class="fas fa-print me-1"></i>In
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="toggleFullscreen()">
                                <i class="fas fa-expand me-1"></i>Toàn màn hình
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contract Parties -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-users me-2"></i>Đương Sự
                        <span class="badge bg-primary ms-2">{{ $contract->parties->count() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @if($contract->parties->count() > 0)
                        @foreach($contract->parties->groupBy('group_name') as $groupName => $groupParties)
                            <div class="mb-3">
                                <h6 class="text-primary">{{ $groupName }}</h6>
                                <div class="row">
                                    @foreach($groupParties as $party)
                                        <div class="col-md-6 mb-2">
                                            <div class="card bg-light border-0">
                                                <div class="card-body py-2">
                                                    <div class="d-flex align-items-start">
                                                        <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center">
                                                            <i class="fas fa-user text-primary"></i>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1">{{ $party->litigant->full_name }}</h6>
                                                            <div class="small text-muted">
                                                                <div>{{ $party->party_type_label }}</div>
                                                                @if($party->litigant->type === 'individual' && $party->litigant->individual)
                                                                    <div>CCCD: {{ $party->litigant->individual->id_number }}</div>
                                                                    <div>ĐC: {{ $party->litigant->individual->address }}</div>
                                                                @elseif($party->litigant->type === 'organization' && $party->litigant->organization)
                                                                    <div>MST: {{ $party->litigant->organization->tax_code }}</div>
                                                                    <div>ĐC: {{ $party->litigant->organization->address }}</div>
                                                                @endif
                                                                @if($party->notes)
                                                                    <div class="text-info">Ghi chú: {{ $party->notes }}</div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-3 text-muted">
                            <i class="fas fa-user-slash fa-2x mb-2"></i>
                            <p>Chưa có đương sự nào</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Contract Assets -->
            @if($contract->assets->count() > 0)
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-building me-2"></i>Tài Sản
                            <span class="badge bg-secondary ms-2">{{ $contract->assets->count() }}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($contract->assets as $asset)
                                <div class="col-md-6 mb-3">
                                    <div class="card bg-light border-0">
                                        <div class="card-body py-2">
                                            <div class="d-flex align-items-start">
                                                <div class="avatar-sm bg-success bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-home text-success"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">{{ ucfirst(str_replace('_', ' ', $asset->asset_type)) }}</h6>
                                                    <div class="small text-muted">
                                                        @if($asset->asset_type === 'real_estate_house' || $asset->asset_type === 'real_estate_apartment' || $asset->asset_type === 'real_estate_land_only')
                                                            @if($asset->realEstate)
                                                                <div>ĐC: {{ $asset->realEstate->address }}</div>
                                                                <div>DT: {{ $asset->realEstate->area }} m²</div>
                                                                @if($asset->realEstate->certificate_number)
                                                                    <div>GCN: {{ $asset->realEstate->certificate_number }}</div>
                                                                @endif
                                                            @endif
                                                        @elseif($asset->asset_type === 'movable_property_car' || $asset->asset_type === 'movable_property_motorcycle')
                                                            @if($asset->movableProperty)
                                                                <div>BKS: {{ $asset->movableProperty->license_plate }}</div>
                                                                <div>Hiệu: {{ $asset->movableProperty->brand }} {{ $asset->movableProperty->model }}</div>
                                                                <div>Năm SX: {{ $asset->movableProperty->manufacture_year }}</div>
                                                            @endif
                                                        @endif
                                                        @if($asset->pivot->notes)
                                                            <div class="text-info mt-1">{{ $asset->pivot->notes }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Contract Info -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Thông Tin Hợp Đồng
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">Số hợp đồng</small>
                        <div class="fw-medium">{{ $contract->contract_number ?: 'Chưa có' }}</div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Loại hợp đồng</small>
                        <div class="fw-medium">{{ $contract->template->contractType->name }}</div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Template</small>
                        <div class="fw-medium">{{ $contract->template->name }}</div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Ngày hợp đồng</small>
                        <div class="fw-medium">{{ $contract->contract_date->format('d/m/Y') }}</div>
                    </div>

                    @if($contract->transaction_value)
                        <div class="mb-3">
                            <small class="text-muted d-block">Giá trị giao dịch</small>
                            <div class="fw-medium text-success">{{ $contract->formatted_transaction_value }}</div>
                        </div>
                    @endif

                    @if($contract->notary_fee)
                        <div class="mb-3">
                            <small class="text-muted d-block">Lệ phí công chứng</small>
                            <div class="fw-medium">{{ $contract->notary_fee }}</div>
                        </div>
                    @endif

                    @if($contract->notary_number)
                        <div class="mb-3">
                            <small class="text-muted d-block">Số công chứng</small>
                            <div class="fw-medium">{{ $contract->notary_number }}</div>
                        </div>
                    @endif

                    @if($contract->book_number)
                        <div class="mb-3">
                            <small class="text-muted d-block">Số sổ</small>
                            <div class="fw-medium">{{ $contract->book_number }}</div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <small class="text-muted d-block">Trạng thái</small>
                        <span class="badge bg-{{ $contract->status_color }}">{{ $contract->status_label }}</span>
                    </div>

                    <div class="mb-0">
                        <small class="text-muted d-block">Ngày tạo</small>
                        <div class="fw-medium">{{ $contract->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>

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
                            <div class="text-center">
                                <div class="text-primary">
                                    <i class="fas fa-users fa-2x mb-2"></i>
                                </div>
                                <h5 class="mb-1">{{ $contract->parties->count() }}</h5>
                                <small class="text-muted">Đương sự</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="text-success">
                                    <i class="fas fa-building fa-2x mb-2"></i>
                                </div>
                                <h5 class="mb-1">{{ $contract->assets->count() }}</h5>
                                <small class="text-muted">Tài sản</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-cog me-2"></i>Thao Tác
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.dossiers.contracts.export.pdf', [$dossier, $contract]) }}"
                           class="btn btn-danger">
                            <i class="fas fa-file-pdf me-2"></i>Tải PDF
                        </a>
                        <a href="{{ route('admin.dossiers.contracts.export.word', [$dossier, $contract]) }}"
                           class="btn btn-info">
                            <i class="fas fa-file-word me-2"></i>Tải Word
                        </a>
                        <button type="button" class="btn btn-outline-secondary" onclick="printContract()">
                            <i class="fas fa-print me-2"></i>In Hợp Đồng
                        </button>
                        <hr>
                        <a href="{{ route('admin.dossiers.show', $dossier) }}"
                           class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i>Về Hồ Sơ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.contract-preview {
    line-height: 1.6;
    font-family: 'Times New Roman', serif;
    background: white;
    padding: 2rem;
    border: 1px solid #ddd;
    border-radius: 0.375rem;
    min-height: 400px;
}

.contract-preview h1, .contract-preview h2, .contract-preview h3 {
    text-align: center;
    margin-bottom: 1rem;
}

.contract-preview p {
    margin-bottom: 1rem;
    text-align: justify;
}

.avatar-sm {
    width: 2.5rem;
    height: 2.5rem;
}

@media print {
    .container-fluid, .card, .btn, .breadcrumb {
        display: none !important;
    }

    .contract-preview {
        display: block !important;
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
    }
}

.fullscreen-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: white;
    z-index: 9999;
    overflow-y: auto;
    padding: 2rem;
}

.fullscreen-overlay .close-fullscreen {
    position: fixed;
    top: 1rem;
    right: 1rem;
    z-index: 10000;
}
</style>

<script>
function printContract() {
    window.print();
}

function toggleFullscreen() {
    const content = document.getElementById('contractContent');
    const isFullscreen = document.querySelector('.fullscreen-overlay');

    if (isFullscreen) {
        // Exit fullscreen
        document.body.removeChild(isFullscreen);
    } else {
        // Enter fullscreen
        const overlay = document.createElement('div');
        overlay.className = 'fullscreen-overlay';

        const closeBtn = document.createElement('button');
        closeBtn.className = 'btn btn-secondary close-fullscreen';
        closeBtn.innerHTML = '<i class="fas fa-times me-2"></i>Đóng';
        closeBtn.onclick = toggleFullscreen;

        const contentClone = content.cloneNode(true);

        overlay.appendChild(closeBtn);
        overlay.appendChild(contentClone);
        document.body.appendChild(overlay);
    }
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // ESC to exit fullscreen
    if (e.key === 'Escape' && document.querySelector('.fullscreen-overlay')) {
        toggleFullscreen();
    }

    // Ctrl+P to print
    if (e.ctrlKey && e.key === 'p') {
        e.preventDefault();
        printContract();
    }
});
</script>
@endsection

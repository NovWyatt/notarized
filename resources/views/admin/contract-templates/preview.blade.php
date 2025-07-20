@extends('layouts.app2')

@section('content')
    <div class="container-fluid p-3">
        <div class="row">
            <div class="col-12">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">Xem trước template: {{ $contractTemplate->name }}</h1>
                        <p class="text-muted">
                            <i class="fas fa-file-contract mr-1"></i>{{ $contractTemplate->contractType->name }}
                            <span class="ml-3">
                                <i class="fas fa-info-circle mr-1"></i>Dữ liệu mẫu được sử dụng để xem trước
                            </span>
                        </p>
                    </div>
                    <div>
                        <button type="button" class="btn btn-outline-secondary" id="printBtn">
                            <i class="fas fa-print mr-2"></i>In
                        </button>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-primary dropdown-toggle" data-toggle="dropdown">
                                <i class="fas fa-download mr-2"></i>Tải xuống
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item"
                                    href="{{ route('admin.contract-templates.export', $contractTemplate) }}?format=html">
                                    <i class="fas fa-code mr-2"></i>Tải HTML
                                </a>
                                <a class="dropdown-item"
                                    href="{{ route('admin.contract-templates.export', $contractTemplate) }}?format=txt">
                                    <i class="fas fa-file-text mr-2"></i>Tải Text
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="javascript:void(0)" id="exportPdf">
                                    <i class="fas fa-file-pdf mr-2"></i>Xuất PDF
                                </a>
                            </div>
                        </div>
                        <a href="{{ route('admin.contract-templates.edit', $contractTemplate) }}" class="btn btn-primary">
                            <i class="fas fa-edit mr-2"></i>Chỉnh sửa
                        </a>
                        <button type="button" class="btn btn-secondary" onclick="window.close()">
                            <i class="fas fa-times mr-2"></i>Đóng
                        </button>
                    </div>
                </div>

                <!-- Preview Controls -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body py-3">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <label class="mb-0 mr-3">Chế độ xem:</label>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <input type="radio" class="btn-check" name="viewMode" id="viewWeb"
                                            value="web" checked>
                                        <label class="btn btn-outline-primary" for="viewWeb">
                                            <i class="fas fa-desktop mr-1"></i>Web
                                        </label>

                                        <input type="radio" class="btn-check" name="viewMode" id="viewPrint"
                                            value="print">
                                        <label class="btn btn-outline-primary" for="viewPrint">
                                            <i class="fas fa-print mr-1"></i>In ấn
                                        </label>

                                        <input type="radio" class="btn-check" name="viewMode" id="viewMobile"
                                            value="mobile">
                                        <label class="btn btn-outline-primary" for="viewMobile">
                                            <i class="fas fa-mobile-alt mr-1"></i>Mobile
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center justify-content-end">
                                    <label class="mb-0 mr-3">Zoom:</label>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-outline-secondary" id="zoomOut">
                                            <i class="fas fa-search-minus"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" id="zoomReset">
                                            <span id="zoomLevel">100%</span>
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" id="zoomIn">
                                            <i class="fas fa-search-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preview Content -->
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow">
                            <div class="card-body p-0">
                                <div id="previewContainer" class="preview-web">
                                    <div id="previewContent" class="p-4">
                                        {!! $previewContent !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Template Information -->
                <div class="row mt-4">
                    <div class="col-md-8">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-info-circle mr-2"></i>Thông tin template
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-borderless table-sm">
                                            <tr>
                                                <td class="font-weight-bold">Tên template:</td>
                                                <td>{{ $contractTemplate->name }}</td>
                                            </tr>
                                            <tr>
                                                <td class="font-weight-bold">Loại hợp đồng:</td>
                                                <td>{{ $contractTemplate->contractType->name }}</td>
                                            </tr>
                                            <tr>
                                                <td class="font-weight-bold">Trạng thái:</td>
                                                <td>
                                                    <span
                                                        class="badge badge-{{ $contractTemplate->is_active ? 'success' : 'secondary' }}">
                                                        {{ $contractTemplate->is_active ? 'Hoạt động' : 'Tạm dừng' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-borderless table-sm">
                                            <tr>
                                                <td class="font-weight-bold">Tạo lúc:</td>
                                                <td>{{ $contractTemplate->created_at->format('d/m/Y H:i') }}</td>
                                            </tr>
                                            <tr>
                                                <td class="font-weight-bold">Cập nhật:</td>
                                                <td>{{ $contractTemplate->updated_at->format('d/m/Y H:i') }}</td>
                                            </tr>
                                            <tr>
                                                <td class="font-weight-bold">Đã sử dụng:</td>
                                                <td>
                                                    <span
                                                        class="badge badge-info">{{ $contractTemplate->contracts_count }}</span>
                                                    hợp đồng
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-warning">
                                    <i class="fas fa-database mr-2"></i>Dữ liệu mẫu
                                </h6>
                            </div>
                            <div class="card-body">
                                <small class="text-muted">Các biến được thay thế:</small>
                                <div class="mt-2">
                                    <div class="d-flex justify-content-between py-1">
                                        <small class="text-muted">{{ current_date }}</small>
                                        <small class="font-weight-bold">{{ now()->format('d/m/Y') }}</small>
                                    </div>
                                    <div class="d-flex justify-content-between py-1">
                                        <small class="text-muted">{{ contract_number }}</small>
                                        <small class="font-weight-bold">HĐ001/2025</small>
                                    </div>
                                    <div class="d-flex justify-content-between py-1">
                                        <small class="text-muted">{{ transaction_value }}</small>
                                        <small class="font-weight-bold">1,000,000,000 VNĐ</small>
                                    </div>
                                    <div class="d-flex justify-content-between py-1">
                                        <small class="text-muted">{{ notary_fee }}</small>
                                        <small class="font-weight-bold">500,000 VNĐ</small>
                                    </div>
                                    <div class="d-flex justify-content-between py-1">
                                        <small class="text-muted">{{ notary_number }}</small>
                                        <small class="font-weight-bold">CC001/2025</small>
                                    </div>
                                    <div class="d-flex justify-content-between py-1">
                                        <small class="text-muted">{{ book_number }}</small>
                                        <small class="font-weight-bold">Sổ 01</small>
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="text-center">
                                    <small class="text-info">
                                        <i class="fas fa-lightbulb mr-1"></i>
                                        Đây là dữ liệu mẫu để xem trước template
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            let currentZoom = 100;

            // View mode switching
            $('input[name="viewMode"]').change(function() {
                const mode = $(this).val();
                const container = $('#previewContainer');

                // Remove all preview classes
                container.removeClass('preview-web preview-print preview-mobile');

                // Add new class
                container.addClass('preview-' + mode);

                // Reset zoom
                currentZoom = 100;
                updateZoom();
            });

            // Zoom functionality
            $('#zoomIn').click(function() {
                if (currentZoom < 200) {
                    currentZoom += 25;
                    updateZoom();
                }
            });

            $('#zoomOut').click(function() {
                if (currentZoom > 50) {
                    currentZoom -= 25;
                    updateZoom();
                }
            });

            $('#zoomReset').click(function() {
                currentZoom = 100;
                updateZoom();
            });

            function updateZoom() {
                const scale = currentZoom / 100;
                $('#previewContent').css('transform', `scale(${scale})`);
                $('#zoomLevel').text(currentZoom + '%');
            }

            // Print functionality
            $('#printBtn').click(function() {
                // Hide non-printable elements
                $('.no-print').hide();

                // Switch to print view temporarily
                const originalMode = $('input[name="viewMode"]:checked').val();
                const container = $('#previewContainer');
                container.removeClass('preview-web preview-print preview-mobile');
                container.addClass('preview-print');

                // Reset zoom for printing
                const originalZoom = currentZoom;
                $('#previewContent').css('transform', 'scale(1)');

                // Print
                window.print();

                // Restore original state
                setTimeout(() => {
                    $('.no-print').show();
                    container.removeClass('preview-print');
                    container.addClass('preview-' + originalMode);
                    $('#previewContent').css('transform', `scale(${originalZoom / 100})`);
                }, 1000);
            });

            // Export to PDF
            $('#exportPdf').click(function() {
                const button = $(this);
                const originalText = button.html();

                // Show loading
                button.html('<i class="fas fa-spinner fa-spin mr-2"></i>Đang tạo PDF...');
                button.addClass('disabled');

                // Get content
                const content = document.getElementById('previewContent');

                // PDF options
                const options = {
                    margin: 1,
                    filename: `${$contractTemplate->name}_preview.pdf`,
                    image: {
                        type: 'jpeg',
                        quality: 0.98
                    },
                    html2canvas: {
                        scale: 2
                    },
                    jsPDF: {
                        unit: 'in',
                        format: 'a4',
                        orientation: 'portrait'
                    }
                };

                // Generate PDF
                html2pdf().set(options).from(content).save().then(() => {
                    // Restore button
                    button.html(originalText);
                    button.removeClass('disabled');
                    toastr.success('PDF đã được tạo thành công');
                }).catch(() => {
                    // Restore button
                    button.html(originalText);
                    button.removeClass('disabled');
                    toastr.error('Có lỗi xảy ra khi tạo PDF');
                });
            });

            // Keyboard shortcuts
            $(document).keydown(function(e) {
                // Ctrl/Cmd + P for print
                if ((e.ctrlKey || e.metaKey) && e.which === 80) {
                    e.preventDefault();
                    $('#printBtn').click();
                }

                // Ctrl/Cmd + Plus for zoom in
                if ((e.ctrlKey || e.metaKey) && (e.which === 187 || e.which === 107)) {
                    e.preventDefault();
                    $('#zoomIn').click();
                }

                // Ctrl/Cmd + Minus for zoom out
                if ((e.ctrlKey || e.metaKey) && (e.which === 189 || e.which === 109)) {
                    e.preventDefault();
                    $('#zoomOut').click();
                }

                // Ctrl/Cmd + 0 for reset zoom
                if ((e.ctrlKey || e.metaKey) && e.which === 48) {
                    e.preventDefault();
                    $('#zoomReset').click();
                }
            });

            // Responsive handling
            $(window).resize(function() {
                const viewMode = $('input[name="viewMode"]:checked').val();
                if (viewMode === 'mobile' && $(window).width() > 768) {
                    // Switch to web view on larger screens
                    $('#viewWeb').prop('checked', true).trigger('change');
                }
            });

            // Initialize tooltips for better UX
            $('[data-toggle="tooltip"]').tooltip();

            // Add helpful keyboard shortcut info
            const shortcuts = `
        <div class="mt-3 p-2 bg-light rounded">
            <small class="text-muted">
                <strong>Phím tắt:</strong><br>
                Ctrl+P: In | Ctrl++: Phóng to | Ctrl+-: Thu nhỏ | Ctrl+0: Reset zoom
            </small>
        </div>
    `;

            // Add shortcuts info to the sample data card
            $('.card:has(.fa-database)').find('.card-body').append(shortcuts);
        });
    </script>

    <style>
        .preview-web {
            background: #f8f9fa;
            min-height: 600px;
        }

        .preview-print {
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: 20px auto;
            padding: 40px;
            max-width: 210mm;
            /* A4 width */
            min-height: 297mm;
            /* A4 height */
        }

        .preview-mobile {
            background: #f8f9fa;
            max-width: 375px;
            margin: 0 auto;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            #previewContent {
                padding: 0 !important;
                box-shadow: none !important;
            }

            .preview-web,
            .preview-mobile {
                background: white !important;
                box-shadow: none !important;
                margin: 0 !important;
                padding: 0 !important;
                max-width: none !important;
                min-height: auto !important;
            }
        }

        .btn-check:checked+.btn {
            background-color: #007bff;
            border-color: #007bff;
            color: white;
        }

        #previewContent {
            transition: transform 0.2s ease;
            transform-origin: top left;
        }

        /* Custom styles for contract content */
        #previewContent h1,
        #previewContent h2,
        #previewContent h3 {
            text-align: center;
            font-weight: bold;
            margin: 20px 0;
        }

        #previewContent .signature-section {
            margin-top: 40px;
        }

        #previewContent .party-info {
            margin: 15px 0;
        }

        #previewContent .clause-section {
            margin: 20px 0;
        }

        #previewContent table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        #previewContent table th,
        #previewContent table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        #previewContent table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
    </style>
@endsection

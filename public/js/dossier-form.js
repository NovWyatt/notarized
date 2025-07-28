    let partiesData = @json($litigants);
    let assetsData = @json($assets);
    let partyIndex = 0;
    let assetIndex = 0;
    let selectedTemplateId = null;

    // Search timeout variables
    let litigantSearchTimeout = null;
    let assetSearchTimeout = null;

    // Helper function để setup search cho litigant input
    function setupLitigantSearch(input) {
        if (!input) return;

        const resultsContainer = input.nextElementSibling;
        if (!resultsContainer || !resultsContainer.classList.contains('search-results')) {
            console.warn('Search results container not found for litigant search');
            return;
        }

        input.addEventListener('input', function () {
            const query = this.value ? this.value.trim() : '';

            clearTimeout(litigantSearchTimeout);

            if (query.length < 2) {
                resultsContainer.classList.add('d-none');
                resultsContainer.innerHTML = '';
                return;
            }

            litigantSearchTimeout = setTimeout(() => {
                console.log('Searching litigants for:', query);

                resultsContainer.innerHTML = '<div class="p-2 text-muted"><i class="fas fa-spinner fa-spin me-1"></i>Đang tìm kiếm...</div>';
                resultsContainer.classList.remove('d-none');

                fetch(`/api/search-litigants?q=${encodeURIComponent(query)}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        console.log('Litigant search response status:', response.status);
                        if (!response.ok) {
                            throw new Error('HTTP error! status: ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Litigant search data:', data);

                        if (!Array.isArray(data) || data.length === 0) {
                            resultsContainer.innerHTML = '<div class="p-2 text-muted"><i class="fas fa-user-slash me-1"></i>Không tìm thấy đương sự nào</div>';
                        } else {
                            resultsContainer.innerHTML = data.map(item => {
                                let identityInfo = '';
                                if (item.identity_documents && item.identity_documents.length > 0) {
                                    identityInfo = `<br><small class="text-info"><i class="fas fa-id-card me-1"></i>${item.identity_documents[0].document_type}: ${item.identity_documents[0].document_number}</small>`;
                                }

                                return `<div class="search-result-item p-2 border-bottom"
                                                         data-id="${item.id || ''}"
                                                         data-name="${item.full_name || ''}"
                                                         data-type="${item.type || ''}"
                                                         style="cursor: pointer;">
                                                        <div class="fw-medium"><i class="fas fa-user me-1"></i>${item.full_name || 'N/A'}</div>
                                                        <small class="text-muted">Loại: ${item.type_label || item.type || ''}</small>
                                                        ${identityInfo}
                                                    </div>`;
                            }).join('');

                            // Add click handlers
                            resultsContainer.querySelectorAll('.search-result-item').forEach(item => {
                                item.addEventListener('click', function () {
                                    const id = this.getAttribute('data-id');
                                    const name = this.getAttribute('data-name');
                                    const type = this.getAttribute('data-type');

                                    console.log('Selected litigant:', { id, name, type });

                                    // Set values cho hidden input và display
                                    const partyItem = input.closest('.party-item');
                                    if (partyItem) {
                                        const hiddenInput = partyItem.querySelector('input[name*="[litigant_id]"]');
                                        const displayDiv = partyItem.querySelector('.selected-litigant-display');

                                        if (hiddenInput) {
                                            hiddenInput.value = id;
                                            console.log('Set litigant ID:', id);
                                        }
                                        if (displayDiv) {
                                            displayDiv.innerHTML = `<div class="alert alert-info p-2 mb-2">
                                                            <i class="fas fa-user me-2"></i><strong>${name}</strong>
                                                            <br><small>Loại: ${type}</small>
                                                        </div>`;
                                            displayDiv.classList.remove('d-none');
                                        }

                                        input.value = name;
                                        resultsContainer.classList.add('d-none');
                                    }
                                });
                            });
                        }

                        resultsContainer.classList.remove('d-none');
                    })
                    .catch(error => {
                        console.error('Error searching litigants:', error);
                        resultsContainer.innerHTML = '<div class="p-2 text-danger"><i class="fas fa-exclamation-triangle me-1"></i>Lỗi tìm kiếm. Vui lòng thử lại.</div>';
                        resultsContainer.classList.remove('d-none');
                    });
            }, 300);
        });

        // Hide results when clicking outside
        document.addEventListener('click', function (event) {
            if (!input.contains(event.target) && !resultsContainer.contains(event.target)) {
                resultsContainer.classList.add('d-none');
            }
        });

        // Clear selection when input is cleared
        input.addEventListener('input', function () {
            if (this.value.trim() === '') {
                const partyItem = this.closest('.party-item');
                if (partyItem) {
                    const hiddenInput = partyItem.querySelector('input[name*="[litigant_id]"]');
                    const displayDiv = partyItem.querySelector('.selected-litigant-display');

                    if (hiddenInput) hiddenInput.value = '';
                    if (displayDiv) displayDiv.classList.add('d-none');
                }
            }
        });
    }

    // Helper function để setup search cho asset input
    function setupAssetSearch(input) {
        if (!input) return;

        const resultsContainer = input.nextElementSibling;
        if (!resultsContainer || !resultsContainer.classList.contains('search-results')) {
            console.warn('Search results container not found for asset search');
            return;
        }

        input.addEventListener('input', function () {
            const query = this.value ? this.value.trim() : '';

            clearTimeout(assetSearchTimeout);

            if (query.length < 2) {
            resultsContainer.classList.add('d-none');
            resultsContainer.innerHTML = '';
            return
        }

        assetSearchTimeout = setTimeout(() => {
            console.log('Searching assets for:', query);

            resultsContainer.innerHTML = '<div class="p-2 text-muted"><i class="fas fa-spinner fa-spin me-1"></i>Đang tìm kiếm...</div>';
            resultsContainer.classList.remove('d-none');

            fetch(`/api/search-assets?q=${encodeURIComponent(query)}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => {
                    console.log('Asset search response status:', response.status);
                    if (!response.ok) {
                        return response.text().then(text => {
                            console.error('Asset search error response:', text);
                            throw new Error('HTTP error! status: ' + response.status);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Asset search data:', data);

                    if (data.error) {
                        resultsContainer.innerHTML = `<div class="p-2 text-danger"><i class="fas fa-exclamation-triangle me-1"></i>${data.error}</div>`;
                    } else if (!Array.isArray(data) || data.length === 0) {
                        resultsContainer.innerHTML = '<div class="p-2 text-muted"><i class="fas fa-building-slash me-1"></i>Không tìm thấy tài sản nào</div>';
                    } else {
                        resultsContainer.innerHTML = data.map(item => {
                            const typeIcons = {
                                'land_plot': '<i class="fas fa-map-marked-alt me-1"></i>',
                                'house': '<i class="fas fa-home me-1"></i>',
                                'apartment': '<i class="fas fa-building me-1"></i>',
                                'vehicle': '<i class="fas fa-car me-1"></i>',
                                'other': '<i class="fas fa-cube me-1"></i>'
                            };
                            const typeIcon = typeIcons[item.asset_type] || typeIcons.other;

                            return `<div class="search-result-item p-2 border-bottom"
                                                         data-id="${item.id || ''}"
                                                         data-text="${item.text || ''}"
                                                         data-type="${item.asset_type || ''}"
                                                         style="cursor: pointer;">
                                                        <div class="fw-medium">${typeIcon} ${item.text || 'N/A'}</div>
                                                        <small class="text-muted">${item.type_label || ''}</small>
                                                    </div>`;
                        }).join('');

                        // Add click handlers
                        resultsContainer.querySelectorAll('.search-result-item').forEach(item => {
                            item.addEventListener('click', function () {
                                const id = this.getAttribute('data-id');
                                const text = this.getAttribute('data-text');
                                const type = this.getAttribute('data-type');

                                console.log('Selected asset:', { id, text, type });

                                // Set values cho hidden input và display
                                const assetItem = input.closest('.asset-item');
                                if (assetItem) {
                                    const hiddenInput = assetItem.querySelector('input[name*="[asset_id]"]');
                                    const displayDiv = assetItem.querySelector('.selected-asset-display');

                                    if (hiddenInput) {
                                        hiddenInput.value = id;
                                        console.log('Set asset ID:', id);
                                    }
                                    if (displayDiv) {
                                        const typeIcons = {
                                            'land_plot': '<i class="fas fa-map-marked-alt me-1"></i>',
                                            'house': '<i class="fas fa-home me-1"></i>',
                                            'apartment': '<i class="fas fa-building me-1"></i>',
                                            'vehicle': '<i class="fas fa-car me-1"></i>',
                                            'other': '<i class="fas fa-cube me-1"></i>'
                                        };
                                        const icon = typeIcons[type] || typeIcons.other;

                                        displayDiv.innerHTML = `<div class="alert alert-success p-2 mb-2">
                                                            ${icon}<strong>${text}</strong>
                                                            <br><small>Loại: ${item.type_label || type}</small>
                                                        </div>`;
                                        displayDiv.classList.remove('d-none');
                                    }

                                    input.value = text;
                                    resultsContainer.classList.add('d-none');
                                }
                            });
                        });
                    }

                    resultsContainer.classList.remove('d-none');
                })
                .catch(error => {
                    console.error('Error searching assets:', error);
                    resultsContainer.innerHTML = '<div class="p-2 text-danger"><i class="fas fa-exclamation-triangle me-1"></i>Lỗi tìm kiếm. Vui lòng thử lại.</div>';
                    resultsContainer.classList.remove('d-none');
                });
        }, 300);
    });

    // Hide results when clicking outside
    document.addEventListener('click', function (event) {
        if (!input.contains(event.target) && !resultsContainer.contains(event.target)) {
            resultsContainer.classList.add('d-none');
        }
    });

    // Clear selection when input is cleared
    input.addEventListener('input', function () {
        if (this.value.trim() === '') {
            const assetItem = this.closest('.asset-item');
            if (assetItem) {
                const hiddenInput = assetItem.querySelector('input[name*="[asset_id]"]');
                const displayDiv = assetItem.querySelector('.selected-asset-display');

                if (hiddenInput) hiddenInput.value = '';
                if (displayDiv) displayDiv.classList.add('d-none');
            }
        }
    });
                        }

    // Các function khác giữ nguyên...
    function selectTemplate(templateId) {
        selectedTemplateId = templateId;
        document.getElementById('selectedTemplateId').value = templateId;
        loadTemplateInfo(templateId);
        resetContractForm();
        new bootstrap.Modal(document.getElementById('createContractModal')).show();
    }

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

    function previewTemplate(templateId) {
        fetch(`{{ route('admin.dossiers.ajax.template-preview', $dossier->id) }}?template_id=${templateId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('templatePreviewContent').innerHTML = data.content;
                    document.querySelector('#previewTemplateModal .modal-title').textContent = `Xem Trước: ${data.template_name}`;
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

    function useThisTemplate() {
        const templateId = document.getElementById('previewTemplateModal').getAttribute('data-template-id');
        if (templateId) {
            bootstrap.Modal.getInstance(document.getElementById('previewTemplateModal')).hide();
            selectTemplate(templateId);
        }
    }

    function resetContractForm() {
        document.getElementById('partiesContainer').innerHTML = '';
        document.getElementById('assetsContainer').innerHTML = '';
        partyIndex = 0;
        assetIndex = 0;
        addParty();
    }

    function addParty() {
        const container = document.getElementById('partiesContainer');
        const partyHtml = `
                                <div class="party-item" data-index="${partyIndex}">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="form-label">Đương sự <span class="text-danger">*</span></label>
                                            <div class="search-container position-relative">
                                                <input type="text" class="form-control litigant-search"
                                                       placeholder="Tìm kiếm đương sự..."
                                                       autocomplete="off">
                                                <div class="search-results position-absolute w-100 bg-white border rounded-bottom d-none"
                                                     style="z-index: 9999 !important; max-height: 200px; overflow-y: auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1);"></div>
                                            </div>
                                            <input type="hidden" name="parties[${partyIndex}][litigant_id]" required>
                                            <div class="selected-litigant-display d-none"></div>
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

        // Setup search cho input vừa tạo
        const newSearchInput = container.querySelector(`[data-index="${partyIndex}"] .litigant-search`);
        if (newSearchInput) {
            setupLitigantSearch(newSearchInput);
            console.log('Setup litigant search for party:', partyIndex);
        }

        partyIndex++;
    }

    function removeParty(index) {
        const partyElement = document.querySelector(`[data-index="${index}"]`);
        if (partyElement) {
            partyElement.remove();
        }

        if (document.querySelectorAll('.party-item').length === 0) {
            addParty();
        }
    }

    function addAsset() {
        const container = document.getElementById('assetsContainer');
        const assetHtml = `
                                <div class="asset-item" data-index="${assetIndex}">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <label class="form-label">Tài sản</label>
                                            <div class="search-container position-relative">
                                                <input type="text" class="form-control asset-search"
                                                       placeholder="Tìm kiếm tài sản..."
                                                       autocomplete="off">
                                                <div class="search-results position-absolute w-100 bg-white border rounded-bottom d-none"
                                                     style="z-index: 9999 !important; max-height: 200px; overflow-y: auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1);"></div>
                                            </div>
                                            <input type="hidden" name="assets[${assetIndex}][asset_id]">
                                            <div class="selected-asset-display d-none"></div>
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

        // Setup search cho input vừa tạo
        const newSearchInput = container.querySelector(`[data-index="${assetIndex}"] .asset-search`);
        if (newSearchInput) {
            setupAssetSearch(newSearchInput);
            console.log('Setup asset search for asset:', assetIndex);
        }

        assetIndex++;
    }

    function removeAsset(index) {
        const assetElement = document.querySelector(`[data-index="${index}"]`);
        if (assetElement) {
            assetElement.remove();
        }
    }

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
    $(document).ready(function () {
        console.log('Document ready - initializing dossier show page');

        // Add first party by default when modal opens
        $('#createContractModal').on('show.bs.modal', function () {
            console.log('Create contract modal opening');
            if (document.querySelectorAll('.party-item').length === 0) {
                addParty();
            }
        });

        // Handle modal close events
        $('#createContractModal').on('hidden.bs.modal', function () {
            console.log('Create contract modal closed');
            // Reset form when modal closes
            resetContractForm();
        });

        // Handle preview modal events
        $('#previewTemplateModal').on('show.bs.modal', function () {
            console.log('Preview template modal opening');
        });

        $('#previewTemplateModal').on('hidden.bs.modal', function () {
            console.log('Preview template modal closed');
            // Clear preview content to save memory
            document.getElementById('templatePreviewContent').innerHTML = '';
        });

        // Form validation before submit
        $('#createContractForm').on('submit', function (e) {
            console.log('Form submit triggered');

            // Check if template is selected
            const templateId = document.getElementById('selectedTemplateId').value;
            if (!templateId) {
                e.preventDefault();
                alert('Vui lòng chọn mẫu hợp đồng');
                return false;
            }

            // Check if at least one party is selected
            const partyInputs = document.querySelectorAll('input[name*="[litigant_id]"]');
            let hasValidParty = false;
            partyInputs.forEach(input => {
                if (input.value && input.value.trim() !== '') {
                    hasValidParty = true;
                }
            });

            if (!hasValidParty) {
                e.preventDefault();
                alert('Vui lòng chọn ít nhất một đương sự');
                return false;
            }

            // Check if required fields are filled
            const requiredFields = document.querySelectorAll('#createContractForm [required]');
            let hasEmptyRequired = false;
            requiredFields.forEach(field => {
                if (!field.value || field.value.trim() === '') {
                    hasEmptyRequired = true;
                    field.classList.add('is-invalid');

                    // Remove invalid class after user starts typing
                    field.addEventListener('input', function () {
                        this.classList.remove('is-invalid');
                    }, { once: true });
                }
            });

            if (hasEmptyRequired) {
                e.preventDefault();
                alert('Vui lòng điền đầy đủ các trường bắt buộc');
                return false;
            }

            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang tạo hợp đồng...';

                // Re-enable button after 10 seconds as fallback
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Tạo Hợp Đồng';
                }, 10000);
            }

            console.log('Form validation passed, submitting...');
            return true;
        });

        // Handle accordion collapse for better UX
        const accordionButtons = document.querySelectorAll('#contractTypesAccordion .accordion-button');
        accordionButtons.forEach(button => {
            button.addEventListener('click', function () {
                console.log('Accordion toggled:', this.textContent.trim());
            });
        });

        // Auto-focus search inputs when they become visible
        $(document).on('shown.bs.modal', '#createContractModal', function () {
            const firstLitigantSearch = document.querySelector('.litigant-search');
            if (firstLitigantSearch) {
                setTimeout(() => {
                    firstLitigantSearch.focus();
                }, 300);
            }
        });

        // Handle keyboard shortcuts
        $(document).on('keydown', function (e) {
            // Ctrl/Cmd + Enter to submit form when modal is open
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                const modal = document.getElementById('createContractModal');
                if (modal && modal.classList.contains('show')) {
                    const form = document.getElementById('createContractForm');
                    if (form) {
                        form.dispatchEvent(new Event('submit'));
                    }
                }
            }

            // Escape to close search results
            if (e.key === 'Escape') {
                document.querySelectorAll('.search-results').forEach(container => {
                    container.classList.add('d-none');
                });
            }
        });

        // Auto-save form data to localStorage (optional feature)
        let formSaveTimeout;
        $(document).on('input change', '#createContractForm input, #createContractForm select, #createContractForm textarea', function () {
            clearTimeout(formSaveTimeout);
            formSaveTimeout = setTimeout(function () {
                saveFormData();
            }, 1000);
        });

        // Restore form data on modal open (optional feature)
        $('#createContractModal').on('show.bs.modal', function () {
            setTimeout(restoreFormData, 100);
        });

        // Clear form data when successfully submitted
        $('#createContractForm').on('submit', function () {
            clearFormData();
        });

        console.log('Dossier show page initialization completed');
    });

    // Auto-save form data functions (optional feature for better UX)
    function saveFormData() {
        try {
            const formData = {};
            const form = document.getElementById('createContractForm');
            if (!form) return;

            // Save basic form fields
            const inputs = form.querySelectorAll('input:not([type="hidden"]), select, textarea');
            inputs.forEach(input => {
                if (input.name && input.value) {
                    formData[input.name] = input.value;
                }
            });

            // Save to localStorage with expiration
            const saveData = {
                data: formData,
                timestamp: Date.now(),
                expires: Date.now() + (1000 * 60 * 30) // 30 minutes
            };

            localStorage.setItem('dossier_contract_form_draft', JSON.stringify(saveData));
            console.log('Form data auto-saved');
        } catch (error) {
            console.warn('Could not save form data:', error);
        }
    }

    function restoreFormData() {
        try {
            const savedData = localStorage.getItem('dossier_contract_form_draft');
            if (!savedData) return;

            const parseData = JSON.parse(savedData);

            // Check if data is expired
            if (Date.now() > parseData.expires) {
                localStorage.removeItem('dossier_contract_form_draft');
                return;
            }

            const form = document.getElementById('createContractForm');
            if (!form || !parseData.data) return;

            // Restore form fields
            Object.keys(parseData.data).forEach(name => {
                const field = form.querySelector(`[name="${name}"]`);
                if (field && field.type !== 'hidden') {
                    field.value = parseData.data[name];
                }
            });

            console.log('Form data restored from auto-save');

            // Show notification to user
            showNotification('Đã khôi phục dữ liệu form đã lưu', 'info');
        } catch (error) {
            console.warn('Could not restore form data:', error);
        }
    }

    function clearFormData() {
        try {
            localStorage.removeItem('dossier_contract_form_draft');
            console.log('Form data cleared');
        } catch (error) {
            console.warn('Could not clear form data:', error);
        }
    }

    // Notification helper function
    function showNotification(message, type = 'info', duration = 3000) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} position-fixed`;
        notification.style.cssText = `
                top: 20px;
                right: 20px;
                z-index: 10500;
                min-width: 300px;
                opacity: 0;
                transform: translateX(100%);
                transition: all 0.3s ease;
            `;
        notification.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
                    <span>${message}</span>
                    <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
                </div>
            `;

        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.style.opacity = '1';
            notification.style.transform = 'translateX(0)';
        }, 10);

        // Auto remove
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, duration);
    }

    // Enhanced error handling
    window.addEventListener('error', function (event) {
        console.error('JavaScript error occurred:', {
            message: event.message,
            filename: event.filename,
            lineno: event.lineno,
            colno: event.colno,
            error: event.error
        });

        // Show user-friendly error message for critical errors
        if (event.message && event.message.includes('fetch')) {
            showNotification('Lỗi kết nối mạng. Vui lòng kiểm tra kết nối internet.', 'error');
        }
    });

    window.addEventListener('unhandledrejection', function (event) {
        console.error('Unhandled promise rejection:', event.reason);

        // Handle fetch errors specifically
        if (event.reason && event.reason.message && event.reason.message.includes('HTTP error')) {
            showNotification('Lỗi kết nối với server. Vui lòng thử lại.', 'error');
        }
    });

    // Performance monitoring (optional)
    if (window.performance && window.performance.measure) {
        window.addEventListener('load', function () {
            setTimeout(() => {
                const perfData = performance.getEntriesByType('navigation')[0];
                console.log('Page performance:', {
                    domContentLoaded: Math.round(perfData.domContentLoadedEventEnd - perfData.domContentLoadedEventStart),
                    loadComplete: Math.round(perfData.loadEventEnd - perfData.loadEventStart),
                    totalTime: Math.round(perfData.loadEventEnd - perfData.fetchStart)
                });
            }, 1000);
        });
    }

    // Utility function for debugging
    function debugFormState() {
        const form = document.getElementById('createContractForm');
        if (!form) {
            console.log('Form not found');
            return;
        }

        console.log('=== FORM DEBUG INFO ===');
        console.log('Template ID:', document.getElementById('selectedTemplateId')?.value);

        const parties = form.querySelectorAll('.party-item');
        console.log('Parties count:', parties.length);
        parties.forEach((party, index) => {
            const litigantId = party.querySelector('input[name*="[litigant_id]"]')?.value;
            const role = party.querySelector('select[name*="[party_type]"]')?.value;
            const group = party.querySelector('select[name*="[group_name]"]')?.value;
            console.log(`Party ${index}:`, { litigantId, role, group });
        });

        const assets = form.querySelectorAll('.asset-item');
        console.log('Assets count:', assets.length);
        assets.forEach((asset, index) => {
            const assetId = asset.querySelector('input[name*="[asset_id]"]')?.value;
            console.log(`Asset ${index}:`, { assetId });
        });

        console.log('=====================');
    }

    // Make debug function available globally for testing
    window.debugFormState = debugFormState;

    console.log('Dossier show script fully loaded and initialized');

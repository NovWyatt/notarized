// public/js/assets-form.js
// JavaScript for asset create and edit forms

AssetManager.form = {
    // Initialize form functionality
    init: function(mode = 'create') {
        this.mode = mode;
        this.initAssetTypeChange();
        this.initFormValidation();
        this.initResetButton();

        if (mode === 'edit') {
            this.initEditMode();
        }
    },

    // Initialize asset type dropdown change handler
    initAssetTypeChange: function() {
        const assetTypeSelect = document.getElementById('asset_type');

        if (assetTypeSelect) {
            assetTypeSelect.addEventListener('change', (e) => {
                this.handleAssetTypeChange(e.target.value);
            });

            // For edit mode, handle existing sections on page load
            if (this.mode === 'edit' && assetTypeSelect.value) {
                this.hideInappropriateSections(assetTypeSelect.value);
            }
        }
    },

    // Handle asset type change
    handleAssetTypeChange: function(assetType) {
        const dynamicFields = document.getElementById('dynamic-fields');

        if (!dynamicFields) return;

        console.log('Asset type changed to:', assetType);

        if (!assetType) {
            dynamicFields.innerHTML = '';
            return;
        }

        if (this.mode === 'edit') {
            this.handleEditModeTypeChange(assetType, dynamicFields);
        } else {
            this.handleCreateModeTypeChange(assetType, dynamicFields);
        }
    },

    // Handle asset type change in create mode
    handleCreateModeTypeChange: function(assetType, dynamicFields) {
        // Show loading
        AssetManager.utils.removeLoading();
        AssetManager.utils.showLoading(dynamicFields, 'Đang tải form...');

        // Fetch dynamic fields
        const url = `${AssetManager.config.routes.getFields}?asset_type=${encodeURIComponent(assetType)}`;

        AssetManager.utils.apiRequest(url, { method: 'GET' })
            .then(data => {
                AssetManager.utils.removeLoading();
                this.renderDynamicFields(data, dynamicFields);
            })
            .catch(error => {
                AssetManager.utils.removeLoading();
                AssetManager.utils.showError(dynamicFields, error);
            });
    },

    // Handle asset type change in edit mode
    handleEditModeTypeChange: function(assetType, dynamicFields) {
        // Define which sections should be visible for this asset type
        const shouldShow = this.getSectionVisibility(assetType);

        // Find existing sections
        const existingSections = {
            certificate: AssetManager.utils.findSectionByText(dynamicFields, 'Thông tin Giấy Chứng Nhận'),
            landPlot: AssetManager.utils.findSectionByText(dynamicFields, 'Thông tin Thửa Đất'),
            house: AssetManager.utils.findSectionByText(dynamicFields, 'Thông tin Nhà Ở'),
            apartment: AssetManager.utils.findSectionByText(dynamicFields, 'Thông tin Căn Hộ'),
            vehicle: AssetManager.utils.findSectionByText(dynamicFields, 'Thông tin Phương Tiện')
        };

        // Remove sections that shouldn't be visible
        Object.keys(existingSections).forEach(key => {
            if (existingSections[key] && !shouldShow[key]) {
                existingSections[key].remove();
            }
        });

        // Check what sections need to be added
        const needsToAdd = {};
        Object.keys(shouldShow).forEach(key => {
            needsToAdd[key] = shouldShow[key] && !existingSections[key];
        });

        const needsUpdate = Object.values(needsToAdd).some(needed => needed);

        if (needsUpdate) {
            AssetManager.utils.showLoading(dynamicFields, 'Đang tải thêm form...');

            const url = `${AssetManager.config.routes.getFields}?asset_type=${encodeURIComponent(assetType)}`;

            AssetManager.utils.apiRequest(url, { method: 'GET' })
                .then(data => {
                    AssetManager.utils.removeLoading();
                    this.addMissingFields(data, dynamicFields, needsToAdd);
                })
                .catch(error => {
                    AssetManager.utils.removeLoading();
                    AssetManager.utils.showError(dynamicFields, error);
                });
        }
    },

    // Get section visibility based on asset type
    getSectionVisibility: function(assetType) {
        return {
            certificate: assetType.includes('real_estate'),
            landPlot: assetType.includes('real_estate'),
            house: assetType === 'real_estate_house',
            apartment: assetType === 'real_estate_apartment',
            vehicle: assetType.includes('movable_property')
        };
    },

    // Hide inappropriate sections for edit mode
    hideInappropriateSections: function(assetType) {
        const dynamicFields = document.getElementById('dynamic-fields');
        if (!dynamicFields) return;

        const shouldShow = this.getSectionVisibility(assetType);

        const sections = {
            'Thông tin Giấy Chứng Nhận': shouldShow.certificate,
            'Thông tin Thửa Đất': shouldShow.landPlot,
            'Thông tin Nhà Ở': shouldShow.house,
            'Thông tin Căn Hộ': shouldShow.apartment,
            'Thông tin Phương Tiện': shouldShow.vehicle
        };

        Object.keys(sections).forEach(sectionText => {
            const section = AssetManager.utils.findSectionByText(dynamicFields, sectionText);
            if (section && !sections[sectionText]) {
                section.remove();
            }
        });
    },

    // Add missing fields to the form
    addMissingFields: function(data, container, needsToAdd) {
        let fieldsHtml = '';

        if (needsToAdd.certificate && data.certificate_fields) {
            fieldsHtml += AssetManager.fieldTemplates.certificate;
        }
        if (needsToAdd.landPlot && data.land_plot_fields) {
            fieldsHtml += AssetManager.fieldTemplates.landPlot;
        }
        if (needsToAdd.house && data.house_fields) {
            fieldsHtml += AssetManager.fieldTemplates.house;
        }
        if (needsToAdd.apartment && data.apartment_fields) {
            fieldsHtml += AssetManager.fieldTemplates.apartment;
        }
        if (needsToAdd.vehicle && data.vehicle_fields) {
            fieldsHtml += AssetManager.fieldTemplates.vehicle;
        }

        if (fieldsHtml) {
            container.insertAdjacentHTML('beforeend', fieldsHtml);
        }
    },

    // Render dynamic fields for create mode
    renderDynamicFields: function(data, container) {
        let fieldsHtml = '';

        if (data.certificate_fields) {
            fieldsHtml += AssetManager.fieldTemplates.certificate;
        }
        if (data.land_plot_fields) {
            fieldsHtml += AssetManager.fieldTemplates.landPlot;
        }
        if (data.house_fields) {
            fieldsHtml += AssetManager.fieldTemplates.house;
        }
        if (data.apartment_fields) {
            fieldsHtml += AssetManager.fieldTemplates.apartment;
        }
        if (data.vehicle_fields) {
            fieldsHtml += AssetManager.fieldTemplates.vehicle;
        }

        container.innerHTML = fieldsHtml;
    },

    // Initialize form validation
    initFormValidation: function() {
        const form = document.getElementById('assetForm');

        if (form) {
            form.addEventListener('submit', (e) => {
                if (!this.validateForm(e)) {
                    e.preventDefault();
                    return false;
                }
                this.showSubmitLoading(e.target);
            });
        }
    },

    // Validate form before submission
    validateForm: function(e) {
        const assetType = document.getElementById('asset_type').value;

        if (!assetType) {
            alert('Vui lòng chọn loại tài sản.');
            document.getElementById('asset_type').focus();
            return false;
        }

        return true;
    },

    // Show loading state during form submission
    showSubmitLoading: function(form) {
        const submitBtn = form.querySelector('button[type="submit"]');

        if (submitBtn) {
            const originalText = submitBtn.innerHTML;
            const loadingText = this.mode === 'edit' ? 'Đang cập nhật...' : 'Đang tạo...';

            submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status"></span>${loadingText}`;
            submitBtn.disabled = true;

            // Re-enable button after delay in case of validation errors
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 10000);
        }
    },

    // Initialize reset button
    initResetButton: function() {
        const resetBtn = document.querySelector('button[onclick="resetForm()"]');

        if (resetBtn) {
            resetBtn.removeAttribute('onclick');
            resetBtn.addEventListener('click', () => {
                this.resetForm();
            });
        }
    },

    // Reset form functionality
    resetForm: function() {
        const message = this.mode === 'edit'
            ? 'Bạn có chắc chắn muốn đặt lại form? Tất cả thay đổi chưa lưu sẽ bị mất.'
            : 'Bạn có chắc chắn muốn đặt lại form?';

        if (confirm(message)) {
            if (this.mode === 'edit') {
                window.location.reload();
            } else {
                document.getElementById('assetForm').reset();
                document.getElementById('dynamic-fields').innerHTML = '';
            }
        }
    },

    // Initialize edit mode specific functionality
    initEditMode: function() {
        this.initAutoSave();
    },

    // Initialize auto-save functionality (optional)
    initAutoSave: function() {
        if (!AssetManager.config.routes.update) return;

        let autoSaveTimeout;
        const scheduleAutoSave = AssetManager.utils.debounce(() => {
            this.performAutoSave();
        }, 30000); // Auto-save after 30 seconds of inactivity

        // Attach auto-save to form inputs (optional - can be enabled as needed)
        // Uncomment the lines below to enable auto-save
        /*
        document.querySelectorAll('#assetForm input, #assetForm textarea, #assetForm select').forEach(input => {
            input.addEventListener('input', scheduleAutoSave);
            input.addEventListener('change', scheduleAutoSave);
        });
        */
    },

    // Perform auto-save (optional)
    performAutoSave: function() {
        const formData = new FormData(document.getElementById('assetForm'));

        // Only save basic fields, not the full form
        const basicData = {
            asset_name: formData.get('asset_name'),
            estimated_value: formData.get('estimated_value'),
            notes: formData.get('notes'),
            _token: AssetManager.config.csrf.token,
            _method: 'PUT'
        };

        AssetManager.utils.apiRequest(AssetManager.config.routes.update, {
            method: 'POST',
            body: JSON.stringify(basicData)
        })
        .then(data => {
            console.log('Auto-saved successfully');
            AssetManager.utils.showSuccess('Đã lưu tự động', 2000);
        })
        .catch(error => {
            console.log('Auto-save failed:', error);
        });
    }
};

// Global function for backward compatibility
window.resetForm = function() {
    if (AssetManager.form) {
        AssetManager.form.resetForm();
    }
};

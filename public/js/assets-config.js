// public/js/assets-config.js
// Configuration and initialization for all asset-related JavaScript

/**
 * Asset Management Configuration
 * This file contains global configuration for the asset management system
 */

// Check if AssetManager exists, if not create it
window.AssetManager = window.AssetManager || {};

// Extend configuration with default settings
AssetManager.config = Object.assign({
    // Default routes (will be overridden by individual pages)
    routes: {
        index: '/properties',
        create: '/properties/create',
        edit: '/properties/{id}/edit',
        show: '/properties/{id}',
        store: '/properties',
        update: '/properties/{id}',
        destroy: '/properties/{id}',
        getFields: '/properties/get-fields',
        bulkDelete: '/properties/bulk-delete',
        export: '/properties/export'
    },

    // CSRF configuration
    csrf: {
        token: '',
        header: 'X-CSRF-TOKEN'
    },

    // UI Configuration
    ui: {
        loadingDelay: 300, // ms before showing loading indicator
        successMessageDuration: 3000, // ms
        autoSaveDelay: 30000, // ms
        confirmDeleteMessage: 'Bạn có chắc chắn muốn xóa?',
        confirmResetMessage: 'Bạn có chắc chắn muốn đặt lại form?'
    },

    // Asset type mapping
    assetTypes: {
        'real_estate_land': {
            label: 'Bất động sản - Đất',
            hasLandPlot: true,
            hasCertificate: true,
            hasHouse: false,
            hasApartment: false,
            hasVehicle: false
        },
        'real_estate_house': {
            label: 'Bất động sản - Nhà',
            hasLandPlot: true,
            hasCertificate: true,
            hasHouse: true,
            hasApartment: false,
            hasVehicle: false
        },
        'real_estate_apartment': {
            label: 'Bất động sản - Căn hộ',
            hasLandPlot: true,
            hasCertificate: true,
            hasHouse: false,
            hasApartment: true,
            hasVehicle: false
        },
        'movable_property_vehicle': {
            label: 'Tài sản động - Phương tiện',
            hasLandPlot: false,
            hasCertificate: false,
            hasHouse: false,
            hasApartment: false,
            hasVehicle: true
        }
    },

    // Validation rules
    validation: {
        required: ['asset_type'],
        numeric: ['estimated_value', 'area', 'construction_area', 'floor_area', 'number_of_floors'],
        date: ['issue_date', 'land_use_term', 'ownership_term', 'vehicle_issue_date'],
        maxLength: {
            'asset_name': 255,
            'notes': 1000,
            'license_plate': 20,
            'engine_number': 50,
            'chassis_number': 50
        }
    },

    // Error messages
    messages: {
        networkError: 'Lỗi kết nối mạng. Vui lòng kiểm tra kết nối internet.',
        serverError: 'Lỗi server. Vui lòng thử lại sau.',
        notFoundError: 'Không tìm thấy tài nguyên yêu cầu.',
        validationError: 'Dữ liệu không hợp lệ. Vui lòng kiểm tra lại.',
        unknownError: 'Có lỗi xảy ra. Vui lòng thử lại sau.',
        saveSuccess: 'Lưu thành công!',
        deleteSuccess: 'Xóa thành công!',
        updateSuccess: 'Cập nhật thành công!'
    }
}, AssetManager.config || {});

// Initialize common functionality
AssetManager.init = function() {
    this.initCSRF();
    this.initErrorHandling();
    this.initFormHelpers();
    this.initTooltips();
};

// Initialize CSRF token
AssetManager.initCSRF = function() {
    if (!this.config.csrf.token) {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                     document.querySelector('input[name="_token"]')?.value;
        if (token) {
            this.config.csrf.token = token;
        }
    }
};

// Initialize global error handling
AssetManager.initErrorHandling = function() {
    window.addEventListener('unhandledrejection', function(event) {
        console.error('Unhandled promise rejection:', event.reason);
        // Optionally show user-friendly error message
        if (event.reason && event.reason.message) {
            AssetManager.utils.showError(
                document.body,
                new Error('Có lỗi không mong muốn xảy ra: ' + event.reason.message)
            );
        }
    });
};

// Initialize form helpers
AssetManager.initFormHelpers = function() {
    // Auto-format number inputs
    document.addEventListener('input', function(e) {
        if (e.target.type === 'number' && e.target.step === '1000') {
            // Format thousands separator for currency inputs
            const value = e.target.value.replace(/\D/g, '');
            if (value) {
                e.target.setAttribute('data-raw-value', value);
            }
        }
    });

    // Auto-uppercase license plates
    document.addEventListener('input', function(e) {
        if (e.target.id === 'license_plate') {
            e.target.value = e.target.value.toUpperCase();
        }
    });

    // Auto-save draft functionality (can be enabled per form)
    if (localStorage && AssetManager.config.ui.autoSaveDelay) {
        document.addEventListener('input', AssetManager.utils.debounce(function(e) {
            if (e.target.form && e.target.form.id === 'assetForm') {
                AssetManager.saveDraft(e.target.form);
            }
        }, AssetManager.config.ui.autoSaveDelay));
    }
};

// Initialize tooltips
AssetManager.initTooltips = function() {
    // Initialize Bootstrap tooltips if available
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
};

// Draft saving functionality
AssetManager.saveDraft = function(form) {
    if (!form || !form.id) return;

    const formData = new FormData(form);
    const draftData = {};

    // Only save basic fields to localStorage
    ['asset_type', 'asset_name', 'estimated_value', 'notes'].forEach(field => {
        if (formData.has(field)) {
            draftData[field] = formData.get(field);
        }
    });

    try {
        localStorage.setItem(`asset_draft_${form.id}`, JSON.stringify({
            data: draftData,
            timestamp: Date.now()
        }));
        console.log('Draft saved to localStorage');
    } catch (error) {
        console.warn('Could not save draft to localStorage:', error);
    }
};

// Load draft functionality
AssetManager.loadDraft = function(formId) {
    if (!localStorage || !formId) return null;

    try {
        const draftString = localStorage.getItem(`asset_draft_${formId}`);
        if (draftString) {
            const draft = JSON.parse(draftString);
            // Check if draft is not too old (24 hours)
            const maxAge = 24 * 60 * 60 * 1000; // 24 hours in ms
            if (Date.now() - draft.timestamp < maxAge) {
                return draft.data;
            } else {
                // Remove old draft
                localStorage.removeItem(`asset_draft_${formId}`);
            }
        }
    } catch (error) {
        console.warn('Could not load draft from localStorage:', error);
    }
    return null;
};

// Clear draft functionality
AssetManager.clearDraft = function(formId) {
    if (localStorage && formId) {
        localStorage.removeItem(`asset_draft_${formId}`);
    }
};

// Enhanced utility functions
AssetManager.utils = Object.assign(AssetManager.utils || {}, {
    // Validate form data
    validateForm: function(form, rules = AssetManager.config.validation) {
        const errors = [];
        const formData = new FormData(form);

        // Check required fields
        rules.required.forEach(field => {
            if (!formData.get(field)) {
                errors.push(`Trường ${field} là bắt buộc`);
            }
        });

        // Check numeric fields
        rules.numeric.forEach(field => {
            const value = formData.get(field);
            if (value && isNaN(value)) {
                errors.push(`Trường ${field} phải là số`);
            }
        });

        // Check date fields
        rules.date.forEach(field => {
            const value = formData.get(field);
            if (value && !this.isValidDate(value)) {
                errors.push(`Trường ${field} không đúng định dạng ngày`);
            }
        });

        // Check max length
        Object.keys(rules.maxLength).forEach(field => {
            const value = formData.get(field);
            const maxLength = rules.maxLength[field];
            if (value && value.length > maxLength) {
                errors.push(`Trường ${field} không được vượt quá ${maxLength} ký tự`);
            }
        });

        return {
            isValid: errors.length === 0,
            errors: errors
        };
    },

    // Check if date is valid
    isValidDate: function(dateString) {
        const date = new Date(dateString);
        return date instanceof Date && !isNaN(date);
    },

    // Get asset type configuration
    getAssetTypeConfig: function(assetType) {
        return AssetManager.config.assetTypes[assetType] || null;
    },

    // Generate URL from route template
    generateUrl: function(route, params = {}) {
        let url = AssetManager.config.routes[route] || route;

        // Replace parameters in URL
        Object.keys(params).forEach(key => {
            url = url.replace(`{${key}}`, params[key]);
        });

        return url;
    },

    // Enhanced error message handling
    getErrorMessage: function(error) {
        if (error.message) {
            if (error.message.includes('NetworkError') || error.message.includes('Failed to fetch')) {
                return AssetManager.config.messages.networkError;
            } else if (error.message.includes('404')) {
                return AssetManager.config.messages.notFoundError;
            } else if (error.message.includes('500')) {
                return AssetManager.config.messages.serverError;
            } else if (error.message.includes('422')) {
                return AssetManager.config.messages.validationError;
            }
        }
        return AssetManager.config.messages.unknownError;
    },

    // Show loading with delay
    showLoadingDelayed: function(container, message = 'Đang tải...', delay = AssetManager.config.ui.loadingDelay) {
        setTimeout(() => {
            this.showLoading(container, message);
        }, delay);
    },

    // Confirm action
    confirm: function(message, callback) {
        if (confirm(message)) {
            callback();
        }
    }
});

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    AssetManager.init();
});

// Export for use in modules (if needed)
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AssetManager;
}

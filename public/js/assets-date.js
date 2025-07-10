document.addEventListener("DOMContentLoaded", function () {
    // ===== HELPER FUNCTIONS =====

    /**
     * Format date từ string input thành dd/mm/yyyy
     */
    function formatDate(input) {
        // Loại bỏ tất cả ký tự không phải số
        let value = input.replace(/\D/g, "");

        // Giới hạn tối đa 8 số
        if (value.length > 8) {
            value = value.substring(0, 8);
        }

        // Format theo dd/mm/yyyy
        if (value.length >= 2) {
            value = value.substring(0, 2) + "/" + value.substring(2);
        }
        if (value.length >= 5) {
            value = value.substring(0, 5) + "/" + value.substring(5);
        }

        return value;
    }

    /**
     * Validate ngày tháng
     */
    function validateDate(dateString) {
        // Kiểm tra format dd/mm/yyyy
        const dateRegex = /^(\d{2})\/(\d{2})\/(\d{4})$/;
        const match = dateString.match(dateRegex);

        if (!match) {
            return {
                valid: false,
                message: "Format không đúng. Vui lòng nhập dd/mm/yyyy",
            };
        }

        const day = parseInt(match[1]);
        const month = parseInt(match[2]);
        const year = parseInt(match[3]);

        // Kiểm tra tháng hợp lệ
        if (month < 1 || month > 12) {
            return {
                valid: false,
                message: "Tháng không hợp lệ (1-12)",
            };
        }

        // Kiểm tra năm hợp lệ (từ 1900 đến năm hiện tại + 10)
        const currentYear = new Date().getFullYear();
        if (year < 1900 || year > currentYear + 10) {
            return {
                valid: false,
                message: `Năm phải từ 1900 đến ${currentYear + 10}`,
            };
        }

        // Kiểm tra ngày hợp lệ
        const date = new Date(year, month - 1, day);
        if (
            date.getDate() !== day ||
            date.getMonth() !== month - 1 ||
            date.getFullYear() !== year
        ) {
            return {
                valid: false,
                message: "Ngày không hợp lệ",
            };
        }

        return {
            valid: true,
            date: date,
        };
    }

    /**
     * Chuyển đổi từ dd/mm/yyyy sang yyyy-mm-dd cho database
     */
    function convertToDbFormat(dateString) {
        const dateRegex = /^(\d{2})\/(\d{2})\/(\d{4})$/;
        const match = dateString.match(dateRegex);

        if (match) {
            const day = match[1];
            const month = match[2];
            const year = match[3];
            return `${year}-${month}-${day}`;
        }

        return "";
    }

    /**
     * Chuyển đổi từ yyyy-mm-dd sang dd/mm/yyyy cho hiển thị
     */
    function convertFromDbFormat(dbDateString) {
        if (!dbDateString) return "";

        const dateRegex = /^(\d{4})-(\d{2})-(\d{2})$/;
        const match = dbDateString.match(dateRegex);

        if (match) {
            const year = match[1];
            const month = match[2];
            const day = match[3];
            return `${day}/${month}/${year}`;
        }

        return "";
    }

    /**
     * Setup date input với auto format và validation cho property form
     * @param {string} inputId - ID của input field
     */
    function setupPropertyDateInput(inputId) {
        const input = document.getElementById(inputId);

        if (!input) {
            return; // Input không tồn tại, có thể chưa được render
        }

        // Nếu đã setup rồi thì skip
        if (input.hasAttribute('data-property-date-setup')) {
            return;
        }

        // Đổi input type từ "date" thành "text" để có thể format tự do
        input.type = 'text';
        input.placeholder = 'Nhập dd/mm/yyyy';
        input.maxLength = 10;

        // Nếu có giá trị ban đầu từ database (yyyy-mm-dd), chuyển đổi sang dd/mm/yyyy
        if (input.value && input.value.includes('-')) {
            const displayValue = convertFromDbFormat(input.value);
            input.value = displayValue;
        }

        // Tạo hidden input để gửi giá trị đúng format lên server
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = input.name;
        hiddenInput.id = input.id + '_hidden';

        // Đổi name của input hiển thị
        input.name = input.name + '_display';

        // Thêm hidden input vào form
        input.parentNode.insertBefore(hiddenInput, input.nextSibling);

        // Tạo div hiển thị lỗi
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.id = input.id + '_error';
        input.parentNode.insertBefore(errorDiv, hiddenInput.nextSibling);

        // Event listener cho input
        input.addEventListener("input", function (e) {
            const formatted = formatDate(e.target.value);
            e.target.value = formatted;

            // Clear previous validation states
            e.target.classList.remove("is-invalid", "is-valid");
            errorDiv.textContent = "";

            // Validate nếu đã nhập đủ 10 ký tự
            if (formatted.length === 10) {
                const validation = validateDate(formatted);
                if (validation.valid) {
                    e.target.classList.add("is-valid");
                    // Cập nhật hidden input với format database
                    hiddenInput.value = convertToDbFormat(formatted);
                } else {
                    e.target.classList.add("is-invalid");
                    errorDiv.textContent = validation.message;
                    hiddenInput.value = "";
                }
            } else {
                // Chưa nhập đủ, clear hidden value
                hiddenInput.value = "";
            }
        });

        // Event listener cho blur (khi rời khỏi field)
        input.addEventListener("blur", function (e) {
            const value = e.target.value.trim();
            if (value && value.length > 0 && value.length < 10) {
                e.target.classList.add("is-invalid");
                errorDiv.textContent = "Vui lòng nhập đầy đủ ngày/tháng/năm (8 số)";
            }
        });

        // Nếu có giá trị ban đầu, validate luôn
        if (input.value && input.value.length === 10) {
            const validation = validateDate(input.value);
            if (validation.valid) {
                input.classList.add("is-valid");
                hiddenInput.value = convertToDbFormat(input.value);
            }
        }

        // Đánh dấu đã setup
        input.setAttribute('data-property-date-setup', 'true');
    }

    // ===== SETUP CÁC DATE FIELDS CHO PROPERTY FORM =====

    /**
     * Setup tất cả date inputs trong form property
     */
    function setupAllPropertyDateInputs() {
        // Các date fields cố định
        const dateFields = [
            'issue_date',
            'vehicle_issue_date',
            'ownership_term',
            'apartment_ownership_term',
            'land_use_term'
        ];

        dateFields.forEach(fieldId => {
            setupPropertyDateInput(fieldId);
        });

        // Tự động tìm và setup các date inputs khác
        const allDateInputs = document.querySelectorAll('input[type="date"]');
        allDateInputs.forEach(input => {
            setupPropertyDateInput(input.id);
        });
    }

    /**
     * Setup date inputs cho dynamic content (khi load AJAX)
     */
    window.setupDynamicPropertyDates = function() {
        // Delay một chút để đảm bảo DOM đã được render
        setTimeout(() => {
            setupAllPropertyDateInputs();
        }, 100);
    };

    /**
     * Theo dõi thay đổi DOM và auto-setup date inputs mới
     */
    function initPropertyDateWatcher() {
        // Setup lần đầu
        setupAllPropertyDateInputs();

        // Theo dõi thay đổi DOM
        const observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                mutation.addedNodes.forEach(function (node) {
                    if (node.nodeType === 1) { // Element node
                        // Tìm date inputs mới trong node được thêm
                        const newDateInputs = node.querySelectorAll
                            ? node.querySelectorAll('input[type="date"]')
                            : [];

                        // Nếu chính node đó là date input
                        if (node.tagName === 'INPUT' && node.type === 'date') {
                            setupPropertyDateInput(node.id);
                        }

                        // Setup cho các input mới tìm thấy
                        newDateInputs.forEach(input => {
                            setupPropertyDateInput(input.id);
                        });
                    }
                });
            });
        });

        // Bắt đầu theo dõi
        observer.observe(document.body, {
            childList: true,
            subtree: true,
        });
    }

    // ===== UTILITY FUNCTIONS =====

    /**
     * Validate tất cả date fields trong property form
     */
    window.validateAllPropertyDates = function (formId = 'assetForm') {
        const form = document.getElementById(formId);
        if (!form) return true;

        const dateInputs = form.querySelectorAll('input[data-property-date-setup="true"]');
        let allValid = true;

        dateInputs.forEach(input => {
            const value = input.value.trim();
            if (value && value.length > 0) {
                if (value.length !== 10) {
                    input.classList.add("is-invalid");
                    const errorDiv = document.getElementById(input.id + '_error');
                    if (errorDiv) errorDiv.textContent = "Vui lòng nhập đầy đủ ngày/tháng/năm";
                    allValid = false;
                } else {
                    const validation = validateDate(value);
                    if (!validation.valid) {
                        input.classList.add("is-invalid");
                        const errorDiv = document.getElementById(input.id + '_error');
                        if (errorDiv) errorDiv.textContent = validation.message;
                        allValid = false;
                    }
                }
            }
        });

        return allValid;
    };

    /**
     * Clear tất cả date fields
     */
    window.clearAllPropertyDates = function (formId = 'assetForm') {
        const form = document.getElementById(formId);
        if (!form) return;

        const dateInputs = form.querySelectorAll('input[data-property-date-setup="true"]');
        const hiddenInputs = form.querySelectorAll('input[type="hidden"][id$="_hidden"]');

        dateInputs.forEach(input => {
            input.value = "";
            input.classList.remove("is-valid", "is-invalid");
        });

        hiddenInputs.forEach(input => {
            input.value = "";
        });
    };

    // Khởi tạo
    initPropertyDateWatcher();

    // ===== INTEGRATION VỚI DYNAMIC FORM =====

    // Hook vào asset type change để setup dates cho dynamic content
    const originalAssetTypeHandler = document.getElementById('asset_type')?.onchange;

    // Override asset type change handler
    if (document.getElementById('asset_type')) {
        document.getElementById('asset_type').addEventListener('change', function() {
            // Gọi handler gốc nếu có
            if (originalAssetTypeHandler) {
                originalAssetTypeHandler.call(this);
            }

            // Setup dates cho dynamic content sau một khoảng delay
            setTimeout(() => {
                window.setupDynamicPropertyDates();
            }, 500);
        });
    }
});

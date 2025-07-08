document.addEventListener("DOMContentLoaded", function () {
    // ===== HELPER FUNCTIONS =====

    /**
     * Format date
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
                message: "Format không đúng. Vui lòng nhập ddmmyyyy",
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

        // Kiểm tra năm hợp lệ (từ 1900 đến năm hiện tại)
        const currentYear = new Date().getFullYear();
        if (year < 1900 || year > currentYear) {
            return {
                valid: false,
                message: `Năm phải từ 1900 đến ${currentYear}`,
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
     * Chuyển đổi sang format yyyy-mm-dd cho database
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
     * Setup date input với auto format và validation
     * @param {string} inputId - ID của input field hiển thị
     * @param {string} hiddenId - ID của hidden input gửi lên server
     * @param {string} errorId - ID của div hiển thị error (optional)
     */
    function setupDateInput(inputId, hiddenId, errorId = null) {
        const input = document.getElementById(inputId);
        const hidden = document.getElementById(hiddenId);
        const errorDiv = errorId ? document.getElementById(errorId) : null;

        if (!input) {
            console.warn(`Input with id "${inputId}" not found`);
            return;
        }

        // Event listener cho input
        input.addEventListener("input", function (e) {
            const formatted = formatDate(e.target.value);
            e.target.value = formatted;

            // Clear previous validation states
            e.target.classList.remove("is-invalid", "is-valid");
            if (errorDiv) errorDiv.textContent = "";

            // Validate nếu đã nhập đủ 10 ký tự
            if (formatted.length === 10) {
                const validation = validateDate(formatted);
                if (validation.valid) {
                    e.target.classList.add("is-valid");
                    // Cập nhật hidden input với format database
                    if (hidden) hidden.value = convertToDbFormat(formatted);
                } else {
                    e.target.classList.add("is-invalid");
                    if (errorDiv) errorDiv.textContent = validation.message;
                    if (hidden) hidden.value = "";
                }
            } else {
                // Chưa nhập đủ, clear hidden value
                if (hidden) hidden.value = "";
            }
        });

        // Event listener cho blur (khi rời khỏi field)
        input.addEventListener("blur", function (e) {
            const value = e.target.value.trim();
            if (value && value.length > 0 && value.length < 10) {
                e.target.classList.add("is-invalid");
                if (errorDiv) {
                    errorDiv.textContent =
                        "Vui lòng nhập đầy đủ ngày/tháng/năm";
                }
            }
        });

        // Nếu có giá trị ban đầu, validate luôn
        if (input.value && input.value.length === 10) {
            const validation = validateDate(input.value);
            if (validation.valid) {
                input.classList.add("is-valid");
                if (hidden) hidden.value = convertToDbFormat(input.value);
            }
        }
    }

    // ===== SETUP CÁC DATE FIELDS =====

    // 1. Birth Date
    setupDateInput("birth_date", "birth_date_formatted", "birth_date_error");

    // 2. Issue Date (cho identity documents)
    setupDateInput(
        "issue_date_0",
        "issue_date_formatted_0",
        "issue_date_error_0"
    );

    // 3. Marriage Issue Date
    setupDateInput(
        "marriage_issue_date",
        "marriage_issue_date_formatted",
        "marriage_issue_date_error"
    );

    // 4. CI Business Registration Date
    setupDateInput(
        "ci_business_registration_date",
        "ci_business_registration_date_formatted",
        "ci_business_registration_date_error"
    );

    // 5. Business Registration Date
    setupDateInput(
        "business_registration_date",
        "business_registration_date_formatted",
        "business_registration_date_error"
    );

    // 6. Change Registration Date
    setupDateInput(
        "change_registration_date",
        "change_registration_date_formatted",
        "change_registration_date_error"
    );

    // 7. CI Change Registration Date
    setupDateInput(
        "ci_change_registration_date",
        "ci_change_registration_date_formatted",
        "ci_change_registration_date_error"
    );

    // ===== SETUP CHO DYNAMIC FIELDS (nếu có nhiều identity documents) =====

    /**
     * Setup date input cho dynamic identity documents
     * Gọi function này khi thêm mới identity document
     */
    window.setupIdentityDocumentDate = function (index) {
        setupDateInput(
            `issue_date_${index}`,
            `issue_date_formatted_${index}`,
            `issue_date_error_${index}`
        );
    };

    /**
     * Auto-detect và setup cho tất cả identity document date fields hiện có
     * Gọi function này sau khi thêm HTML mới vào DOM
     */
    window.setupAllIdentityDocumentDates = function () {
        // Tìm tất cả input có pattern issue_date_X
        const inputs = document.querySelectorAll(
            'input[id^="issue_date_"]:not([id$="_formatted"]):not([id$="_error"])'
        );

        inputs.forEach((input) => {
            const index = input.id.replace("issue_date_", "");
            // Chỉ setup nếu chưa được setup (tránh duplicate event listeners)
            if (!input.hasAttribute("data-date-setup")) {
                setupDateInput(
                    `issue_date_${index}`,
                    `issue_date_formatted_${index}`,
                    `issue_date_error_${index}`
                );
                input.setAttribute("data-date-setup", "true");
            }
        });
    };

    /**
     * Tự động theo dõi thay đổi DOM và setup date inputs mới
     */
    function initDynamicDateSetup() {
        // Setup lần đầu cho các fields có sẵn
        window.setupAllIdentityDocumentDates();

        // Theo dõi thay đổi DOM bằng MutationObserver
        const observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                // Kiểm tra các node được thêm vào
                mutation.addedNodes.forEach(function (node) {
                    if (node.nodeType === 1) {
                        // Element node
                        // Tìm input date mới trong node được thêm
                        const newDateInputs = node.querySelectorAll
                            ? node.querySelectorAll(
                                  'input[id^="issue_date_"]:not([id$="_formatted"]):not([id$="_error"])'
                              )
                            : [];

                        // Nếu chính node đó là input date
                        if (
                            node.id &&
                            node.id.startsWith("issue_date_") &&
                            !node.id.endsWith("_formatted") &&
                            !node.id.endsWith("_error")
                        ) {
                            const index = node.id.replace("issue_date_", "");
                            if (!node.hasAttribute("data-date-setup")) {
                                setupDateInput(
                                    `issue_date_${index}`,
                                    `issue_date_formatted_${index}`,
                                    `issue_date_error_${index}`
                                );
                                node.setAttribute("data-date-setup", "true");
                            }
                        }

                        // Setup cho các input date mới tìm thấy trong node
                        newDateInputs.forEach((input) => {
                            const index = input.id.replace("issue_date_", "");
                            if (!input.hasAttribute("data-date-setup")) {
                                setupDateInput(
                                    `issue_date_${index}`,
                                    `issue_date_formatted_${index}`,
                                    `issue_date_error_${index}`
                                );
                                input.setAttribute("data-date-setup", "true");
                            }
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

    // Khởi tạo dynamic setup
    initDynamicDateSetup();

    // ===== UTILITY FUNCTIONS (có thể gọi từ bên ngoài) =====

    /**
     * Validate tất cả date fields trong form
     * @param {string} formId - ID của form cần validate
     * @returns {boolean} - true nếu tất cả date fields hợp lệ
     */
    window.validateAllDateFields = function (formId = null) {
        const form = formId ? document.getElementById(formId) : document;
        const dateInputs = form.querySelectorAll(
            'input[maxlength="10"][placeholder*="ddmmyyyy"], input[placeholder*="ddmmyyyy"]'
        );

        let allValid = true;

        dateInputs.forEach((input) => {
            const value = input.value.trim();
            if (value && value.length > 0) {
                if (value.length !== 10) {
                    input.classList.add("is-invalid");
                    allValid = false;
                } else {
                    const validation = validateDate(value);
                    if (!validation.valid) {
                        input.classList.add("is-invalid");
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
    window.clearAllDateFields = function (formId = null) {
        const form = formId ? document.getElementById(formId) : document;
        const dateInputs = form.querySelectorAll(
            'input[maxlength="10"][placeholder*="ddmmyyyy"], input[placeholder*="ddmmyyyy"]'
        );
        const hiddenInputs = form.querySelectorAll(
            'input[type="hidden"][name*="date"]'
        );

        dateInputs.forEach((input) => {
            input.value = "";
            input.classList.remove("is-valid", "is-invalid");
        });

        hiddenInputs.forEach((input) => {
            input.value = "";
        });
    };
});

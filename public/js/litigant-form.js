// ================================================================

// File: public/js/litigant-form.js
// Functions for create and edit forms
// Functions cho create/edit forms

let documentCounter = 1;

/**
 * Toggle sections based on litigant type
 */
function toggleSections() {
    const type = document.getElementById("type").value;
    const individualSection = document.getElementById("individualSection");
    const organizationSection = document.getElementById("organizationSection");
    const creditInstitutionSection = document.getElementById(
        "creditInstitutionSection"
    );

    // Hide all sections
    individualSection.classList.add("d-none");
    organizationSection.classList.add("d-none");
    creditInstitutionSection.classList.add("d-none");

    // Show relevant section
    if (type === "individual") {
        individualSection.classList.remove("d-none");
    } else if (type === "organization") {
        organizationSection.classList.remove("d-none");
    } else if (type === "credit_institution") {
        creditInstitutionSection.classList.remove("d-none");
    }
}

/**
 * Add new identity document
 */
function addDocument() {
    const container = document.getElementById("identityDocumentsContainer");
    const newDocument = document.createElement("div");
    newDocument.className = "identity-document-item border rounded p-3 mb-3";
    newDocument.innerHTML = `
    <div class="row align-items-center">
        <div class="col-md-11">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <select class="form-select document-type-select"
                                name="identity_documents[${documentCounter}][document_type]"
                                onchange="toggleDocumentFields(this, ${documentCounter})">
                            <option value="">Chọn loại giấy tờ</option>
                            <option value="cccd">Căn cước công dân (12 số)</option>
                            <option value="cmnd">Chứng minh nhân dân (9 số)</option>
                            <option value="passport">Hộ chiếu</option>
                            <option value="officer_id">Chứng minh sĩ quan</option>
                            <option value="student_card">Thẻ học sinh</option>
                        </select>
                        <label>Loại giấy tờ</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control"
                               name="identity_documents[${documentCounter}][document_number]"
                               placeholder="Số giấy tờ">
                        <label>Số giấy tờ</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control"
                               id="issue_date_${documentCounter}"
                               name="identity_documents[${documentCounter}][issue_date_display]"
                               placeholder="ddmmyyyy"
                               maxlength="10">
                        <label>Ngày cấp (dd/mm/yyyy)</label>
                        <!-- Hidden input để gửi data đúng format cho server -->
                        <input type="hidden"
                               id="issue_date_formatted_${documentCounter}"
                               name="identity_documents[${documentCounter}][issue_date]">
                        <div class="invalid-feedback" id="issue_date_error_${documentCounter}"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control"
                               name="identity_documents[${documentCounter}][issued_by]"
                               placeholder="Nơi cấp">
                        <label>Nơi cấp</label>
                    </div>
                </div>
            </div>
            <!-- Student Card Specific Fields -->
            <div class="student-card-fields d-none">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control"
                                   name="identity_documents[${documentCounter}][school_name]"
                                   placeholder="Tên trường">
                            <label>Tên trường</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control"
                                   name="identity_documents[${documentCounter}][academic_year]"
                                   placeholder="Niên khóa (vd: 2023-2024)">
                            <label>Niên khóa</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-outline-danger btn-sm remove-document-btn"
                    onclick="removeDocument(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>
    `;
    container.appendChild(newDocument);

    // QUAN TRỌNG: Setup date input cho field mới
    // Script sẽ tự động detect nhờ MutationObserver,
    // hoặc có thể gọi manually để đảm bảo:
    if (typeof setupIdentityDocumentDate === "function") {
        setupIdentityDocumentDate(documentCounter);
    }

    documentCounter++;
    updateRemoveButtons();
}

/**
 * Remove identity document
 */
function removeDocument(button) {
    const documentItem = button.closest(".identity-document-item");
    documentItem.remove();
    updateRemoveButtons();
}

/**
 * Update visibility of remove buttons
 */
function updateRemoveButtons() {
    const documentItems = document.querySelectorAll(".identity-document-item");
    const removeButtons = document.querySelectorAll(".remove-document-btn");

    removeButtons.forEach((button, index) => {
        if (documentItems.length > 1) {
            button.classList.remove("d-none");
        } else {
            button.classList.add("d-none");
        }
    });
}

/**
 * Toggle student card specific fields
 */
function toggleDocumentFields(select, index) {
    const documentItem = select.closest(".identity-document-item");
    const studentCardFields = documentItem.querySelector(
        ".student-card-fields"
    );

    if (select.value === "student_card") {
        studentCardFields.classList.remove("d-none");
    } else {
        studentCardFields.classList.add("d-none");
    }
}

/**
 * Validate form before submission
 */
function validateForm() {
    const type = document.getElementById("type").value;
    const fullName = document.getElementById("full_name").value;

    let errors = [];

    if (!fullName) {
        errors.push("Họ và tên là bắt buộc");
    }

    if (!type) {
        errors.push("Loại đương sự là bắt buộc");
    }

    if (errors.length > 0) {
        alert("Lỗi xác thực:\n" + errors.join("\n"));
        return false;
    }

    alert("Xác thực thành công!");
    return true;
}

/**
 * Copy permanent address to temporary address
 */
function copyPermanentAddress() {
    const permanentFields = ["street_address", "province", "district", "ward"];

    permanentFields.forEach((field) => {
        const permanentValue = document.getElementById(
            `permanent_${field}`
        ).value;
        const temporaryField = document.getElementById(`temporary_${field}`);
        if (temporaryField) {
            temporaryField.value = permanentValue;
        }
    });

    // Show temporary address accordion
    const temporaryAccordion = document.getElementById("temporaryAddress");
    if (temporaryAccordion && !temporaryAccordion.classList.contains("show")) {
        const bsCollapse = new bootstrap.Collapse(temporaryAccordion, {
            show: true,
        });
    }
}

/**
 * Validate document numbers based on type
 */
function validateDocumentNumbers() {
    const documentTypes = document.querySelectorAll(".document-type-select");
    let isValid = true;

    documentTypes.forEach(function (select) {
        const documentType = select.value;
        const documentItem = select.closest(".identity-document-item");
        const documentNumber = documentItem.querySelector(
            'input[name*="document_number"]'
        ).value;

        if (documentType && documentNumber) {
            // Validate CCCD (12 digits)
            if (documentType === "cccd" && !/^\d{12}$/.test(documentNumber)) {
                isValid = false;
                alert("Căn cước công dân phải có đúng 12 số");
                return;
            }

            // Validate CMND (9 digits)
            if (documentType === "cmnd" && !/^\d{9}$/.test(documentNumber)) {
                isValid = false;
                alert("Chứng minh nhân dân phải có đúng 9 số");
                return;
            }
        }
    });

    return isValid;
}

/**
 * Initialize form functionality
 */
function initFormFunctionality() {
    document.addEventListener("DOMContentLoaded", function () {
        // Initialize section visibility
        toggleSections();

        // Initialize remove buttons visibility for edit form
        if (typeof updateRemoveButtons === "function") {
            updateRemoveButtons();
        }
    });

    // Form validation on submit
    const form = document.getElementById("litigantForm");
    if (form) {
        form.addEventListener("submit", function (e) {
            const type = document.getElementById("type").value;
            const fullName = document.getElementById("full_name").value;

            if (!type || !fullName) {
                e.preventDefault();
                alert("Vui lòng điền đầy đủ các trường bắt buộc.");
                return false;
            }

            // Validate document numbers
            if (!validateDocumentNumbers()) {
                e.preventDefault();
                return false;
            }
        });
    }
}

//litigant-search.js
function initSpouseSearch() {
    const searchInput = document.getElementById("spouse_search");
    const resultsContainer = document.getElementById("spouseSearchResults");
    const hiddenInput = document.getElementById("spouse_id");
    const clearButton = document.querySelector(".clear-selection");

    if (!searchInput) return;

    let selectedLitigantId = hiddenInput.value || null;

    // Nếu có giá trị từ edit form, mark as selected
    if (selectedLitigantId && searchInput.value) {
        searchInput.classList.add("selected-litigant");
        clearButton.classList.remove("d-none");
    }

    // Debounce function
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Search API call
    async function searchLitigants(query) {
        try {
            const response = await fetch(
                `/api/search-litigants?q=${encodeURIComponent(query)}`
            );
            const data = await response.json();
            return data;
        } catch (error) {
            console.error("Search error:", error);
            return [];
        }
    }

    // Display results
    function displayResults(results) {
        if (results.length === 0) {
            resultsContainer.innerHTML =
                '<div class="no-results">Không tìm thấy kết quả</div>';
            resultsContainer.style.display = "block";
            return;
        }

        const html = results
            .map((litigant, index) => {
                const documents =
                    litigant.identity_documents &&
                    litigant.identity_documents.length > 0
                        ? litigant.identity_documents
                              .map(
                                  (doc) =>
                                      `${doc.document_type.toUpperCase()}: ${
                                          doc.document_number
                                      }`
                              )
                              .join(", ")
                        : "Không có giấy tờ";

                return `
                <div class="search-result-item"
                     data-id="${litigant.id}"
                     data-index="${index}"
                     onclick="selectSpouse(${litigant.id}, '${litigant.full_name}')">
                    <div class="search-result-name">${litigant.full_name}</div>
                    <div class="search-result-type">Cá nhân</div>
                    <div class="search-result-documents">${documents}</div>
                </div>
            `;
            })
            .join("");

        resultsContainer.innerHTML = html;
        resultsContainer.style.display = "block";
    }

    // Select litigant
    window.selectSpouse = function (id, name) {
        selectedLitigantId = id;
        searchInput.value = name;
        searchInput.classList.add("selected-litigant");
        hiddenInput.value = id;
        clearButton.classList.remove("d-none");
        resultsContainer.style.display = "none";
    };

    // Clear selection
    window.clearSpouseSelection = function () {
        selectedLitigantId = null;
        searchInput.value = "";
        searchInput.classList.remove("selected-litigant");
        hiddenInput.value = "";
        clearButton.classList.add("d-none");
        resultsContainer.style.display = "none";
    };

    // Event listeners
    const debouncedSearch = debounce(async (query) => {
        if (selectedLitigantId) return;

        const results = await searchLitigants(query);
        displayResults(results);
    }, 300);

    searchInput.addEventListener("input", function (e) {
        const query = e.target.value.trim();

        if (query.length < 2) {
            resultsContainer.style.display = "none";
            return;
        }

        debouncedSearch(query);
    });

    // Hide results when click outside
    document.addEventListener("click", function (e) {
        if (!e.target.closest(".search-container")) {
            resultsContainer.style.display = "none";
        }
    });

    // Clear selection when start typing again
    searchInput.addEventListener("focus", function () {
        if (selectedLitigantId && this.value) {
            clearSpouseSelection();
        }
    });
}

function initRegistrationRepresentativeSearch() {
    const searchInput = document.getElementById(
        "registration_representative_search"
    );
    const resultsContainer = document.getElementById(
        "registrationRepresentativeSearchResults"
    );
    const hiddenInput = document.getElementById(
        "registration_representative_id"
    );
    const clearButton = searchInput
        ?.closest(".search-container")
        ?.querySelector(".clear-selection");

    if (!searchInput) return;

    let selectedLitigantId = hiddenInput.value || null;

    // Nếu có giá trị từ edit form, mark as selected
    if (selectedLitigantId && searchInput.value) {
        searchInput.classList.add("selected-litigant");
        if (clearButton) clearButton.classList.remove("d-none");
    }

    // Debounce function
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Search API call
    async function searchLitigants(query) {
        try {
            const response = await fetch(
                `/api/search-litigants?q=${encodeURIComponent(query)}`
            );
            const data = await response.json();
            return data;
        } catch (error) {
            console.error("Search error:", error);
            return [];
        }
    }

    // Display results
    function displayResults(results) {
        if (results.length === 0) {
            resultsContainer.innerHTML =
                '<div class="no-results">Không tìm thấy kết quả</div>';
            resultsContainer.style.display = "block";
            return;
        }

        const html = results
            .map((litigant, index) => {
                const documents =
                    litigant.identity_documents &&
                    litigant.identity_documents.length > 0
                        ? litigant.identity_documents
                              .map(
                                  (doc) =>
                                      `${doc.document_type.toUpperCase()}: ${
                                          doc.document_number
                                      }`
                              )
                              .join(", ")
                        : "Không có giấy tờ";

                const typeDisplay =
                    litigant.type === "individual"
                        ? "Cá nhân"
                        : litigant.type === "organization"
                        ? "Tổ chức"
                        : "Tổ chức tín dụng";

                return `
                <div class="search-result-item"
                     data-id="${litigant.id}"
                     data-index="${index}"
                     onclick="selectRegistrationRepresentative(${
                         litigant.id
                     }, '${litigant.full_name}')">
                    <div class="search-result-name">${litigant.full_name}</div>
                    <div class="search-result-type">${typeDisplay}</div>
                    ${
                        litigant.type === "individual"
                            ? `<div class="search-result-documents">${documents}</div>`
                            : ""
                    }
                </div>
            `;
            })
            .join("");

        resultsContainer.innerHTML = html;
        resultsContainer.style.display = "block";
    }

    // Select litigant
    window.selectRegistrationRepresentative = function (id, name) {
        selectedLitigantId = id;
        searchInput.value = name;
        searchInput.classList.add("selected-litigant");
        hiddenInput.value = id;
        if (clearButton) clearButton.classList.remove("d-none");
        resultsContainer.style.display = "none";
    };

    // Clear selection
    window.clearRegistrationRepresentativeSelection = function () {
        selectedLitigantId = null;
        searchInput.value = "";
        searchInput.classList.remove("selected-litigant");
        hiddenInput.value = "";
        if (clearButton) clearButton.classList.add("d-none");
        resultsContainer.style.display = "none";
    };

    // Event listeners
    const debouncedSearch = debounce(async (query) => {
        if (selectedLitigantId) return;

        const results = await searchLitigants(query);
        displayResults(results);
    }, 300);

    searchInput.addEventListener("input", function (e) {
        const query = e.target.value.trim();

        if (query.length < 2) {
            resultsContainer.style.display = "none";
            return;
        }

        debouncedSearch(query);
    });

    // Hide results when click outside
    document.addEventListener("click", function (e) {
        if (!e.target.closest(".search-container")) {
            resultsContainer.style.display = "none";
        }
    });

    // Clear selection when start typing again
    searchInput.addEventListener("focus", function () {
        if (selectedLitigantId && this.value) {
            clearRegistrationRepresentativeSelection();
        }
    });
}

function initCiRegistrationRepresentativeSearch() {
    const searchInput = document.getElementById(
        "ci_registration_representative_search"
    );
    const resultsContainer = document.getElementById(
        "ciRegistrationRepresentativeSearchResults"
    );
    const hiddenInput = document.getElementById(
        "ci_registration_representative_id"
    );
    const clearButton = searchInput
        ?.closest(".search-container")
        ?.querySelector(".clear-selection");

    if (!searchInput) return;

    let selectedLitigantId = hiddenInput.value || null;

    // Nếu có giá trị từ edit form, mark as selected
    if (selectedLitigantId && searchInput.value) {
        searchInput.classList.add("selected-litigant");
        if (clearButton) clearButton.classList.remove("d-none");
    }

    // Debounce function
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Search API call
    async function searchLitigants(query) {
        try {
            const response = await fetch(
                `/api/search-litigants?q=${encodeURIComponent(query)}`
            );
            const data = await response.json();
            return data;
        } catch (error) {
            console.error("Search error:", error);
            return [];
        }
    }

    // Display results
    function displayResults(results) {
        if (results.length === 0) {
            resultsContainer.innerHTML =
                '<div class="no-results">Không tìm thấy kết quả</div>';
            resultsContainer.style.display = "block";
            return;
        }

        const html = results
            .map((litigant, index) => {
                const documents =
                    litigant.identity_documents &&
                    litigant.identity_documents.length > 0
                        ? litigant.identity_documents
                              .map(
                                  (doc) =>
                                      `${doc.document_type.toUpperCase()}: ${
                                          doc.document_number
                                      }`
                              )
                              .join(", ")
                        : "Không có giấy tờ";

                const typeDisplay =
                    litigant.type === "individual"
                        ? "Cá nhân"
                        : litigant.type === "organization"
                        ? "Tổ chức"
                        : "Tổ chức tín dụng";

                return `
                <div class="search-result-item"
                     data-id="${litigant.id}"
                     data-index="${index}"
                     onclick="selectCiRegistrationRepresentative(${
                         litigant.id
                     }, '${litigant.full_name}')">
                    <div class="search-result-name">${litigant.full_name}</div>
                    <div class="search-result-type">${typeDisplay}</div>
                    ${
                        litigant.type === "individual"
                            ? `<div class="search-result-documents">${documents}</div>`
                            : ""
                    }
                </div>
            `;
            })
            .join("");

        resultsContainer.innerHTML = html;
        resultsContainer.style.display = "block";
    }

    // Select litigant
    window.selectCiRegistrationRepresentative = function (id, name) {
        selectedLitigantId = id;
        searchInput.value = name;
        searchInput.classList.add("selected-litigant");
        hiddenInput.value = id;
        if (clearButton) clearButton.classList.remove("d-none");
        resultsContainer.style.display = "none";
    };

    // Clear selection
    window.clearCiRegistrationRepresentativeSelection = function () {
        selectedLitigantId = null;
        searchInput.value = "";
        searchInput.classList.remove("selected-litigant");
        hiddenInput.value = "";
        if (clearButton) clearButton.classList.add("d-none");
        resultsContainer.style.display = "none";
    };

    // Event listeners
    const debouncedSearch = debounce(async (query) => {
        if (selectedLitigantId) return;

        const results = await searchLitigants(query);
        displayResults(results);
    }, 300);

    searchInput.addEventListener("input", function (e) {
        const query = e.target.value.trim();

        if (query.length < 2) {
            resultsContainer.style.display = "none";
            return;
        }

        debouncedSearch(query);
    });

    // Hide results when click outside
    document.addEventListener("click", function (e) {
        if (!e.target.closest(".search-container")) {
            resultsContainer.style.display = "none";
        }
    });

    // Clear selection when start typing again
    searchInput.addEventListener("focus", function () {
        if (selectedLitigantId && this.value) {
            clearCiRegistrationRepresentativeSelection();
        }
    });
}

// Initialize trong DOMContentLoaded
document.addEventListener("DOMContentLoaded", function () {
    initSpouseSearch();
    initRegistrationRepresentativeSearch();
    initCiRegistrationRepresentativeSearch(); // Thêm dòng này
});

// Initialize form functionality
initFormFunctionality();

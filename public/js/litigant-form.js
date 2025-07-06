// ================================================================

// File: public/js/litigant-form.js
// Functions for create and edit forms
// Functions cho create/edit forms

let documentCounter = 1;

/**
 * Toggle sections based on litigant type
 */
function toggleSections() {
    const type = document.getElementById('type').value;
    const individualSection = document.getElementById('individualSection');
    const organizationSection = document.getElementById('organizationSection');
    const creditInstitutionSection = document.getElementById('creditInstitutionSection');

    // Hide all sections
    individualSection.classList.add('d-none');
    organizationSection.classList.add('d-none');
    creditInstitutionSection.classList.add('d-none');

    // Show relevant section
    if (type === 'individual') {
        individualSection.classList.remove('d-none');
    } else if (type === 'organization') {
        organizationSection.classList.remove('d-none');
    } else if (type === 'credit_institution') {
        creditInstitutionSection.classList.remove('d-none');
    }
}

/**
 * Add new identity document
 */
function addDocument() {
    const container = document.getElementById('identityDocumentsContainer');
    const newDocument = document.createElement('div');
    newDocument.className = 'identity-document-item border rounded p-3 mb-3';
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
                        <input type="date" class="form-control"
                               name="identity_documents[${documentCounter}][issue_date]">
                        <label>Ngày cấp</label>
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
    documentCounter++;
    updateRemoveButtons();
}

/**
 * Remove identity document
 */
function removeDocument(button) {
    const documentItem = button.closest('.identity-document-item');
    documentItem.remove();
    updateRemoveButtons();
}

/**
 * Update visibility of remove buttons
 */
function updateRemoveButtons() {
    const documentItems = document.querySelectorAll('.identity-document-item');
    const removeButtons = document.querySelectorAll('.remove-document-btn');

    removeButtons.forEach((button, index) => {
        if (documentItems.length > 1) {
            button.classList.remove('d-none');
        } else {
            button.classList.add('d-none');
        }
    });
}

/**
 * Toggle student card specific fields
 */
function toggleDocumentFields(select, index) {
    const documentItem = select.closest('.identity-document-item');
    const studentCardFields = documentItem.querySelector('.student-card-fields');

    if (select.value === 'student_card') {
        studentCardFields.classList.remove('d-none');
    } else {
        studentCardFields.classList.add('d-none');
    }
}

/**
 * Validate form before submission
 */
function validateForm() {
    const type = document.getElementById('type').value;
    const fullName = document.getElementById('full_name').value;

    let errors = [];

    if (!fullName) {
        errors.push('Họ và tên là bắt buộc');
    }

    if (!type) {
        errors.push('Loại đương sự là bắt buộc');
    }

    if (errors.length > 0) {
        alert('Lỗi xác thực:\n' + errors.join('\n'));
        return false;
    }

    alert('Xác thực thành công!');
    return true;
}

/**
 * Copy permanent address to temporary address
 */
function copyPermanentAddress() {
    const permanentFields = ['street_address', 'province', 'district', 'ward'];

    permanentFields.forEach(field => {
        const permanentValue = document.getElementById(`permanent_${field}`).value;
        const temporaryField = document.getElementById(`temporary_${field}`);
        if (temporaryField) {
            temporaryField.value = permanentValue;
        }
    });

    // Show temporary address accordion
    const temporaryAccordion = document.getElementById('temporaryAddress');
    if (temporaryAccordion && !temporaryAccordion.classList.contains('show')) {
        const bsCollapse = new bootstrap.Collapse(temporaryAccordion, {
            show: true
        });
    }
}

/**
 * Validate document numbers based on type
 */
function validateDocumentNumbers() {
    const documentTypes = document.querySelectorAll('.document-type-select');
    let isValid = true;

    documentTypes.forEach(function(select) {
        const documentType = select.value;
        const documentItem = select.closest('.identity-document-item');
        const documentNumber = documentItem.querySelector('input[name*="document_number"]').value;

        if (documentType && documentNumber) {
            // Validate CCCD (12 digits)
            if (documentType === 'cccd' && !/^\d{12}$/.test(documentNumber)) {
                isValid = false;
                alert('Căn cước công dân phải có đúng 12 số');
                return;
            }

            // Validate CMND (9 digits)
            if (documentType === 'cmnd' && !/^\d{9}$/.test(documentNumber)) {
                isValid = false;
                alert('Chứng minh nhân dân phải có đúng 9 số');
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
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize section visibility
        toggleSections();

        // Initialize remove buttons visibility for edit form
        if (typeof updateRemoveButtons === 'function') {
            updateRemoveButtons();
        }
    });

    // Form validation on submit
    const form = document.getElementById('litigantForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const type = document.getElementById('type').value;
            const fullName = document.getElementById('full_name').value;

            if (!type || !fullName) {
                e.preventDefault();
                alert('Vui lòng điền đầy đủ các trường bắt buộc.');
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

// Initialize form functionality
initFormFunctionality();

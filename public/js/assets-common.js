// public/js/assets-common.js
// Common utilities and configurations for all asset pages

window.AssetManager = window.AssetManager || {};

// Common configurations
AssetManager.config = {
    routes: {
        getFields: "", // Will be set by each page
        bulkDelete: "", // Will be set by each page
        store: "", // Will be set by each page
        update: "", // Will be set by each page
        createCertificateType: "", // Will be set by each page
        createIssuingAuthority: "", // Will be set by each page
        searchCertificateTypes: "", // Will be set by each page
        searchIssuingAuthorities: "", // Will be set by each page
    },
    csrf: {
        token:
            document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute("content") ||
            document.querySelector('input[name="_token"]')?.value ||
            "",
    },
};

// Field templates for dynamic forms
AssetManager.fieldTemplates = {
    certificate: `
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Thông tin Giấy Chứng Nhận</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="certificate_type_id" class="form-label">Tên gọi GCNSQH tài sản</label>
                            <div class="search-dropdown">
                                <div class="input-group">
                                    <input type="text" class="form-control search-input" id="certificate_type_search"
                                           placeholder="Tìm kiếm tên gọi GCNSQH tài sản..." autocomplete="off">
                                    <button type="button" class="btn btn-outline-primary btn-create-item"
                                            onclick="AssetManager.search.showCreateCertificateTypeModal()">
                                        <i class="fas fa-plus me-2"></i>
                                    </button>
                                </div>
                                <input type="hidden" id="certificate_type_id" name="certificate_type_id">
                                <div class="search-results" id="certificate_type_results"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="certificate_issuing_authority_id" class="form-label">Nơi cấp</label>
                            <div class="search-dropdown">
                                <div class="input-group">
                                    <input type="text" class="form-control search-input" id="certificate_issuing_authority_search"
                                           placeholder="Tìm kiếm nơi cấp..." autocomplete="off">
                                    <button type="button" class="btn btn-outline-primary btn-create-item"
                                            onclick="AssetManager.search.showCreateIssuingAuthorityModal()">
                                        <i class="fas fa-plus me-2"></i>
                                    </button>
                                </div>
                                <input type="hidden" id="certificate_issuing_authority_id" name="certificate_issuing_authority_id">
                                <div class="search-results" id="certificate_issuing_authority_results"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="issue_number" class="form-label">Số phát hành</label>
                            <input type="text" class="form-control" id="issue_number" name="issue_number"
                                   placeholder="Nhập số phát hành">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="book_number" class="form-label">Số vào sổ</label>
                            <input type="text" class="form-control" id="book_number" name="book_number"
                                   placeholder="Nhập số vào sổ">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="issue_date" class="form-label">Ngày cấp</label>
                            <input type="date" class="form-control" id="issue_date" name="issue_date">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,

    landPlot: `
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Thông tin Thửa Đất</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="plot_number" class="form-label">Thửa đất số</label>
                            <input type="text" class="form-control" id="plot_number" name="plot_number"
                                   placeholder="Nhập số thửa">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="map_sheet_number" class="form-label">Tờ bản đồ số</label>
                            <input type="text" class="form-control" id="map_sheet_number" name="map_sheet_number"
                                   placeholder="Nhập tờ bản đồ">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="area" class="form-label">Diện tích (m²)</label>
                            <input type="number" class="form-control" id="area" name="area"
                                   placeholder="0" min="0" step="0.01">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="house_number" class="form-label">Số nhà</label>
                            <input type="text" class="form-control" id="house_number" name="house_number"
                                   placeholder="Nhập số nhà">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="street_name" class="form-label">Tên đường</label>
                            <input type="text" class="form-control" id="street_name" name="street_name"
                                placeholder="Nhập tên đường">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="province" class="form-label">Tỉnh/Thành</label>
                            <input type="text" class="form-control" id="province" name="province"
                            placeholder="Nhập tỉnh/thành">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="district" class="form-label">Quận/Huyện</label>
                            <input type="text" class="form-control" id="district" name="district"
                            placeholder="Nhập quận/huyện">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="ward" class="form-label">Phường/Xã</label>
                            <input type="text" class="form-control" id="ward" name="ward"
                            placeholder="Nhập phường xã">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="usage_form" class="form-label">Hình thức sử dụng</label>
                            <input type="text" class="form-control" id="usage_form" name="usage_form"
                                placeholder="Nhập hình thức sử dụng">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="usage_purpose" class="form-label">Mục đích sử dụng</label>
                            <input type="text" class="form-control" id="usage_purpose" name="usage_purpose"
                                placeholder="Nhập mục đích sử dụng">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="land_use_term" class="form-label">Thời hạn sử dụng</label>
                            <input type="date" class="form-control" id="land_use_term" name="land_use_term">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="usage_origin" class="form-label">Nguồn gốc sử dụng</label>
                            <input type="text" class="form-control" id="usage_origin" name="usage_origin"
                                placeholder="Nhập nguồn gốc sử dụng">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="land_notes" class="form-label">Ghi chú về đất</label>
                            <textarea class="form-control" id="land_notes" name="land_notes" rows="2"
                                    placeholder="Ghi chú về thửa đất..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,

    house: `
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Thông tin Nhà Ở</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="house_type" class="form-label">Loại nhà ở</label>
                            <input type="text" class="form-control" id="house_type" name="house_type"
                                placeholder="Nhập loại nhà">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="construction_area" class="form-label">Diện tích xây dựng (m²)</label>
                            <input type="number" class="form-control" id="construction_area" name="construction_area"
                                placeholder="0" min="0" step="0.01">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="floor_area" class="form-label">Diện tích sàn (m²)</label>
                            <input type="number" class="form-control" id="floor_area" name="floor_area"
                                placeholder="0" min="0" step="0.01">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="number_of_floors" class="form-label">Số tầng</label>
                            <input type="number" class="form-control" id="number_of_floors" name="number_of_floors"
                                placeholder="1" min="1" max="100">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="ownership_form" class="form-label">Hình thức sở hữu</label>
                            <input type="text" class="form-control" id="ownership_form" name="ownership_form"
                                placeholder="Nhập hình thức sở hữu">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="grade_level" class="form-label">Cấp (Hạng)</label>
                            <input type="text" class="form-control" id="grade_level" name="grade_level"
                                placeholder="Nhập cấp/hạng">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="ownership_term" class="form-label">Thời hạn sở hữu</label>
                            <input type="date" class="form-control" id="ownership_term" name="ownership_term">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="structure" class="form-label">Kết cấu</label>
                            <input type="text" class="form-control" id="structure" name="structure"
                                placeholder="Nhập kết cấu">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="house_notes" class="form-label">Ghi chú về nhà</label>
                            <textarea class="form-control" id="house_notes" name="house_notes" rows="2"
                                      placeholder="Ghi chú về nhà ở..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,

    apartment: `
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Thông tin Căn Hộ</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="apartment_number" class="form-label">Căn hộ số</label>
                            <input type="text" class="form-control" id="apartment_number" name="apartment_number"
                                   placeholder="Nhập số căn hộ">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="apartment_floor" class="form-label">Căn hộ thuộc tầng</label>
                            <input type="number" class="form-control" id="apartment_floor" name="apartment_floor"
                                   placeholder="1" min="1" max="200">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="building_floors" class="form-label">Số tầng nhà chung cư</label>
                            <input type="number" class="form-control" id="building_floors" name="building_floors"
                                   placeholder="1" min="1" max="200">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="apartment_construction_area" class="form-label">Diện tích xây dựng (m²)</label>
                            <input type="number" class="form-control" id="apartment_construction_area" name="apartment_construction_area"
                                   placeholder="0" min="0" step="0.01">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="apartment_floor_area" class="form-label">Diện tích sàn (m²)</label>
                            <input type="number" class="form-control" id="apartment_floor_area" name="apartment_floor_area"
                                   placeholder="0" min="0" step="0.01">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="apartment_ownership_form" class="form-label">Hình thức sở hữu</label>
                            <input type="text" class="form-control" id="apartment_ownership_form" name="apartment_ownership_form"
                                   placeholder="Nhập hình thức sở hữu">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="apartment_ownership_term" class="form-label">Thời hạn sở hữu</label>
                            <input type="date" class="form-control" id="apartment_ownership_term" name="apartment_ownership_term">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="apartment_structure" class="form-label">Kết cấu</label>
                            <input type="text" class="form-control" id="apartment_structure" name="apartment_structure"
                                   placeholder="Nhập kết cấu">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="apartment_notes" class="form-label">Ghi chú về căn hộ</label>
                            <textarea class="form-control" id="apartment_notes" name="apartment_notes" rows="2"
                                      placeholder="Ghi chú về căn hộ..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,

    vehicle: `
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Thông tin Phương Tiện</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="registration_number" class="form-label">Giấy đăng ký số</label>
                            <input type="text" class="form-control" id="registration_number" name="registration_number"
                                   placeholder="Nhập số đăng ký">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="license_plate" class="form-label">Biển kiểm soát</label>
                            <input type="text" class="form-control" id="license_plate" name="license_plate"
                                   placeholder="Nhập biển số">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="brand" class="form-label">Nhãn hiệu</label>
                            <input type="text" class="form-control" id="brand" name="brand"
                                   placeholder="Nhập nhãn hiệu">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="vehicle_type" class="form-label">Loại xe</label>
                            <input type="text" class="form-control" id="vehicle_type" name="vehicle_type"
                                   placeholder="Nhập loại xe">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="color" class="form-label">Màu sơn</label>
                            <input type="text" class="form-control" id="color" name="color"
                                   placeholder="Nhập màu sơn">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="engine_number" class="form-label">Số máy</label>
                            <input type="text" class="form-control" id="engine_number" name="engine_number"
                                   placeholder="Nhập số máy">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="chassis_number" class="form-label">Số khung</label>
                            <input type="text" class="form-control" id="chassis_number" name="chassis_number"
                                   placeholder="Nhập số khung">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="vehicle_issue_date" class="form-label">Ngày cấp</label>
                            <input type="date" class="form-control" id="vehicle_issue_date" name="vehicle_issue_date">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="issuing_authority_id" class="form-label">Nơi cấp</label>
                            <div class="search-dropdown">
                                <div class="input-group">
                                    <input type="text" class="form-control search-input" id="issuing_authority_search"
                                           placeholder="Tìm kiếm cơ quan cấp phát..." autocomplete="off">
                                    <button type="button" class="btn btn-outline-primary btn-create-item"
                                            onclick="AssetManager.search.showCreateIssuingAuthorityModal()">
                                        <i class="fas fa-plus me-2"></i>
                                    </button>
                                </div>
                                <input type="hidden" id="issuing_authority_id" name="issuing_authority_id">
                                <div class="search-results" id="issuing_authority_results"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="payload" class="form-label">Trọng tải (tấn)</label>
                            <input type="number" class="form-control" id="payload" name="payload"
                                   placeholder="0" min="0" step="0.01">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="engine_capacity" class="form-label">Dung tích (L)</label>
                            <input type="number" class="form-control" id="engine_capacity" name="engine_capacity"
                                   placeholder="0" min="0" step="0.01">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="seating_capacity" class="form-label">Số chỗ ngồi</label>
                            <input type="number" class="form-control" id="seating_capacity" name="seating_capacity"
                                   placeholder="0" min="1" max="100">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="type_number" class="form-label">Số loại</label>
                            <input type="text" class="form-control" id="type_number" name="type_number"
                                   placeholder="Nhập số loại">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="vehicle_notes" class="form-label">Ghi chú về phương tiện</label>
                            <textarea class="form-control" id="vehicle_notes" name="vehicle_notes" rows="2"
                                      placeholder="Ghi chú về phương tiện..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
};

// Common utility functions
AssetManager.utils = {
    // Show loading indicator
    showLoading: function (container, message = "Đang tải...") {
        const loadingDiv = document.createElement("div");
        loadingDiv.className = "text-center py-3";
        loadingDiv.innerHTML = `<div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted">${message}</p>`;
        loadingDiv.id = "loading-indicator";
        container.appendChild(loadingDiv);
    },

    // Remove loading indicator
    removeLoading: function () {
        const loadingIndicator = document.getElementById("loading-indicator");
        if (loadingIndicator) {
            loadingIndicator.remove();
        }
    },

    // Show error message
    showError: function (container, error, technicalDetails = "") {
        let errorMessage = "Có lỗi xảy ra.";

        if (error.message) {
            if (
                error.message.includes("NetworkError") ||
                error.message.includes("Failed to fetch")
            ) {
                errorMessage =
                    "Lỗi kết nối mạng. Vui lòng kiểm tra kết nối internet.";
            } else if (error.message.includes("404")) {
                errorMessage =
                    "Không tìm thấy endpoint. Vui lòng kiểm tra route.";
            } else if (error.message.includes("500")) {
                errorMessage = "Lỗi server. Vui lòng kiểm tra logs.";
            } else if (error.message.includes("JSON")) {
                errorMessage = "Server trả về dữ liệu không đúng định dạng.";
            }
            technicalDetails = error.message;
        }

        const errorDiv = document.createElement("div");
        errorDiv.className = "alert alert-danger";
        errorDiv.innerHTML = `
            <h6><i class="bi bi-exclamation-triangle me-2"></i>Có lỗi xảy ra</h6>
            <p class="mb-2"><strong>${errorMessage}</strong></p>
            ${
                technicalDetails
                    ? `
                <details>
                    <summary>Chi tiết kỹ thuật (click để xem)</summary>
                    <pre class="mt-2 small text-muted">${technicalDetails}</pre>
                </details>
            `
                    : ""
            }
        `;
        container.appendChild(errorDiv);
    },

    // Show success notification
    showSuccess: function (message, duration = 3000) {
        const indicator = document.createElement("div");
        indicator.className = "alert alert-success position-fixed";
        indicator.style.top = "20px";
        indicator.style.right = "20px";
        indicator.style.zIndex = "9999";
        indicator.style.minWidth = "300px";
        indicator.innerHTML = `<i class="bi bi-check-circle me-2"></i>${message}`;
        document.body.appendChild(indicator);
        setTimeout(() => indicator.remove(), duration);
    },

    // Make API request with proper headers
    apiRequest: function (url, options = {}) {
        const defaultOptions = {
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
                "X-Requested-With": "XMLHttpRequest",
            },
        };

        // Add CSRF token if available
        if (AssetManager.config.csrf.token) {
            defaultOptions.headers["X-CSRF-TOKEN"] =
                AssetManager.config.csrf.token;
        }

        // Merge options
        const mergedOptions = {
            ...defaultOptions,
            ...options,
            headers: {
                ...defaultOptions.headers,
                ...options.headers,
            },
        };

        return fetch(url, mergedOptions)
            .then((response) => {
                return response.text().then((text) => {
                    try {
                        const jsonData = JSON.parse(text);
                        return {
                            ok: response.ok,
                            status: response.status,
                            data: jsonData,
                        };
                    } catch (parseError) {
                        throw new Error(
                            `Invalid JSON response. Status: ${
                                response.status
                            }. Content: ${text.substring(0, 100)}...`
                        );
                    }
                });
            })
            .then((result) => {
                if (!result.ok) {
                    throw new Error(
                        `HTTP ${result.status}: ${
                            result.data.message || "Unknown error"
                        }`
                    );
                }
                if (result.data.error) {
                    throw new Error(result.data.message || result.data.error);
                }
                return result.data;
            });
    },

    // Find section by header text
    findSectionByText: function (container, text) {
        const headers = container.querySelectorAll(".card-header h5");
        for (let header of headers) {
            if (header.textContent.includes(text)) {
                return header.closest(".card");
            }
        }
        return null;
    },

    // Format number with thousand separators
    formatNumber: function (num) {
        return new Intl.NumberFormat("vi-VN").format(num);
    },

    // Debounce function
    debounce: function (func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },
};

// Initialize configuration when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
    // Update CSRF token if it wasn't available during initial load
    if (!AssetManager.config.csrf.token) {
        AssetManager.config.csrf.token =
            document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute("content") ||
            document.querySelector('input[name="_token"]')?.value ||
            "";
    }
});

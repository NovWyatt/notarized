// public/js/assets-search.js
// JavaScript for search functionality and creating new items

AssetManager.search = {
    // Initialize search functionality
    init: function() {
        this.initSearchHandlers();
        this.initCreateModals();
    },

    // Initialize search input handlers
    initSearchHandlers: function() {
        // Certificate type search
        this.initSearchInput('certificate_type_search', 'certificate_type_results', 'certificate_type_id', 'searchCertificateTypes');

        // Certificate issuing authority search
        this.initSearchInput('certificate_issuing_authority_search', 'certificate_issuing_authority_results', 'certificate_issuing_authority_id', 'searchIssuingAuthorities');

        // Vehicle issuing authority search
        this.initSearchInput('issuing_authority_search', 'issuing_authority_results', 'issuing_authority_id', 'searchIssuingAuthorities');
    },

    // Initialize search input for a specific field
    initSearchInput: function(searchInputId, resultsId, hiddenInputId, searchRoute) {
        const searchInput = document.getElementById(searchInputId);
        const resultsContainer = document.getElementById(resultsId);
        const hiddenInput = document.getElementById(hiddenInputId);

        if (!searchInput || !resultsContainer || !hiddenInput) return;

        let searchTimeout;
        let selectedIndex = -1;

        // Search on input
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.trim();

            clearTimeout(searchTimeout);

            if (query.length < 2) {
                this.hideResults(resultsContainer);
                hiddenInput.value = '';
                return;
            }

            searchTimeout = setTimeout(() => {
                this.performSearch(query, resultsContainer, hiddenInput, searchRoute);
            }, 300);
        });

        // Handle keyboard navigation
        searchInput.addEventListener('keydown', (e) => {
            const items = resultsContainer.querySelectorAll('.search-result-item');

            switch(e.key) {
                case 'ArrowDown':
                    e.preventDefault();
                    selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
                    this.updateSelection(items, selectedIndex);
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    selectedIndex = Math.max(selectedIndex - 1, -1);
                    this.updateSelection(items, selectedIndex);
                    break;
                case 'Enter':
                    e.preventDefault();
                    if (selectedIndex >= 0 && items[selectedIndex]) {
                        items[selectedIndex].click();
                    }
                    break;
                case 'Escape':
                    this.hideResults(resultsContainer);
                    selectedIndex = -1;
                    break;
            }
        });

        // Hide results when clicking outside
        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !resultsContainer.contains(e.target)) {
                this.hideResults(resultsContainer);
                selectedIndex = -1;
            }
        });

        // Clear selection when input loses focus
        searchInput.addEventListener('blur', () => {
            setTimeout(() => {
                selectedIndex = -1;
            }, 200);
        });
    },

    // Perform search request
    performSearch: function(query, resultsContainer, hiddenInput, searchRoute) {
        const url = `${AssetManager.config.routes[searchRoute]}?q=${encodeURIComponent(query)}`;

        AssetManager.utils.apiRequest(url, { method: 'GET' })
            .then(data => {
                this.displayResults(data, resultsContainer, hiddenInput);
            })
            .catch(error => {
                console.error('Search error:', error);
                this.showSearchError(resultsContainer);
            });
    },

    // Display search results
    displayResults: function(results, resultsContainer, hiddenInput) {
        // Handle both direct array and data.data format
        const items = Array.isArray(results) ? results : (results.data || []);

        if (!items || items.length === 0) {
            resultsContainer.innerHTML = '<div class="search-result-item text-muted">Không tìm thấy kết quả</div>';
            resultsContainer.style.display = 'block';
            return;
        }

        const html = items.map(item =>
            `<div class="search-result-item" data-id="${item.id}" data-name="${item.name}">
                <strong>${item.name}</strong>
                ${item.description ? `<br><small class="text-muted">${item.description}</small>` : ''}
            </div>`
        ).join('');

        resultsContainer.innerHTML = html;
        resultsContainer.style.display = 'block';

        // Add click handlers
        resultsContainer.querySelectorAll('.search-result-item[data-id]').forEach(item => {
            item.addEventListener('click', () => {
                // Find the search input more reliably
                const searchInput = this.findSearchInput(resultsContainer);

                if (searchInput && hiddenInput) {
                    searchInput.value = item.dataset.name;
                    hiddenInput.value = item.dataset.id;
                    this.hideResults(resultsContainer);
                } else {
                    console.error('Could not find search input or hidden input', {
                        searchInput: searchInput,
                        hiddenInput: hiddenInput,
                        resultsContainer: resultsContainer
                    });
                }
            });
        });
    },

    // Helper function to find the search input reliably
    findSearchInput: function(resultsContainer) {
        // Method 1: Look for sibling input-group
        const inputGroup = resultsContainer.previousElementSibling;
        if (inputGroup && inputGroup.classList.contains('input-group')) {
            const searchInput = inputGroup.querySelector('.search-input');
            if (searchInput) return searchInput;
        }

        // Method 2: Look in parent container
        const parentContainer = resultsContainer.parentElement;
        if (parentContainer) {
            const searchInput = parentContainer.querySelector('.search-input');
            if (searchInput) return searchInput;
        }

        // Method 3: Based on results container ID, find corresponding input
        const resultsId = resultsContainer.id;
        if (resultsId.includes('certificate_type')) {
            return document.getElementById('certificate_type_search');
        } else if (resultsId.includes('certificate_issuing_authority')) {
            return document.getElementById('certificate_issuing_authority_search');
        } else if (resultsId.includes('issuing_authority')) {
            return document.getElementById('issuing_authority_search');
        }

        // Method 4: Look for any search input in the same form group
        const formGroup = resultsContainer.closest('.mb-3') || resultsContainer.closest('.form-group');
        if (formGroup) {
            return formGroup.querySelector('.search-input');
        }

        return null;
    },

    // Update keyboard selection
    updateSelection: function(items, selectedIndex) {
        items.forEach((item, index) => {
            if (index === selectedIndex) {
                item.classList.add('selected');
            } else {
                item.classList.remove('selected');
            }
        });
    },

    // Hide search results
    hideResults: function(resultsContainer) {
        resultsContainer.style.display = 'none';
    },

    // Show search error
    showSearchError: function(resultsContainer) {
        resultsContainer.innerHTML = '<div class="search-result-item text-danger">Lỗi khi tìm kiếm</div>';
        resultsContainer.style.display = 'block';
    },

    // Initialize create modals
    initCreateModals: function() {
        this.initCreateCertificateTypeModal();
        this.initCreateIssuingAuthorityModal();
    },

    // Show create certificate type modal
    showCreateCertificateTypeModal: function() {
        const modal = new bootstrap.Modal(document.getElementById('createCertificateTypeModal'));
        modal.show();
    },

    // Show create issuing authority modal
    showCreateIssuingAuthorityModal: function() {
        const modal = new bootstrap.Modal(document.getElementById('createIssuingAuthorityModal'));
        modal.show();
    },

    // Initialize create certificate type modal
    initCreateCertificateTypeModal: function() {
        const form = document.getElementById('createCertificateTypeForm');
        const modal = document.getElementById('createCertificateTypeModal');

        if (!form || !modal) return;

        form.addEventListener('submit', (e) => {
            e.preventDefault();

            const formData = new FormData(form);
            const data = {
                name: formData.get('name'),
                description: formData.get('description')
            };

            this.createCertificateType(data, modal);
        });
    },

    // Initialize create issuing authority modal
    initCreateIssuingAuthorityModal: function() {
        const form = document.getElementById('createIssuingAuthorityForm');
        const modal = document.getElementById('createIssuingAuthorityModal');

        if (!form || !modal) return;

        form.addEventListener('submit', (e) => {
            e.preventDefault();

            const formData = new FormData(form);
            // FIX: Sử dụng 'address' thay vì 'description' để match với controller
            const data = {
                name: formData.get('name'),
                address: formData.get('description') // Field name trong form là 'description' nhưng server expect 'address'
            };

            this.createIssuingAuthority(data, modal);
        });
    },

    // Create new certificate type
    createCertificateType: function(data, modal) {
        const submitBtn = modal.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang tạo...';
        submitBtn.disabled = true;

        AssetManager.utils.apiRequest(AssetManager.config.routes.createCertificateType, {
            method: 'POST',
            body: JSON.stringify(data)
        })
        .then(response => {
            AssetManager.utils.showSuccess('Tạo loại chứng chỉ thành công!');

            // Update search input
            const searchInput = document.getElementById('certificate_type_search');
            const hiddenInput = document.getElementById('certificate_type_id');

            if (searchInput && hiddenInput) {
                searchInput.value = response.certificateType.name;
                hiddenInput.value = response.certificateType.id;
            }

            // Close modal and reset form
            bootstrap.Modal.getInstance(modal).hide();
            modal.querySelector('form').reset();
        })
        .catch(error => {
            console.error('Create certificate type error:', error);
            AssetManager.utils.showError(modal.querySelector('.modal-body'), error);
        })
        .finally(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    },

    // Create new issuing authority
    createIssuingAuthority: function(data, modal) {
        const submitBtn = modal.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang tạo...';
        submitBtn.disabled = true;

        AssetManager.utils.apiRequest(AssetManager.config.routes.createIssuingAuthority, {
            method: 'POST',
            body: JSON.stringify(data)
        })
        .then(response => {
            AssetManager.utils.showSuccess('Tạo cơ quan cấp phát thành công!');

            // Update both certificate and vehicle issuing authority inputs if they exist
            const certificateSearchInput = document.getElementById('certificate_issuing_authority_search');
            const certificateHiddenInput = document.getElementById('certificate_issuing_authority_id');
            const vehicleSearchInput = document.getElementById('issuing_authority_search');
            const vehicleHiddenInput = document.getElementById('issuing_authority_id');

            if (certificateSearchInput && certificateHiddenInput) {
                certificateSearchInput.value = response.issuingAuthority.name;
                certificateHiddenInput.value = response.issuingAuthority.id;
            }

            if (vehicleSearchInput && vehicleHiddenInput) {
                vehicleSearchInput.value = response.issuingAuthority.name;
                vehicleHiddenInput.value = response.issuingAuthority.id;
            }

            // Close modal and reset form
            bootstrap.Modal.getInstance(modal).hide();
            modal.querySelector('form').reset();
        })
        .catch(error => {
            console.error('Create issuing authority error:', error);
            AssetManager.utils.showError(modal.querySelector('.modal-body'), error);
        })
        .finally(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    }
};

// Initialize search functionality when dynamic fields are added
document.addEventListener('DOMContentLoaded', function() {
    // Observer to watch for dynamic content
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) { // Element node
                        // Check if this is a certificate or vehicle section
                        if (node.querySelector && (
                            node.querySelector('#certificate_type_search') ||
                            node.querySelector('#certificate_issuing_authority_search') ||
                            node.querySelector('#issuing_authority_search')
                        )) {
                            // Reinitialize search for new elements
                            setTimeout(() => {
                                AssetManager.search.initSearchHandlers();
                            }, 100);
                        }
                    }
                });
            }
        });
    });

    const dynamicFields = document.getElementById('dynamic-fields');
    if (dynamicFields) {
        observer.observe(dynamicFields, {
            childList: true,
            subtree: true
        });
    }

    // Initialize search for existing elements
    AssetManager.search.init();
});

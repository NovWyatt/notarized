// public/js/assets-index.js
// JavaScript specific to the assets index page

AssetManager.index = {
    // Initialize index page functionality
    init: function() {
        this.initSelectAllCheckboxes();
        this.initBulkDelete();
        this.initIndividualCheckboxes();
    },

    // Handle "select all" functionality
    initSelectAllCheckboxes: function() {
        const selectAllBtn = document.getElementById('select-all');
        const selectAllHeader = document.getElementById('select-all-header');

        if (selectAllBtn) {
            selectAllBtn.addEventListener('change', function() {
                AssetManager.index.toggleAllCheckboxes(this.checked);
                AssetManager.index.toggleBulkDelete();
            });
        }

        if (selectAllHeader) {
            selectAllHeader.addEventListener('change', function() {
                AssetManager.index.toggleAllCheckboxes(this.checked);
                AssetManager.index.toggleBulkDelete();
            });
        }
    },

    // Handle individual checkbox changes
    initIndividualCheckboxes: function() {
        document.querySelectorAll('.asset-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', AssetManager.index.toggleBulkDelete);
        });
    },

    // Toggle all checkboxes
    toggleAllCheckboxes: function(checked) {
        const checkboxes = document.querySelectorAll('.asset-checkbox');
        checkboxes.forEach(checkbox => checkbox.checked = checked);

        // Sync the other select-all checkbox
        const selectAllBtn = document.getElementById('select-all');
        const selectAllHeader = document.getElementById('select-all-header');

        if (selectAllBtn) selectAllBtn.checked = checked;
        if (selectAllHeader) selectAllHeader.checked = checked;
    },

    // Show/hide bulk delete button based on selection
    toggleBulkDelete: function() {
        const checkedBoxes = document.querySelectorAll('.asset-checkbox:checked');
        const bulkDeleteBtn = document.getElementById('bulk-delete');

        if (bulkDeleteBtn) {
            if (checkedBoxes.length > 0) {
                bulkDeleteBtn.style.display = 'block';
                bulkDeleteBtn.textContent = `Xóa đã chọn (${checkedBoxes.length})`;
            } else {
                bulkDeleteBtn.style.display = 'none';
            }
        }
    },

    // Initialize bulk delete functionality
    initBulkDelete: function() {
        const bulkDeleteBtn = document.getElementById('bulk-delete');

        if (bulkDeleteBtn) {
            bulkDeleteBtn.addEventListener('click', function() {
                AssetManager.index.performBulkDelete();
            });
        }
    },

    // Perform bulk delete operation
    performBulkDelete: function() {
        const checkedBoxes = document.querySelectorAll('.asset-checkbox:checked');

        if (checkedBoxes.length === 0) return;

        if (confirm(`Bạn có chắc chắn muốn xóa ${checkedBoxes.length} tài sản đã chọn?`)) {
            const assetIds = Array.from(checkedBoxes).map(cb => cb.value);

            // Show loading state
            const bulkDeleteBtn = document.getElementById('bulk-delete');
            const originalText = bulkDeleteBtn.innerHTML;
            bulkDeleteBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang xóa...';
            bulkDeleteBtn.disabled = true;

            AssetManager.utils.apiRequest(AssetManager.config.routes.bulkDelete, {
                method: 'POST',
                body: JSON.stringify({ asset_ids: assetIds })
            })
            .then(data => {
                if (data.success) {
                    AssetManager.utils.showSuccess(`Đã xóa thành công ${assetIds.length} tài sản`);
                    // Reload page after short delay
                    setTimeout(() => location.reload(), 1000);
                } else {
                    throw new Error(data.message || 'Có lỗi xảy ra khi xóa');
                }
            })
            .catch(error => {
                console.error('Bulk delete error:', error);
                alert('Có lỗi xảy ra: ' + error.message);

                // Restore button state
                bulkDeleteBtn.innerHTML = originalText;
                bulkDeleteBtn.disabled = false;
            });
        }
    }
};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    AssetManager.index.init();
});

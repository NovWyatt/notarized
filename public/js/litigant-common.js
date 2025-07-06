// File: public/js/litigant-common.js
// Common functions shared across all litigant views
// Functions dùng chung

/**
 * Auto-hide alerts after 5 seconds
 */
function initAlertAutoHide() {
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                try {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                } catch (e) {
                    // Alert already closed or doesn't exist
                }
            }, 5000);
        });
    });
}

/**
 * Show confirmation modal for delete action
 */
function confirmDelete(id) {
    const form = document.getElementById('deleteForm');
    if (form) {
        form.action = `/litigants/${id}`;
    }

    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// Initialize common functionality
initAlertAutoHide();

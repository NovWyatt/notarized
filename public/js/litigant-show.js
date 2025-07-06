// ================================================================

// File: public/js/litigant-show.js
// Functions specific to show view
// Functions cho show view

/**
 * Initialize show page functionality
 */
function initShowPage() {
    // Show confirmation modal for delete
    window.confirmDelete = function() {
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    };
}

// Initialize show page
document.addEventListener('DOMContentLoaded', initShowPage);

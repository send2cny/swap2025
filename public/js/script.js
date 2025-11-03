/**
 * MVC PHP CRUD Application JavaScript
 */

// DOM Ready
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });

    // Confirm delete actions
    const deleteLinks = document.querySelectorAll('a[href*="delete"]');
    deleteLinks.forEach(link => {
        if (!link.hasAttribute('onclick')) {
            link.addEventListener('click', function(e) {
                if (!confirm('Are you sure you want to delete this item?')) {
                    e.preventDefault();
                }
            });
        }
    });

    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = 'red';
                } else {
                    field.style.borderColor = '';
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields');
            }
        });
    });

    // Password confirmation validation
    const passwordFields = document.querySelectorAll('input[name="password"]');
    passwordFields.forEach(field => {
        const form = field.closest('form');
        const confirmField = form.querySelector('input[name="confirm_password"]');

        if (confirmField) {
            confirmField.addEventListener('blur', function() {
                if (field.value !== confirmField.value) {
                    confirmField.setCustomValidity('Passwords do not match');
                    confirmField.style.borderColor = 'red';
                } else {
                    confirmField.setCustomValidity('');
                    confirmField.style.borderColor = '';
                }
            });
        }
    });

    // Table row highlighting
    const tableRows = document.querySelectorAll('table tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f8fafc';
        });
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
    });

    // Active menu highlighting
    const currentUrl = window.location.href;
    const navLinks = document.querySelectorAll('.nav-menu a');
    navLinks.forEach(link => {
        if (link.href === currentUrl || currentUrl.includes(link.getAttribute('href'))) {
            link.style.color = '#2563eb';
            link.style.fontWeight = 'bold';
        }
    });

    // Search input auto-submit delay
    const searchInputs = document.querySelectorAll('input[name="search"]');
    searchInputs.forEach(input => {
        let timeout = null;
        input.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                // Auto-submit after 500ms of no typing
                // Uncomment to enable auto-submit
                // this.closest('form').submit();
            }, 500);
        });
    });

    // Modal functionality (if needed)
    window.openModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'block';
        }
    };

    window.closeModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
        }
    };

    // Close modal on outside click
    window.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal')) {
            e.target.style.display = 'none';
        }
    });

    // Toggle details (for audit logs)
    window.toggleDetails = function(id) {
        const details = document.getElementById('details-' + id);
        if (details) {
            details.style.display = details.style.display === 'none' ? 'block' : 'none';
        }
    };

    // Print functionality
    window.printPage = function() {
        window.print();
    };

    // Export table to CSV
    window.exportTableToCSV = function(tableId, filename) {
        const table = document.getElementById(tableId);
        if (!table) return;

        let csv = [];
        const rows = table.querySelectorAll('tr');

        rows.forEach(row => {
            const cols = row.querySelectorAll('td, th');
            const rowData = [];
            cols.forEach(col => {
                rowData.push('"' + col.innerText.replace(/"/g, '""') + '"');
            });
            csv.push(rowData.join(','));
        });

        const csvContent = csv.join('\n');
        const blob = new Blob([csvContent], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename || 'export.csv';
        a.click();
        window.URL.revokeObjectURL(url);
    };

    // Back to top button
    const backToTopBtn = document.createElement('button');
    backToTopBtn.innerHTML = '↑';
    backToTopBtn.className = 'back-to-top';
    backToTopBtn.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: #2563eb;
        color: white;
        border: none;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        cursor: pointer;
        display: none;
        z-index: 1000;
        font-size: 24px;
    `;

    document.body.appendChild(backToTopBtn);

    window.addEventListener('scroll', function() {
        if (window.scrollY > 300) {
            backToTopBtn.style.display = 'block';
        } else {
            backToTopBtn.style.display = 'none';
        }
    });

    backToTopBtn.addEventListener('click', function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // Loading indicator for forms
    const submitButtons = document.querySelectorAll('button[type="submit"]');
    submitButtons.forEach(button => {
        button.closest('form').addEventListener('submit', function() {
            button.disabled = true;
            button.innerHTML = 'Processing...';
        });
    });

    console.log('MVC CRUD Application initialized');
});

/**
 * Main JS - Task Management System
 */

$(document).ready(function() {
    // Handle Mobile/Collapsible Sidebar
    const sidebar = $('#sidebar');
    const body = $('body');
    const toggler = $('.sidebar-toggle, #sidebarToggle, #mobileSidebarToggle, #logoToggle');
    const closeBtn = $('#sidebarClose');

    // Tooltip management for Sidebar
    function updateSidebarTooltips() {
        const isCollapsed = body.hasClass('sidebar-collapsed');
        const tooltips = $('[data-bs-toggle="tooltip"]');
        
        // Safety check for Bootstrap tooltip plugin
        if (typeof $.fn.tooltip !== 'function') return;

        if (isCollapsed && $(window).width() > 992) {
            tooltips.tooltip('enable');
        } else if (!isCollapsed && $(window).width() > 992) {
            // Disable only sidebar tooltips in expanded mode
            sidebar.find('[data-bs-toggle="tooltip"]').tooltip('disable');
        }
    }

    // Toggle Sidebar Action
    if (toggler.length) {
        toggler.on('click', function() {
            // Immediately hide any active tooltips to prevent "sticking" during animation
            if (typeof $.fn.tooltip === 'function') {
                $('[data-bs-toggle="tooltip"]').tooltip('hide');
            }
            
            if ($(window).width() > 992) {
                body.toggleClass('sidebar-collapsed');
                localStorage.setItem('sidebarCollapsed', body.hasClass('sidebar-collapsed'));
                
                // Recalculate DataTables layout if it exists
                if (typeof table !== 'undefined') {
                    setTimeout(() => {
                        table.columns.adjust().draw();
                    }, 300); 
                }
                
                // Small delay to re-evaluate tooltips after animation starts/finishes
                setTimeout(updateSidebarTooltips, 300);
            } else {
                sidebar.toggleClass('active');
                if (sidebar.hasClass('active')) {
                    body.css('overflow', 'hidden');
                } else {
                    body.css('overflow', '');
                }
            }
        });
    }

    if (closeBtn.length) {
        closeBtn.on('click', function() {
            sidebar.removeClass('active');
            body.css('overflow', '');
        });
    }

    // Close sidebar when clicking outside on mobile
    $(document).on('click', function(e) {
        if ($(window).width() <= 992) {
            if (!sidebar.is(e.target) && sidebar.has(e.target).length === 0 && !toggler.is(e.target) && toggler.has(e.target).length === 0) {
                sidebar.removeClass('active');
                body.css('overflow', '');
            }
        }
    });

    // Initial tooltip setup
    updateSidebarTooltips();

    // DataTables Default Configuration
    if ($.fn.dataTable) {
        $.extend(true, $.fn.dataTable.defaults, {
            language: {
                search: "",
                searchPlaceholder: "Search...",
                lengthMenu: "_MENU_ per page",
            },
            pageLength: 10,
            responsive: true,
            dom: '<"d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-4 mb-4"f l>rt<"d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-4 mt-4"i p>',
        });
    }
});

/**
 * Global Form Handler for AJAX Submissions
 * @param {string} selector - Form selector
 * @param {function} callback - Success callback
 */
function handleFormSubmit(selector, callback) {
    const $form = $(selector);
    if (!$form.length) return;

    $form.on('submit', function(e) {
        e.preventDefault();
        
        const $submitBtn = $form.find('button[type="submit"]');
        const originalBtnHtml = $submitBtn.html();
        const noToast = $form.data('no-toast') === true;
        
        // Disable button and show loading state
        $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');

        $.ajax({
            url: $form.attr('action'),
            method: $form.attr('method') || 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    if (!noToast) {
                        toastr.success(response.message || 'Action completed successfully');
                    }
                    if (callback && typeof callback === 'function') {
                        callback(response);
                    }
                } else {
                    toastr.error(response.message || 'Something went wrong');
                    $submitBtn.prop('disabled', false).html(originalBtnHtml);
                }
            },
            error: function(xhr) {
                let message = 'An error occurred. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                toastr.error(message);
                $submitBtn.prop('disabled', false).html(originalBtnHtml);
            }
        });
    });
}

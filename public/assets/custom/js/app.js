/**
 * File Path: public/assets/custom/js/app.js
 *
 * Custom JavaScript for SIB-K
 * Additional functionality dan enhancements untuk aplikasi
 *
 * @package    SIB-K
 * @subpackage Assets
 * @category   JavaScript
 * @author     Development Team
 * @created    2025-01-01
 */

(function ($) {
  "use strict";

  // ========================================
  // CSRF Token Setup for AJAX
  // ========================================
  $.ajaxSetup({
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
  });

  // ========================================
  // Notification Functions
  // ========================================
  const Notification = {
    /**
     * Load unread notifications
     */
    loadUnread: function () {
      $.ajax({
        url: "/api/notifications/count",
        method: "POST",
        success: function (response) {
          if (response.count > 0) {
            $("#notification-badge").text(response.count).show();
          } else {
            $("#notification-badge").text("0").hide();
          }
        },
        error: function () {
          console.log("Failed to load notifications");
        },
      });
    },

    /**
     * Mark notification as read
     */
    markAsRead: function (notificationId) {
      $.ajax({
        url: "/notifications/mark-read/" + notificationId,
        method: "POST",
        success: function () {
          Notification.loadUnread();
        },
      });
    },

    /**
     * Mark all as read
     */
    markAllAsRead: function () {
      $.ajax({
        url: "/notifications/mark-all-read",
        method: "POST",
        success: function () {
          Notification.loadUnread();
          location.reload();
        },
      });
    },
  };

  // ========================================
  // Alert Functions
  // ========================================
  const Alert = {
    /**
     * Show success alert
     */
    success: function (message) {
      const html = `
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="mdi mdi-check-circle me-2"></i> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
      $("#alert-container").html(html);
      this.autoHide();
    },

    /**
     * Show error alert
     */
    error: function (message) {
      const html = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="mdi mdi-alert-circle me-2"></i> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
      $("#alert-container").html(html);
      this.autoHide();
    },

    /**
     * Show warning alert
     */
    warning: function (message) {
      const html = `
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="mdi mdi-alert me-2"></i> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
      $("#alert-container").html(html);
      this.autoHide();
    },

    /**
     * Auto hide alerts after 5 seconds
     */
    autoHide: function () {
      setTimeout(function () {
        $(".alert").fadeOut("slow", function () {
          $(this).remove();
        });
      }, 5000);
    },
  };

  // ========================================
  // Form Validation Enhancement
  // ========================================
  const FormValidation = {
    /**
     * Initialize form validation
     */
    init: function () {
      $('form[data-validate="true"]').on("submit", function (e) {
        let isValid = true;

        $(this)
          .find("[required]")
          .each(function () {
            if (!$(this).val()) {
              isValid = false;
              $(this).addClass("is-invalid");
            } else {
              $(this).removeClass("is-invalid");
            }
          });

        if (!isValid) {
          e.preventDefault();
          Alert.error("Mohon lengkapi semua field yang wajib diisi");
        }
      });

      // Remove error on input
      $("form [required]").on("input change", function () {
        if ($(this).val()) {
          $(this).removeClass("is-invalid");
        }
      });
    },
  };

  // ========================================
  // Loading Overlay
  // ========================================
  const Loading = {
    show: function () {
      if ($("#loading-overlay").length === 0) {
        $("body").append(`
                    <div id="loading-overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
                         background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; 
                         justify-content: center;">
                        <div class="spinner-border text-light" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `);
      }
    },
    hide: function () {
      $("#loading-overlay").remove();
    },
  };

  // ========================================
  // Confirm Delete
  // ========================================
  const ConfirmDelete = {
    init: function () {
      $(document).on("click", "[data-confirm-delete]", function (e) {
        e.preventDefault();
        const url = $(this).attr("href") || $(this).data("url");
        const message = $(this).data("message") || "Apakah Anda yakin ingin menghapus data ini?";

        if (confirm(message)) {
          if ($(this).is("form")) {
            $(this).submit();
          } else {
            window.location.href = url;
          }
        }
      });
    },
  };

  // ========================================
  // DataTable Enhancement
  // ========================================
  const DataTableHelper = {
    init: function (selector, options = {}) {
      const defaultOptions = {
        language: {
          url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json",
        },
        responsive: true,
        pageLength: 25,
        dom: "Bfrtip",
        buttons: ["copy", "csv", "excel", "pdf", "print"],
      };

      return $(selector).DataTable($.extend({}, defaultOptions, options));
    },
  };

  // ========================================
  // Print Helper
  // ========================================
  const PrintHelper = {
    print: function (selector) {
      const content = $(selector).html();
      const printWindow = window.open("", "", "height=600,width=800");

      printWindow.document.write("<html><head><title>Print</title>");
      printWindow.document.write('<link rel="stylesheet" href="/assets/css/bootstrap.min.css">');
      printWindow.document.write("</head><body>");
      printWindow.document.write(content);
      printWindow.document.write("</body></html>");

      printWindow.document.close();
      printWindow.print();
    },
  };

  // ========================================
  // Document Ready
  // ========================================
  $(document).ready(function () {
    // Load notifications on page load
    if ($("#notification-badge").length) {
      Notification.loadUnread();

      // Refresh every 60 seconds
      setInterval(function () {
        Notification.loadUnread();
      }, 60000);
    }

    // Initialize form validation
    FormValidation.init();

    // Initialize confirm delete
    ConfirmDelete.init();

    // Auto-hide alerts
    setTimeout(function () {
      $(".alert").fadeOut("slow", function () {
        $(this).remove();
      });
    }, 5000);

    // Tooltip initialization
    $('[data-bs-toggle="tooltip"]').tooltip();

    // Popover initialization
    $('[data-bs-toggle="popover"]').popover();

    // Print button
    $(document).on("click", "[data-print]", function (e) {
      e.preventDefault();
      const target = $(this).data("print");
      PrintHelper.print(target);
    });
  });

  // ========================================
  // Export to Global Scope
  // ========================================
  window.SIBK = {
    Notification: Notification,
    Alert: Alert,
    Loading: Loading,
    FormValidation: FormValidation,
    ConfirmDelete: ConfirmDelete,
    DataTableHelper: DataTableHelper,
    PrintHelper: PrintHelper,
  };
})(jQuery);

/**
 * Pledgen Admin JavaScript
 */

(function($) {
    'use strict';

    // Admin namespace
    window.PledgenAdmin = window.PledgenAdmin || {};

    PledgenAdmin.init = function() {
        this.bindEvents();
        this.initTooltips();
    };

    PledgenAdmin.bindEvents = function() {
        // Copy shortcode examples
        $(document).on('click', '.pledgen-example code', function() {
            const text = $(this).text();
            navigator.clipboard.writeText(text).then(function() {
                // Show success message
                const $code = $(this);
                const originalText = $code.text();
                $code.text('Copied!');
                setTimeout(function() {
                    $code.text(originalText);
                }, 2000);
            }.bind(this));
        });

        // Refresh stats
        $(document).on('click', '.pledgen-refresh-stats', function(e) {
            e.preventDefault();
            PledgenAdmin.refreshStats();
        });

        // Test API connection
        $(document).on('click', '.pledgen-test-api', function(e) {
            e.preventDefault();
            PledgenAdmin.testApiConnection();
        });
    };

    PledgenAdmin.initTooltips = function() {
        // Add tooltips to shortcode examples
        $('.pledgen-example code').attr('title', 'Click to copy');
    };

    PledgenAdmin.refreshStats = function() {
        const $button = $('.pledgen-refresh-stats');
        const originalText = $button.text();
        
        $button.text('Refreshing...').prop('disabled', true);
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'pledgen_refresh_stats',
                nonce: pledgen_admin.nonce
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Failed to refresh stats: ' + response.data);
                }
            },
            error: function() {
                alert('Failed to refresh stats. Please try again.');
            },
            complete: function() {
                $button.text(originalText).prop('disabled', false);
            }
        });
    };

    PledgenAdmin.testApiConnection = function() {
        const $button = $('.pledgen-test-api');
        const originalText = $button.text();
        
        $button.text('Testing...').prop('disabled', true);
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'pledgen_test_api',
                nonce: pledgen_admin.nonce
            },
            success: function(response) {
                if (response.success) {
                    alert('API connection successful!');
                } else {
                    alert('API connection failed: ' + response.data);
                }
            },
            error: function() {
                alert('Failed to test API connection. Please try again.');
            },
            complete: function() {
                $button.text(originalText).prop('disabled', false);
            }
        });
    };

    // Initialize when DOM is ready
    $(document).ready(function() {
        PledgenAdmin.init();
    });

})(jQuery); 
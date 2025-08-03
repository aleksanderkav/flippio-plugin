<?php
/**
 * Pledgen Plugin Test File
 * 
 * This file can be used to test basic plugin functionality
 * Remove this file before production use
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Test function to verify plugin is working
function pledgen_test_plugin() {
    echo '<div style="background: #f0f0f0; padding: 20px; margin: 20px; border: 1px solid #ccc;">';
    echo '<h3>Pledgen Plugin Test</h3>';
    
    // Test API connection
    $api_url = 'https://vercel-ny-front-flippio-plra8uev9-aleksanderkavs-projects.vercel.app/api/public/stats';
    $response = wp_remote_get($api_url, array(
        'timeout' => 10,
        'headers' => array('Accept' => 'application/json')
    ));
    
    if (is_wp_error($response)) {
        echo '<p style="color: red;">❌ API Connection Failed: ' . $response->get_error_message() . '</p>';
    } else {
        $status_code = wp_remote_retrieve_response_code($response);
        if ($status_code === 200) {
            echo '<p style="color: green;">✅ API Connection Successful (HTTP ' . $status_code . ')</p>';
            
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);
            
            if ($data && isset($data['success']) && $data['success']) {
                echo '<p>📊 Total Cards: ' . number_format($data['data']['totalCards']) . '</p>';
                echo '<p>💰 Total Value: $' . number_format($data['data']['totalValue'], 2) . '</p>';
            } else {
                echo '<p style="color: orange;">⚠️ API returned unexpected data format</p>';
            }
        } else {
            echo '<p style="color: red;">❌ API Connection Failed (HTTP ' . $status_code . ')</p>';
        }
    }
    
    // Test shortcode functionality
    echo '<h4>Shortcode Test:</h4>';
    echo '<p>Add this to any page/post to test:</p>';
    echo '<code style="background: #fff; padding: 10px; display: block; margin: 10px 0;">[pledgen_cards limit="6"]</code>';
    
    // Test plugin constants
    echo '<h4>Plugin Constants:</h4>';
    echo '<p>Version: ' . (defined('PLEDGEN_VERSION') ? PLEDGEN_VERSION : 'Not defined') . '</p>';
    echo '<p>API Base: ' . (defined('PLEDGEN_API_BASE') ? PLEDGEN_API_BASE : 'Not defined') . '</p>';
    
    echo '</div>';
}

// Add test to admin page
add_action('admin_notices', function() {
    if (isset($_GET['page']) && $_GET['page'] === 'pledgen' && isset($_GET['test'])) {
        pledgen_test_plugin();
    }
});

// Add test link to admin page
add_action('admin_footer', function() {
    if (isset($_GET['page']) && $_GET['page'] === 'pledgen') {
        echo '<script>
            jQuery(document).ready(function($) {
                $(".wrap h1").after("<a href=\"?page=pledgen&test=1\" class=\"button button-secondary\" style=\"margin-left: 10px;\">Run Test</a>");
            });
        </script>';
    }
}); 
<?php
/**
 * Admin Page Template
 */

// Check user capabilities
if (!current_user_can('manage_options')) {
    return;
}

// Get stats
$stats = $this->get_stats_from_api();
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="pledgen-admin-content">
        <!-- Stats Overview -->
        <div class="pledgen-admin-section">
            <h2><?php _e('Card Collection Overview', 'pledgen'); ?></h2>
            
            <?php if ($stats): ?>
                <div class="pledgen-stats-grid">
                    <div class="pledgen-stat-card">
                        <div class="pledgen-stat-number"><?php echo number_format($stats['totalCards']); ?></div>
                        <div class="pledgen-stat-label"><?php _e('Total Cards', 'pledgen'); ?></div>
                    </div>
                    
                    <div class="pledgen-stat-card">
                        <div class="pledgen-stat-number">$<?php echo number_format($stats['totalValue'], 2); ?></div>
                        <div class="pledgen-stat-label"><?php _e('Total Value', 'pledgen'); ?></div>
                    </div>
                    
                    <div class="pledgen-stat-card">
                        <div class="pledgen-stat-number">$<?php echo number_format($stats['averagePrice'], 2); ?></div>
                        <div class="pledgen-stat-label"><?php _e('Average Price', 'pledgen'); ?></div>
                    </div>
                    
                    <div class="pledgen-stat-card">
                        <div class="pledgen-stat-number"><?php echo number_format($stats['cardsWithPrices']); ?></div>
                        <div class="pledgen-stat-label"><?php _e('Cards with Prices', 'pledgen'); ?></div>
                    </div>
                </div>
                
                <?php if (!empty($stats['categories'])): ?>
                    <div class="pledgen-category-breakdown">
                        <h3><?php _e('Cards by Category', 'pledgen'); ?></h3>
                        <div class="pledgen-category-chart">
                            <?php foreach ($stats['categories'] as $category => $count): ?>
                                <div class="pledgen-category-item">
                                    <span class="pledgen-category-name"><?php echo esc_html($category); ?></span>
                                    <span class="pledgen-category-count"><?php echo number_format($count); ?></span>
                                    <div class="pledgen-category-bar">
                                        <div class="pledgen-category-fill" style="width: <?php echo ($count / $stats['totalCards']) * 100; ?>%"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="pledgen-last-updated">
                    <p><strong><?php _e('Last Updated:', 'pledgen'); ?></strong> <?php echo date('F j, Y g:i A', strtotime($stats['lastUpdated'])); ?></p>
                </div>
            <?php else: ?>
                <div class="pledgen-error">
                    <?php _e('Unable to fetch statistics from the API.', 'pledgen'); ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Shortcode Examples -->
        <div class="pledgen-admin-section">
            <h2><?php _e('Shortcode Examples', 'pledgen'); ?></h2>
            
            <div class="pledgen-shortcode-examples">
                <div class="pledgen-example">
                    <h4><?php _e('Display All Cards', 'pledgen'); ?></h4>
                    <code>[pledgen_cards]</code>
                    <p><?php _e('Shows all cards with default settings (20 cards per page, with filters and pagination).', 'pledgen'); ?></p>
                </div>
                
                <div class="pledgen-example">
                    <h4><?php _e('Display Pokemon Cards Only', 'pledgen'); ?></h4>
                    <code>[pledgen_cards category="Pokemon" limit="12"]</code>
                    <p><?php _e('Shows only Pokemon cards, limited to 12 cards per page.', 'pledgen'); ?></p>
                </div>
                
                <div class="pledgen-example">
                    <h4><?php _e('Display Cards by Price (High to Low)', 'pledgen'); ?></h4>
                    <code>[pledgen_cards sort_by="latest_price" sort_order="desc" limit="50"]</code>
                    <p><?php _e('Shows cards sorted by price in descending order, 50 cards per page.', 'pledgen'); ?></p>
                </div>
                
                <div class="pledgen-example">
                    <h4><?php _e('Display Single Card by Slug', 'pledgen'); ?></h4>
                    <code>[pledgen_card slug="charizard-psa-10"]</code>
                    <p><?php _e('Displays detailed information for a specific card.', 'pledgen'); ?></p>
                </div>
                
                <div class="pledgen-example">
                    <h4><?php _e('Display Single Card by ID', 'pledgen'); ?></h4>
                    <code>[pledgen_card id="card-uuid-here"]</code>
                    <p><?php _e('Displays detailed information for a specific card using its UUID.', 'pledgen'); ?></p>
                </div>
            </div>
        </div>
        
        <!-- Available Parameters -->
        <div class="pledgen-admin-section">
            <h2><?php _e('Available Parameters', 'pledgen'); ?></h2>
            
            <div class="pledgen-parameters">
                <h3><?php _e('pledgen_cards Parameters', 'pledgen'); ?></h3>
                <table class="widefat">
                    <thead>
                        <tr>
                            <th><?php _e('Parameter', 'pledgen'); ?></th>
                            <th><?php _e('Type', 'pledgen'); ?></th>
                            <th><?php _e('Default', 'pledgen'); ?></th>
                            <th><?php _e('Description', 'pledgen'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>limit</code></td>
                            <td>number</td>
                            <td>20</td>
                            <td><?php _e('Number of cards to display per page (max 100).', 'pledgen'); ?></td>
                        </tr>
                        <tr>
                            <td><code>category</code></td>
                            <td>string</td>
                            <td>''</td>
                            <td><?php _e('Filter by category (Pokemon, Sports, Gaming, Other).', 'pledgen'); ?></td>
                        </tr>
                        <tr>
                            <td><code>search</code></td>
                            <td>string</td>
                            <td>''</td>
                            <td><?php _e('Search term to filter cards by name.', 'pledgen'); ?></td>
                        </tr>
                        <tr>
                            <td><code>sort_by</code></td>
                            <td>string</td>
                            <td>created_at</td>
                            <td><?php _e('Sort field (created_at, latest_price, name, year).', 'pledgen'); ?></td>
                        </tr>
                        <tr>
                            <td><code>sort_order</code></td>
                            <td>string</td>
                            <td>desc</td>
                            <td><?php _e('Sort direction (asc, desc).', 'pledgen'); ?></td>
                        </tr>
                        <tr>
                            <td><code>show_filters</code></td>
                            <td>boolean</td>
                            <td>true</td>
                            <td><?php _e('Show/hide filter controls (true, false).', 'pledgen'); ?></td>
                        </tr>
                        <tr>
                            <td><code>show_pagination</code></td>
                            <td>boolean</td>
                            <td>true</td>
                            <td><?php _e('Show/hide pagination (true, false).', 'pledgen'); ?></td>
                        </tr>
                    </tbody>
                </table>
                
                <h3><?php _e('pledgen_card Parameters', 'pledgen'); ?></h3>
                <table class="widefat">
                    <thead>
                        <tr>
                            <th><?php _e('Parameter', 'pledgen'); ?></th>
                            <th><?php _e('Type', 'pledgen'); ?></th>
                            <th><?php _e('Default', 'pledgen'); ?></th>
                            <th><?php _e('Description', 'pledgen'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>slug</code></td>
                            <td>string</td>
                            <td>''</td>
                            <td><?php _e('Card slug (e.g., "charizard-psa-10").', 'pledgen'); ?></td>
                        </tr>
                        <tr>
                            <td><code>id</code></td>
                            <td>string</td>
                            <td>''</td>
                            <td><?php _e('Card UUID identifier.', 'pledgen'); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- API Status -->
        <div class="pledgen-admin-section">
            <h2><?php _e('API Status', 'pledgen'); ?></h2>
            
            <div class="pledgen-api-status">
                <p><strong><?php _e('API Base URL:', 'pledgen'); ?></strong> <code><?php echo esc_html(PLEDGEN_API_BASE); ?></code></p>
                
                <?php
                // Test API connection
                $test_response = wp_remote_get(PLEDGEN_API_BASE . '/stats', array(
                    'timeout' => 10,
                    'headers' => array('Accept' => 'application/json')
                ));
                
                if (is_wp_error($test_response)) {
                    echo '<div class="pledgen-error"><strong>' . __('API Status:', 'pledgen') . '</strong> ' . __('Connection failed', 'pledgen') . ' - ' . esc_html($test_response->get_error_message()) . '</div>';
                } else {
                    $status_code = wp_remote_retrieve_response_code($test_response);
                    $content_type = wp_remote_retrieve_header($test_response, 'content-type');
                    $body = wp_remote_retrieve_body($test_response);
                    
                    if ($status_code === 200) {
                        // Check if we got HTML instead of JSON
                        if (strpos($content_type, 'text/html') !== false || strpos($body, '<!doctype html>') !== false) {
                            echo '<div class="pledgen-error"><strong>' . __('API Status:', 'pledgen') . '</strong> ' . __('API endpoints returning HTML instead of JSON. The API routes may not be properly configured on Vercel.', 'pledgen') . '</div>';
                            echo '<div class="pledgen-error"><strong>' . __('Solution:', 'pledgen') . '</strong> ' . __('The Vercel deployment needs to be configured to handle API routes as serverless functions, not static files.', 'pledgen') . '</div>';
                        } else {
                            echo '<div class="pledgen-success"><strong>' . __('API Status:', 'pledgen') . '</strong> ' . __('Connected successfully', 'pledgen') . '</div>';
                        }
                    } else {
                        echo '<div class="pledgen-error"><strong>' . __('API Status:', 'pledgen') . '</strong> ' . __('Connection failed', 'pledgen') . ' - HTTP ' . $status_code . '</div>';
                        if ($status_code === 401) {
                            echo '<div class="pledgen-error"><strong>' . __('Authentication Issue:', 'pledgen') . '</strong> ' . __('The API is returning 401 Unauthorized. This may indicate the API requires authentication or the endpoints are not publicly accessible.', 'pledgen') . '</div>';
                        }
                    }
                }
                ?>
            </div>
        </div>
        
        <!-- Cache Management -->
        <div class="pledgen-admin-section">
            <h2><?php _e('Cache Management', 'pledgen'); ?></h2>
            
            <div class="pledgen-cache-controls">
                <p><?php _e('The plugin caches API responses for 1 hour to improve performance. You can clear the cache manually if needed.', 'pledgen'); ?></p>
                
                <form method="post" action="">
                    <?php wp_nonce_field('pledgen_clear_cache', 'pledgen_cache_nonce'); ?>
                    <input type="submit" name="pledgen_clear_cache" class="button button-secondary" value="<?php _e('Clear All Cache', 'pledgen'); ?>">
                </form>
                
                <?php
                if (isset($_POST['pledgen_clear_cache']) && wp_verify_nonce($_POST['pledgen_cache_nonce'], 'pledgen_clear_cache')) {
                    // Clear all Pledgen transients
                    global $wpdb;
                    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_pledgen_%'");
                    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_pledgen_%'");
                    
                    echo '<div class="pledgen-success">' . __('Cache cleared successfully!', 'pledgen') . '</div>';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<style>
.pledgen-admin-content {
    max-width: 1200px;
}

.pledgen-admin-section {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 20px;
    margin-bottom: 20px;
}

.pledgen-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.pledgen-stat-card {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
}

.pledgen-stat-number {
    font-size: 32px;
    font-weight: 700;
    color: #007cba;
    margin-bottom: 8px;
}

.pledgen-stat-label {
    font-size: 14px;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.pledgen-category-breakdown {
    margin-top: 30px;
}

.pledgen-category-chart {
    margin-top: 16px;
}

.pledgen-category-item {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
    padding: 8px 0;
}

.pledgen-category-name {
    min-width: 100px;
    font-weight: 500;
}

.pledgen-category-count {
    min-width: 60px;
    text-align: right;
    color: #6c757d;
}

.pledgen-category-bar {
    flex: 1;
    height: 8px;
    background: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
}

.pledgen-category-fill {
    height: 100%;
    background: #007cba;
    transition: width 0.3s ease;
}

.pledgen-last-updated {
    margin-top: 20px;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 4px;
    border-left: 4px solid #007cba;
}

.pledgen-shortcode-examples {
    display: grid;
    gap: 20px;
}

.pledgen-example {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 6px;
    padding: 16px;
}

.pledgen-example h4 {
    margin: 0 0 8px 0;
    color: #1a1a1a;
}

.pledgen-example code {
    display: block;
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    padding: 8px 12px;
    margin: 8px 0;
    font-family: 'Courier New', monospace;
    color: #e83e8c;
}

.pledgen-example p {
    margin: 8px 0 0 0;
    color: #6c757d;
    font-size: 14px;
}

.pledgen-parameters table {
    margin-top: 16px;
}

.pledgen-parameters th {
    font-weight: 600;
}

.pledgen-parameters code {
    background: #f8f9fa;
    padding: 2px 6px;
    border-radius: 3px;
    font-family: 'Courier New', monospace;
    color: #e83e8c;
}

.pledgen-api-status {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 6px;
    padding: 16px;
}

.pledgen-api-status code {
    background: #fff;
    padding: 4px 8px;
    border-radius: 3px;
    font-family: 'Courier New', monospace;
}

.pledgen-cache-controls {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 6px;
    padding: 16px;
}

.pledgen-success {
    background: #d4edda;
    color: #155724;
    padding: 12px 16px;
    border-radius: 4px;
    border: 1px solid #c3e6cb;
    margin: 16px 0;
}

.pledgen-error {
    background: #f8d7da;
    color: #721c24;
    padding: 12px 16px;
    border-radius: 4px;
    border: 1px solid #f5c6cb;
    margin: 16px 0;
}

@media (max-width: 768px) {
    .pledgen-stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 16px;
    }
    
    .pledgen-stat-number {
        font-size: 24px;
    }
    
    .pledgen-category-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    
    .pledgen-category-name,
    .pledgen-category-count {
        min-width: auto;
    }
    
    .pledgen-category-bar {
        width: 100%;
    }
}
</style> 
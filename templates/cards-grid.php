<?php
/**
 * Cards Grid Template
 * 
 * @var array $cards_data API response data
 * @var array $atts Shortcode attributes
 */

// Ensure we have data
if (!$cards_data || !isset($cards_data['data'])) {
    echo '<div class="pledgen-error">' . __('No cards data available.', 'pledgen') . '</div>';
    return;
}

$cards = $cards_data['data'];
$pagination = $cards_data['pagination'] ?? null;
?>

<div class="pledgen-container">
    <?php if ($atts['show_filters'] === 'true'): ?>
    <div class="pledgen-filters">
        <h3><?php _e('Filter Cards', 'pledgen'); ?></h3>
        <form>
            <div class="pledgen-filters-grid">
                <div class="pledgen-filter-group pledgen-search">
                    <label for="pledgen-search"><?php _e('Search Cards', 'pledgen'); ?></label>
                    <input type="text" id="pledgen-search" name="search" placeholder="<?php _e('Search by card name...', 'pledgen'); ?>" value="<?php echo esc_attr($atts['search'] ?? ''); ?>">
                </div>
                
                <div class="pledgen-filter-group">
                    <label for="pledgen-category"><?php _e('Category', 'pledgen'); ?></label>
                    <select id="pledgen-category" name="category">
                        <option value=""><?php _e('All Categories', 'pledgen'); ?></option>
                        <option value="Pokemon" <?php selected($atts['category'] ?? '', 'Pokemon'); ?>><?php _e('Pokemon', 'pledgen'); ?></option>
                        <option value="Sports" <?php selected($atts['category'] ?? '', 'Sports'); ?>><?php _e('Sports', 'pledgen'); ?></option>
                        <option value="Gaming" <?php selected($atts['category'] ?? '', 'Gaming'); ?>><?php _e('Gaming', 'pledgen'); ?></option>
                        <option value="Other" <?php selected($atts['category'] ?? '', 'Other'); ?>><?php _e('Other', 'pledgen'); ?></option>
                    </select>
                </div>
                
                <div class="pledgen-filter-group">
                    <label for="pledgen-sort-by"><?php _e('Sort By', 'pledgen'); ?></label>
                    <select id="pledgen-sort-by" name="sort_by">
                        <option value="created_at" <?php selected($atts['sort_by'] ?? '', 'created_at'); ?>><?php _e('Date Added', 'pledgen'); ?></option>
                        <option value="latest_price" <?php selected($atts['sort_by'] ?? '', 'latest_price'); ?>><?php _e('Price', 'pledgen'); ?></option>
                        <option value="name" <?php selected($atts['sort_by'] ?? '', 'name'); ?>><?php _e('Name', 'pledgen'); ?></option>
                        <option value="year" <?php selected($atts['sort_by'] ?? '', 'year'); ?>><?php _e('Year', 'pledgen'); ?></option>
                    </select>
                </div>
                
                <div class="pledgen-filter-group">
                    <label for="pledgen-sort-order"><?php _e('Sort Order', 'pledgen'); ?></label>
                    <select id="pledgen-sort-order" name="sort_order">
                        <option value="desc" <?php selected($atts['sort_order'] ?? '', 'desc'); ?>><?php _e('Descending', 'pledgen'); ?></option>
                        <option value="asc" <?php selected($atts['sort_order'] ?? '', 'asc'); ?>><?php _e('Ascending', 'pledgen'); ?></option>
                    </select>
                </div>
                
                <div class="pledgen-filter-group">
                    <label for="pledgen-limit"><?php _e('Cards per Page', 'pledgen'); ?></label>
                    <select id="pledgen-limit" name="limit">
                        <option value="12" <?php selected($atts['limit'] ?? '', 12); ?>><?php _e('12 Cards', 'pledgen'); ?></option>
                        <option value="20" <?php selected($atts['limit'] ?? '', 20); ?>><?php _e('20 Cards', 'pledgen'); ?></option>
                        <option value="50" <?php selected($atts['limit'] ?? '', 50); ?>><?php _e('50 Cards', 'pledgen'); ?></option>
                        <option value="100" <?php selected($atts['limit'] ?? '', 100); ?>><?php _e('100 Cards', 'pledgen'); ?></option>
                    </select>
                </div>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <div class="pledgen-cards-grid">
        <?php if (empty($cards)): ?>
            <div class="pledgen-error">
                <?php _e('No cards found matching your criteria.', 'pledgen'); ?>
            </div>
        <?php else: ?>
            <?php foreach ($cards as $card): ?>
                <div class="pledgen-card" data-card-id="<?php echo esc_attr($card['id']); ?>" data-card-slug="<?php echo esc_attr(strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $card['name']))); ?>">
                    <!-- Card Image - Temporarily Hidden -->
                    <!--
                    <div class="pledgen-card-image">
                        <?php if (!empty($card['image_url'])): ?>
                            <img src="<?php echo esc_url($card['image_url']); ?>" alt="<?php echo esc_attr($card['name']); ?>" loading="lazy">
                        <?php else: ?>
                            <div class="placeholder">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                                </svg>
                                <span><?php _e('No Image', 'pledgen'); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    -->
                    
                    <div class="pledgen-card-content">
                        <h3 class="pledgen-card-title"><?php echo esc_html($card['name']); ?></h3>
                        
                        <div class="pledgen-card-meta">
                            <?php if (!empty($card['category'])): ?>
                                <span><?php echo esc_html($card['category']); ?></span>
                            <?php endif; ?>
                            
                            <?php if (!empty($card['year'])): ?>
                                <span><?php echo esc_html($card['year']); ?></span>
                            <?php endif; ?>
                            
                            <?php if (!empty($card['grading'])): ?>
                                <span><?php echo esc_html($card['grading']); ?></span>
                            <?php endif; ?>
                            
                            <?php if (!empty($card['set_name'])): ?>
                                <span><?php echo esc_html($card['set_name']); ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="pledgen-card-price <?php echo empty($card['latest_price']) ? 'no-price' : ''; ?>">
                            <?php if (!empty($card['latest_price'])): ?>
                                $<?php echo number_format($card['latest_price'], 2); ?>
                            <?php else: ?>
                                <span class="no-price"><?php _e('No price data', 'pledgen'); ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="pledgen-card-actions">
                            <button class="pledgen-btn pledgen-btn-primary"><?php _e('View Details', 'pledgen'); ?></button>
                            <button class="pledgen-btn pledgen-btn-secondary"><?php _e('Track Price', 'pledgen'); ?></button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if ($atts['show_pagination'] === 'true' && $pagination): ?>
        <div class="pledgen-pagination">
            <?php
            $current_page = 1;
            $total_pages = ceil($pagination['total'] / $pagination['limit']);
            
            if ($total_pages > 1):
                // Previous button
                $prev_disabled = $current_page <= 1 ? 'disabled' : '';
                echo '<button class="pledgen-btn pledgen-btn-secondary ' . $prev_disabled . '" data-page="' . ($current_page - 1) . '">‹</button>';
                
                // Page numbers
                $start_page = max(1, $current_page - 2);
                $end_page = min($total_pages, $current_page + 2);
                
                if ($start_page > 1) {
                    echo '<button class="pledgen-btn pledgen-btn-secondary" data-page="1">1</button>';
                    if ($start_page > 2) {
                        echo '<span class="pledgen-pagination-ellipsis">...</span>';
                    }
                }
                
                for ($i = $start_page; $i <= $end_page; $i++) {
                    $current_class = $i === $current_page ? 'current' : '';
                    echo '<button class="pledgen-btn pledgen-btn-secondary ' . $current_class . '" data-page="' . $i . '">' . $i . '</button>';
                }
                
                if ($end_page < $total_pages) {
                    if ($end_page < $total_pages - 1) {
                        echo '<span class="pledgen-pagination-ellipsis">...</span>';
                    }
                    echo '<button class="pledgen-btn pledgen-btn-secondary" data-page="' . $total_pages . '">' . $total_pages . '</button>';
                }
                
                // Next button
                $next_disabled = $current_page >= $total_pages ? 'disabled' : '';
                echo '<button class="pledgen-btn pledgen-btn-secondary ' . $next_disabled . '" data-page="' . ($current_page + 1) . '">›</button>';
            endif;
            ?>
        </div>
    <?php endif; ?>
</div> 
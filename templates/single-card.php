<?php
/**
 * Single Card Template
 * 
 * @var array $card_data API response data
 */

// Ensure we have data
if (!$card_data || !isset($card_data['data'])) {
    echo '<div class="pledgen-error">' . __('Card data not available.', 'pledgen') . '</div>';
    return;
}

$card = $card_data['data'];
$price_history = $card['price_history'] ?? [];
?>

<div class="pledgen-container">
    <div class="pledgen-single-card">
        <div class="pledgen-single-card-header">
            <!-- Card Image - Temporarily Hidden -->
            <!--
            <div class="pledgen-single-card-image">
                <?php if (!empty($card['image_url'])): ?>
                    <img src="<?php echo esc_url($card['image_url']); ?>" alt="<?php echo esc_attr($card['name']); ?>" loading="lazy">
                <?php else: ?>
                    <div class="placeholder">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                        </svg>
                        <span><?php _e('No Image Available', 'pledgen'); ?></span>
                    </div>
                <?php endif; ?>
            </div>
            -->
            
            <div class="pledgen-single-card-info">
                <h1><?php echo esc_html($card['name']); ?></h1>
                
                <div class="pledgen-single-card-meta">
                    <?php if (!empty($card['category'])): ?>
                        <div class="meta-item">
                            <div class="meta-label"><?php _e('Category', 'pledgen'); ?></div>
                            <div class="meta-value"><?php echo esc_html($card['category']); ?></div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($card['year'])): ?>
                        <div class="meta-item">
                            <div class="meta-label"><?php _e('Year', 'pledgen'); ?></div>
                            <div class="meta-value"><?php echo esc_html($card['year']); ?></div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($card['grading'])): ?>
                        <div class="meta-item">
                            <div class="meta-label"><?php _e('Grade', 'pledgen'); ?></div>
                            <div class="meta-value"><?php echo esc_html($card['grading']); ?></div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($card['set_name'])): ?>
                        <div class="meta-item">
                            <div class="meta-label"><?php _e('Set', 'pledgen'); ?></div>
                            <div class="meta-value"><?php echo esc_html($card['set_name']); ?></div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($card['rarity'])): ?>
                        <div class="meta-item">
                            <div class="meta-label"><?php _e('Rarity', 'pledgen'); ?></div>
                            <div class="meta-value"><?php echo esc_html($card['rarity']); ?></div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($card['card_type'])): ?>
                        <div class="meta-item">
                            <div class="meta-label"><?php _e('Type', 'pledgen'); ?></div>
                            <div class="meta-value"><?php echo esc_html($card['card_type']); ?></div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($card['serial_number'])): ?>
                        <div class="meta-item">
                            <div class="meta-label"><?php _e('Serial Number', 'pledgen'); ?></div>
                            <div class="meta-value"><?php echo esc_html($card['serial_number']); ?></div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="pledgen-single-card-price <?php echo empty($card['latest_price']) ? 'no-price' : ''; ?>">
                    <?php if (!empty($card['latest_price'])): ?>
                        $<?php echo number_format($card['latest_price'], 2); ?>
                    <?php else: ?>
                        <span class="no-price"><?php _e('No price data available', 'pledgen'); ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="pledgen-single-card-actions">
                    <?php 
                    // Generate affiliate links
                    $search_term = urlencode($card['name']);
                    $ebay_url = "https://www.ebay.com/sch/i.html?_nkw={$search_term}";
                    $tcgplayer_url = "https://www.tcgplayer.com/search/all/product?q={$search_term}";
                    ?>
                    
                    <a href="<?php echo esc_url($ebay_url); ?>" class="pledgen-btn pledgen-btn-primary" target="_blank" rel="noopener">
                        <?php _e('Buy on eBay', 'pledgen'); ?>
                    </a>
                    
                    <a href="<?php echo esc_url($tcgplayer_url); ?>" class="pledgen-btn pledgen-btn-secondary" target="_blank" rel="noopener">
                        <?php _e('View on TCGPlayer', 'pledgen'); ?>
                    </a>
                    
                    <button class="pledgen-btn pledgen-btn-secondary" onclick="Pledgen.App.trackCard('<?php echo esc_js($card['id']); ?>')">
                        <?php _e('Track Price', 'pledgen'); ?>
                    </button>
                </div>
            </div>
        </div>
        
        <?php if (!empty($price_history)): ?>
        <div class="pledgen-price-chart">
            <h3><?php _e('Price History', 'pledgen'); ?></h3>
            <div class="pledgen-chart-container">
                <?php if (count($price_history) >= 2): ?>
                    <div style="position: relative; height: 200px;">
                        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; display: flex; align-items: end; padding: 20px; gap: 2px;">
                            <?php
                            $prices = array_column($price_history, 'price');
                            $min_price = min($prices);
                            $max_price = max($prices);
                            $range = $max_price - $min_price;
                            
                            foreach ($price_history as $index => $entry):
                                $height = $range > 0 ? (($entry['price'] - $min_price) / $range) * 100 : 50;
                                $color = $index === 0 ? '#28a745' : '#007cba';
                                $date = date('M j, Y', strtotime($entry['timestamp']));
                                ?>
                                <div style="
                                    flex: 1;
                                    height: <?php echo $height; ?>%;
                                    background: <?php echo $color; ?>;
                                    border-radius: 2px 2px 0 0;
                                    position: relative;
                                    min-height: 4px;
                                " title="$<?php echo number_format($entry['price'], 2); ?> - <?php echo $date; ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div style="margin-top: 16px; font-size: 14px; color: #6c757d;">
                        <strong><?php _e('Price History Summary:', 'pledgen'); ?></strong><br>
                        <?php
                        $first_price = $price_history[count($price_history) - 1]['price'];
                        $latest_price = $price_history[0]['price'];
                        $price_change = $latest_price - $first_price;
                        $price_change_percent = $first_price > 0 ? ($price_change / $first_price) * 100 : 0;
                        $change_color = $price_change >= 0 ? '#28a745' : '#dc3545';
                        $change_icon = $price_change >= 0 ? '↗' : '↘';
                        ?>
                        <span style="color: <?php echo $change_color; ?>;">
                            <?php echo $change_icon; ?> 
                            $<?php echo number_format(abs($price_change), 2); ?> 
                            (<?php echo number_format(abs($price_change_percent), 1); ?>%)
                        </span>
                        <?php _e('since', 'pledgen'); ?> 
                        <?php echo date('M j, Y', strtotime($price_history[count($price_history) - 1]['timestamp'])); ?>
                    </div>
                <?php else: ?>
                    <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #6c757d;">
                        <?php _e('Insufficient data for chart', 'pledgen'); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($card['category'])): ?>
        <div class="pledgen-similar-cards">
            <h3><?php _e('Similar Cards', 'pledgen'); ?></h3>
            <div class="pledgen-similar-grid" id="pledgen-similar-cards">
                <div class="pledgen-loading"><?php _e('Loading similar cards...', 'pledgen'); ?></div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Load similar cards when the page loads
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Pledgen !== 'undefined' && Pledgen.App) {
        // Load similar cards from the same category
        const category = '<?php echo esc_js($card['category'] ?? ''); ?>';
        if (category) {
            Pledgen.App.loadSimilarCards(category, '<?php echo esc_js($card['id']); ?>');
        }
    }
});

// Add tracking functionality
if (typeof Pledgen !== 'undefined') {
    Pledgen.App.trackCard = function(cardId) {
        // In a real implementation, this would add the card to a user's watchlist
        alert('<?php _e('Card tracking feature coming soon!', 'pledgen'); ?>');
    };
    
    Pledgen.App.loadSimilarCards = function(category, excludeCardId) {
        const container = document.getElementById('pledgen-similar-cards');
        if (!container) return;
        
        // Make AJAX request to get similar cards
        jQuery.ajax({
            url: Pledgen.config.ajaxUrl,
            type: 'POST',
            data: {
                action: 'pledgen_get_cards',
                nonce: Pledgen.config.nonce,
                category: category,
                limit: 4,
                exclude: excludeCardId
            },
            success: function(response) {
                if (response.success && response.data.data.length > 0) {
                    let html = '';
                    response.data.data.forEach(function(card) {
                        html += Pledgen.Templates.card(card);
                    });
                    container.innerHTML = html;
                } else {
                    container.innerHTML = '<div class="pledgen-error"><?php _e('No similar cards found.', 'pledgen'); ?></div>';
                }
            },
            error: function() {
                container.innerHTML = '<div class="pledgen-error"><?php _e('Failed to load similar cards.', 'pledgen'); ?></div>';
            }
        });
    };
}
</script> 
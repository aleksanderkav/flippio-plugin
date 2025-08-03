/**
 * Pledgen Card Tracker JavaScript
 */

(function($) {
    'use strict';

    // Pledgen namespace
    window.Pledgen = window.Pledgen || {};

    // Configuration
    Pledgen.config = {
        ajaxUrl: pledgen_ajax.ajax_url,
        nonce: pledgen_ajax.nonce,
        apiBase: pledgen_ajax.api_base
    };

    // Main Pledgen class
    Pledgen.App = {
        init: function() {
            this.bindEvents();
            this.initFilters();
            this.initPagination();
        },

        bindEvents: function() {
            // Filter form submission
            $(document).on('submit', '.pledgen-filters form', function(e) {
                e.preventDefault();
                Pledgen.App.loadCards();
            });

            // Search input with debounce
            let searchTimeout;
            $(document).on('input', '.pledgen-search input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    Pledgen.App.loadCards();
                }, 500);
            });

            // Filter change events
            $(document).on('change', '.pledgen-filters select', function() {
                Pledgen.App.loadCards();
            });

            // Pagination clicks
            $(document).on('click', '.pledgen-pagination .pledgen-btn', function(e) {
                e.preventDefault();
                if (!$(this).hasClass('disabled') && !$(this).hasClass('current')) {
                    const page = $(this).data('page');
                    Pledgen.App.loadCards(page);
                }
            });

            // Card click events
            $(document).on('click', '.pledgen-card', function() {
                const cardId = $(this).data('card-id');
                const cardSlug = $(this).data('card-slug');
                if (cardId || cardSlug) {
                    Pledgen.App.showCardModal(cardId || cardSlug);
                }
            });

            // Modal close
            $(document).on('click', '.pledgen-modal-overlay, .pledgen-modal-close', function() {
                Pledgen.App.closeModal();
            });

            // ESC key to close modal
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape') {
                    Pledgen.App.closeModal();
                }
            });
        },

        initFilters: function() {
            // Load available filters from API
            this.loadAvailableFilters();
        },

        initPagination: function() {
            // Initialize pagination if present
            if ($('.pledgen-pagination').length) {
                this.updatePagination();
            }
        },

        loadCards: function(page = 1) {
            const container = $('.pledgen-cards-grid');
            if (!container.length) return;

            // Show loading state
            container.html('<div class="pledgen-loading">Loading cards...</div>');

            // Get filter values
            const filters = this.getFilterValues();
            filters.offset = (page - 1) * (filters.limit || 20);

            // Make AJAX request
            $.ajax({
                url: Pledgen.config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'pledgen_get_cards',
                    nonce: Pledgen.config.nonce,
                    ...filters
                },
                success: function(response) {
                    if (response.success) {
                        Pledgen.App.renderCards(response.data);
                        Pledgen.App.updatePagination(response.data.pagination, page);
                    } else {
                        container.html('<div class="pledgen-error">' + response.data + '</div>');
                    }
                },
                error: function() {
                    container.html('<div class="pledgen-error">Failed to load cards. Please try again.</div>');
                }
            });
        },

        getFilterValues: function() {
            const filters = {};
            
            // Get search term
            const search = $('.pledgen-search input').val();
            if (search) filters.search = search;

            // Get category
            const category = $('.pledgen-filter-group select[name="category"]').val();
            if (category) filters.category = category;

            // Get sort options
            const sortBy = $('.pledgen-filter-group select[name="sort_by"]').val();
            if (sortBy) filters.sort_by = sortBy;

            const sortOrder = $('.pledgen-filter-group select[name="sort_order"]').val();
            if (sortOrder) filters.sort_order = sortOrder;

            // Get limit
            const limit = $('.pledgen-filter-group select[name="limit"]').val();
            if (limit) filters.limit = parseInt(limit);

            return filters;
        },

        renderCards: function(data) {
            const container = $('.pledgen-cards-grid');
            const cards = data.data || [];

            if (cards.length === 0) {
                container.html('<div class="pledgen-error">No cards found matching your criteria.</div>');
                return;
            }

            let html = '';
            cards.forEach(function(card) {
                html += Pledgen.Templates.card(card);
            });

            container.html(html);
        },

        updatePagination: function(pagination, currentPage = 1) {
            const paginationContainer = $('.pledgen-pagination');
            if (!paginationContainer.length || !pagination) return;

            const totalPages = Math.ceil(pagination.total / pagination.limit);
            if (totalPages <= 1) {
                paginationContainer.hide();
                return;
            }

            let html = '';

            // Previous button
            const prevDisabled = currentPage <= 1 ? 'disabled' : '';
            html += `<button class="pledgen-btn pledgen-btn-secondary ${prevDisabled}" data-page="${currentPage - 1}">‹</button>`;

            // Page numbers
            const startPage = Math.max(1, currentPage - 2);
            const endPage = Math.min(totalPages, currentPage + 2);

            if (startPage > 1) {
                html += '<button class="pledgen-btn pledgen-btn-secondary" data-page="1">1</button>';
                if (startPage > 2) {
                    html += '<span class="pledgen-pagination-ellipsis">...</span>';
                }
            }

            for (let i = startPage; i <= endPage; i++) {
                const currentClass = i === currentPage ? 'current' : '';
                html += `<button class="pledgen-btn pledgen-btn-secondary ${currentClass}" data-page="${i}">${i}</button>`;
            }

            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    html += '<span class="pledgen-pagination-ellipsis">...</span>';
                }
                html += `<button class="pledgen-btn pledgen-btn-secondary" data-page="${totalPages}">${totalPages}</button>`;
            }

            // Next button
            const nextDisabled = currentPage >= totalPages ? 'disabled' : '';
            html += `<button class="pledgen-btn pledgen-btn-secondary ${nextDisabled}" data-page="${currentPage + 1}">›</button>`;

            paginationContainer.html(html).show();
        },

        loadAvailableFilters: function() {
            // Load categories
            $.ajax({
                url: Pledgen.config.apiBase + '/categories',
                type: 'GET',
                success: function(response) {
                    if (response.success && response.data) {
                        Pledgen.App.populateCategoryFilter(response.data);
                    }
                }
            });

            // Load sets
            $.ajax({
                url: Pledgen.config.apiBase + '/sets',
                type: 'GET',
                success: function(response) {
                    if (response.success && response.data) {
                        Pledgen.App.populateSetFilter(response.data);
                    }
                }
            });
        },

        populateCategoryFilter: function(categories) {
            const select = $('.pledgen-filter-group select[name="category"]');
            if (!select.length) return;

            let html = '<option value="">All Categories</option>';
            categories.forEach(function(category) {
                html += `<option value="${category}">${category}</option>`;
            });

            select.html(html);
        },

        populateSetFilter: function(sets) {
            const select = $('.pledgen-filter-group select[name="set"]');
            if (!select.length) return;

            let html = '<option value="">All Sets</option>';
            sets.forEach(function(set) {
                html += `<option value="${set}">${set}</option>`;
            });

            select.html(html);
        },

        showCardModal: function(identifier) {
            // Show loading modal
            this.showModal('<div class="pledgen-loading">Loading card details...</div>');

            // Load card data
            $.ajax({
                url: Pledgen.config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'pledgen_get_card',
                    nonce: Pledgen.config.nonce,
                    identifier: identifier
                },
                success: function(response) {
                    if (response.success) {
                        const html = Pledgen.Templates.singleCard(response.data.data);
                        Pledgen.App.showModal(html);
                    } else {
                        Pledgen.App.showModal('<div class="pledgen-error">' + response.data + '</div>');
                    }
                },
                error: function() {
                    Pledgen.App.showModal('<div class="pledgen-error">Failed to load card details.</div>');
                }
            });
        },

        showModal: function(content) {
            const modal = `
                <div class="pledgen-modal-overlay">
                    <div class="pledgen-modal">
                        <button class="pledgen-modal-close">&times;</button>
                        <div class="pledgen-modal-content">
                            ${content}
                        </div>
                    </div>
                </div>
            `;

            $('body').append(modal);
            $('body').addClass('pledgen-modal-open');
        },

        closeModal: function() {
            $('.pledgen-modal-overlay').remove();
            $('body').removeClass('pledgen-modal-open');
        }
    };

    // Templates
    Pledgen.Templates = {
        card: function(card) {
            const imageUrl = card.image_url || '';
            const imageHtml = imageUrl ? 
                `<img src="${imageUrl}" alt="${card.name}" loading="lazy">` :
                `<div class="placeholder">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                    </svg>
                    <span>No Image</span>
                </div>`;

            const priceHtml = card.latest_price ? 
                `$${parseFloat(card.latest_price).toLocaleString()}` :
                '<span class="no-price">No price data</span>';

            const metaTags = [];
            if (card.category) metaTags.push(`<span>${card.category}</span>`);
            if (card.year) metaTags.push(`<span>${card.year}</span>`);
            if (card.grading) metaTags.push(`<span>${card.grading}</span>`);
            if (card.set_name) metaTags.push(`<span>${card.set_name}</span>`);

            return `
                <div class="pledgen-card" data-card-id="${card.id}" data-card-slug="${card.name.toLowerCase().replace(/[^a-z0-9]+/g, '-')}">
                    <div class="pledgen-card-image">
                        ${imageHtml}
                    </div>
                    <div class="pledgen-card-content">
                        <h3 class="pledgen-card-title">${card.name}</h3>
                        <div class="pledgen-card-meta">
                            ${metaTags.join('')}
                        </div>
                        <div class="pledgen-card-price ${!card.latest_price ? 'no-price' : ''}">
                            ${priceHtml}
                        </div>
                        <div class="pledgen-card-actions">
                            <button class="pledgen-btn pledgen-btn-primary">View Details</button>
                            <button class="pledgen-btn pledgen-btn-secondary">Track Price</button>
                        </div>
                    </div>
                </div>
            `;
        },

        singleCard: function(card) {
            const imageUrl = card.image_url || '';
            const imageHtml = imageUrl ? 
                `<img src="${imageUrl}" alt="${card.name}" loading="lazy">` :
                `<div class="placeholder">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                    </svg>
                    <span>No Image Available</span>
                </div>`;

            const priceHtml = card.latest_price ? 
                `$${parseFloat(card.latest_price).toLocaleString()}` :
                '<span class="no-price">No price data available</span>';

            const metaItems = [];
            if (card.category) metaItems.push(`<div class="meta-item"><div class="meta-label">Category</div><div class="meta-value">${card.category}</div></div>`);
            if (card.year) metaItems.push(`<div class="meta-item"><div class="meta-label">Year</div><div class="meta-value">${card.year}</div></div>`);
            if (card.grading) metaItems.push(`<div class="meta-item"><div class="meta-label">Grade</div><div class="meta-value">${card.grading}</div></div>`);
            if (card.set_name) metaItems.push(`<div class="meta-item"><div class="meta-label">Set</div><div class="meta-value">${card.set_name}</div></div>`);
            if (card.rarity) metaItems.push(`<div class="meta-item"><div class="meta-label">Rarity</div><div class="meta-value">${card.rarity}</div></div>`);

            const priceHistoryHtml = card.price_history && card.price_history.length > 0 ? 
                this.renderPriceChart(card.price_history) :
                '<div class="pledgen-chart-container">No price history available</div>';

            return `
                <div class="pledgen-single-card">
                    <div class="pledgen-single-card-header">
                        <div class="pledgen-single-card-image">
                            ${imageHtml}
                        </div>
                        <div class="pledgen-single-card-info">
                            <h1>${card.name}</h1>
                            <div class="pledgen-single-card-meta">
                                ${metaItems.join('')}
                            </div>
                            <div class="pledgen-single-card-price ${!card.latest_price ? 'no-price' : ''}">
                                ${priceHtml}
                            </div>
                            <div class="pledgen-single-card-actions">
                                <a href="#" class="pledgen-btn pledgen-btn-primary" target="_blank">Buy on eBay</a>
                                <a href="#" class="pledgen-btn pledgen-btn-secondary" target="_blank">View on TCGPlayer</a>
                                <button class="pledgen-btn pledgen-btn-secondary">Track Price</button>
                            </div>
                        </div>
                    </div>
                    <div class="pledgen-price-chart">
                        <h3>Price History</h3>
                        ${priceHistoryHtml}
                    </div>
                </div>
            `;
        },

        renderPriceChart: function(priceHistory) {
            // Simple HTML chart - in a real implementation, you might use Chart.js or similar
            if (priceHistory.length < 2) {
                return '<div class="pledgen-chart-container">Insufficient data for chart</div>';
            }

            const prices = priceHistory.map(entry => entry.price);
            const minPrice = Math.min(...prices);
            const maxPrice = Math.max(...prices);
            const range = maxPrice - minPrice;

            let chartHtml = '<div class="pledgen-chart-container" style="position: relative; height: 200px;">';
            chartHtml += '<div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; display: flex; align-items: end; padding: 20px; gap: 2px;">';

            priceHistory.forEach((entry, index) => {
                const height = range > 0 ? ((entry.price - minPrice) / range) * 100 : 50;
                const color = index === 0 ? '#28a745' : '#007cba';
                
                chartHtml += `
                    <div style="
                        flex: 1;
                        height: ${height}%;
                        background: ${color};
                        border-radius: 2px 2px 0 0;
                        position: relative;
                        min-height: 4px;
                    " title="$${entry.price} - ${new Date(entry.timestamp).toLocaleDateString()}">
                    </div>
                `;
            });

            chartHtml += '</div></div>';

            return chartHtml;
        }
    };

    // Initialize when DOM is ready
    $(document).ready(function() {
        Pledgen.App.init();
    });

})(jQuery); 
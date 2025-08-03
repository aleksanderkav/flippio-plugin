# Pledgen - Flippio Card Tracker WordPress Plugin

A powerful WordPress plugin that connects to the Flippio API to display trading card data with searchable, filterable shortcodes and native WordPress styling.

## 🚀 Features

- **Two Main Shortcodes**: `[pledgen_cards]` and `[pledgen_card]`
- **Advanced Filtering**: Search by name, filter by category, year, grading, set
- **Responsive Design**: Works perfectly on desktop, tablet, and mobile
- **Price Tracking**: Display current prices and price history charts
- **Affiliate Integration**: Built-in links to eBay and TCGPlayer
- **Caching**: 1-hour cache for optimal performance
- **Admin Dashboard**: Statistics widget and management interface
- **No iFrames**: Pure WordPress integration with native styling
- **Dark Mode Support**: Automatic dark mode detection

## 📦 Installation

1. **Download the Plugin**
   ```bash
   git clone https://github.com/aleksanderkav/flippio-plugin.git
   ```

2. **Upload to WordPress**
   - Copy the `pledgen` folder to your WordPress `/wp-content/plugins/` directory
   - Or zip the `pledgen` folder and upload via WordPress admin

3. **Activate the Plugin**
   - Go to WordPress Admin → Plugins
   - Find "Pledgen - Flippio Card Tracker" and click "Activate"

4. **Verify Installation**
   - Check the "Pledgen" menu item in your WordPress admin
   - Verify API connection status in the admin panel

## 🎯 Quick Start

### Display All Cards
```
[pledgen_cards]
```

### Display Pokemon Cards Only
```
[pledgen_cards category="Pokemon" limit="12"]
```

### Display Single Card
```
[pledgen_card slug="charizard-psa-10"]
```

## 📋 Shortcode Reference

### `[pledgen_cards]` Parameters

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `limit` | number | 20 | Number of cards per page (max 100) |
| `category` | string | '' | Filter by category (Pokemon, Sports, Gaming, Other) |
| `search` | string | '' | Search term for card names |
| `sort_by` | string | created_at | Sort field (created_at, latest_price, name, year) |
| `sort_order` | string | desc | Sort direction (asc, desc) |
| `show_filters` | boolean | true | Show/hide filter controls |
| `show_pagination` | boolean | true | Show/hide pagination |

### `[pledgen_card]` Parameters

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `slug` | string | '' | Card slug (e.g., "charizard-psa-10") |
| `id` | string | '' | Card UUID identifier |

## 🎨 Usage Examples

### Basic Card Grid
```
[pledgen_cards]
```
Shows all cards with default settings (20 per page, with filters and pagination).

### Filtered Display
```
[pledgen_cards category="Pokemon" limit="12" sort_by="latest_price" sort_order="desc"]
```
Shows only Pokemon cards, sorted by price (highest first), 12 cards per page.

### Search Results
```
[pledgen_cards search="charizard" limit="50"]
```
Searches for cards containing "charizard" and displays up to 50 results.

### Single Card Display
```
[pledgen_card slug="charizard-psa-10"]
```
Displays detailed information for a specific Charizard card.

### Minimal Display (No Filters)
```
[pledgen_cards show_filters="false" show_pagination="false" limit="6"]
```
Shows 6 cards without filter controls or pagination.

## 🛠️ API Integration

The plugin connects to the Flippio API at:
```
https://vercel-ny-front-flippio-plra8uev9-aleksanderkavs-projects.vercel.app/api/public
```

### Available Endpoints
- `GET /cards` - List all cards with filtering and pagination
- `GET /cards/{id}` - Get single card by ID
- `GET /cards/slug/{slug}` - Get single card by slug
- `GET /stats` - Get collection statistics
- `GET /categories` - Get available categories
- `GET /sets` - Get available sets

### Data Structure
Each card includes:
- Basic info (name, category, year, grading, set)
- Price data (latest price, price history)
- Images (when available)
- Metadata (rarity, card type, serial number)

## 🎨 Customization

### CSS Customization
The plugin uses CSS classes prefixed with `pledgen-`. You can override styles in your theme:

```css
/* Custom card styling */
.pledgen-card {
    border-radius: 16px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
}

/* Custom button colors */
.pledgen-btn-primary {
    background: #your-brand-color;
}
```

### Template Overrides
Create custom templates by copying files from `pledgen/templates/` to your theme:
```
your-theme/
├── pledgen/
│   ├── cards-grid.php
│   └── single-card.php
```

## 🔧 Admin Features

### Dashboard Widget
- Total cards count
- Total collection value
- Average card price
- Last update timestamp

### Admin Page
- Collection statistics
- Shortcode examples
- API status monitoring
- Cache management
- Parameter documentation

### Cache Management
- Automatic 1-hour caching
- Manual cache clearing
- Performance optimization

## 🚀 Performance

### Caching Strategy
- API responses cached for 1 hour
- Transients used for WordPress compatibility
- Automatic cache invalidation on plugin updates

### Optimization Tips
1. Use appropriate `limit` values (20-50 for most cases)
2. Enable caching (default behavior)
3. Use specific categories to reduce data transfer
4. Consider disabling filters/pagination for static displays

## 🔒 Security

### Data Sanitization
- All user inputs sanitized
- SQL injection protection
- XSS prevention
- Nonce verification for AJAX requests

### API Security
- No authentication required (public API)
- CORS enabled for cross-origin requests
- Rate limiting handled by API provider

## 🐛 Troubleshooting

### Common Issues

**Cards not loading**
- Check API connection in admin panel
- Verify shortcode syntax
- Clear plugin cache

**Filters not working**
- Ensure JavaScript is enabled
- Check browser console for errors
- Verify jQuery is loaded

**Styling issues**
- Check for theme conflicts
- Verify CSS is loading
- Test with default theme

### Debug Mode
Enable WordPress debug mode to see detailed error messages:
```php
// In wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

## 📱 Responsive Design

The plugin is fully responsive and works on:
- Desktop (1200px+)
- Tablet (768px - 1199px)
- Mobile (320px - 767px)

### Mobile Optimizations
- Touch-friendly buttons
- Optimized card layouts
- Simplified navigation
- Reduced image sizes

## 🌙 Dark Mode

Automatic dark mode detection with:
- System preference detection
- Theme-aware styling
- Consistent color schemes
- Accessible contrast ratios

## 🔄 Updates

### Version History
- **1.0.0** - Initial release with core functionality

### Update Process
1. Backup your site
2. Deactivate plugin
3. Upload new version
4. Reactivate plugin
5. Clear cache if needed

## 📞 Support

### Documentation
- [API Documentation](../API_DOCUMENTATION.md)
- [WordPress Integration Guide](../WORDPRESS_INTEGRATION.md)

### Issues & Questions
- Check the troubleshooting section
- Review API documentation
- Test with default settings
- Contact support if needed

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📄 License

This plugin is licensed under the GPL v2 or later.

## 🙏 Credits

- **Author**: Aleksander Kavli
- **API**: Flippio Card Tracker
- **Framework**: WordPress
- **Icons**: SVG icons included

---

**Pledgen** - Bringing trading card data to WordPress with style and functionality. 
# Pledgen Plugin Structure

## 📁 Complete File Structure

```
pledgen/
├── pledgen.php                 # Main plugin file
├── README.md                   # Plugin documentation
├── PLUGIN_STRUCTURE.md         # This file
├── test-plugin.php             # Test file (remove in production)
├── assets/
│   ├── css/
│   │   ├── pledgen.css         # Main frontend styles
│   │   └── admin.css           # Admin interface styles
│   └── js/
│       ├── pledgen.js          # Main frontend JavaScript
│       └── admin.js            # Admin interface JavaScript
└── templates/
    ├── cards-grid.php          # Cards grid template
    ├── single-card.php         # Single card template
    └── admin-page.php          # Admin page template
```

## 🔧 Core Files

### `pledgen.php` - Main Plugin File
- Plugin header and metadata
- Main Pledgen class with singleton pattern
- Shortcode registration and handlers
- AJAX endpoints for dynamic loading
- Admin menu and dashboard widget
- API integration with caching
- Security and sanitization

### `assets/css/pledgen.css` - Frontend Styles
- Responsive grid layouts
- Card styling with hover effects
- Filter and pagination styles
- Modal and overlay styles
- Dark mode support
- Mobile optimizations

### `assets/js/pledgen.js` - Frontend JavaScript
- AJAX card loading
- Dynamic filtering and search
- Pagination handling
- Modal functionality
- Template rendering
- Event handling

## 📋 Templates

### `templates/cards-grid.php`
- Displays card grid with filters
- Handles empty states
- Pagination controls
- Search functionality

### `templates/single-card.php`
- Detailed card information
- Price history charts
- Similar cards section
- Affiliate links

### `templates/admin-page.php`
- Statistics dashboard
- Shortcode examples
- API status monitoring
- Cache management

## 🎯 Shortcodes

### `[pledgen_cards]`
**Parameters:**
- `limit` (number, default: 20)
- `category` (string, default: '')
- `search` (string, default: '')
- `sort_by` (string, default: 'created_at')
- `sort_order` (string, default: 'desc')
- `show_filters` (boolean, default: true)
- `show_pagination` (boolean, default: true)

**Usage Examples:**
```
[pledgen_cards]
[pledgen_cards category="Pokemon" limit="12"]
[pledgen_cards sort_by="latest_price" sort_order="desc"]
```

### `[pledgen_card]`
**Parameters:**
- `slug` (string, default: '')
- `id` (string, default: '')

**Usage Examples:**
```
[pledgen_card slug="charizard-psa-10"]
[pledgen_card id="card-uuid-here"]
```

## 🔌 API Integration

### Endpoints Used
- `GET /api/public/cards` - List cards with filtering
- `GET /api/public/cards/{id}` - Single card by ID
- `GET /api/public/cards/slug/{slug}` - Single card by slug
- `GET /api/public/stats` - Collection statistics
- `GET /api/public/categories` - Available categories
- `GET /api/public/sets` - Available sets

### Caching Strategy
- 1-hour cache for all API responses
- WordPress transients for compatibility
- Automatic cache invalidation
- Manual cache clearing in admin

## 🛡️ Security Features

### Input Sanitization
- All user inputs sanitized with `sanitize_text_field()`
- SQL injection protection
- XSS prevention with `esc_html()` and `esc_attr()`

### AJAX Security
- Nonce verification for all AJAX requests
- Capability checks for admin functions
- Rate limiting considerations

## 📱 Responsive Design

### Breakpoints
- Desktop: 1200px+
- Tablet: 768px - 1199px
- Mobile: 320px - 767px

### Mobile Optimizations
- Touch-friendly buttons
- Optimized card layouts
- Simplified navigation
- Reduced image sizes

## 🌙 Dark Mode Support

### Automatic Detection
- System preference detection
- Theme-aware styling
- Consistent color schemes
- Accessible contrast ratios

## 🔧 Admin Features

### Dashboard Widget
- Total cards count
- Total collection value
- Average card price
- Last update timestamp

### Admin Page Sections
- Collection statistics
- Shortcode examples
- API status monitoring
- Cache management
- Parameter documentation

## 🚀 Performance Optimizations

### Caching
- API responses cached for 1 hour
- Transients used for WordPress compatibility
- Automatic cache invalidation

### Loading Optimizations
- Lazy loading for images
- Debounced search input
- Efficient DOM manipulation
- Minimal JavaScript footprint

## 🐛 Error Handling

### Graceful Degradation
- API connection failures
- Empty data states
- Invalid shortcode parameters
- JavaScript disabled scenarios

### User Feedback
- Loading states
- Error messages
- Success confirmations
- Helpful error descriptions

## 📦 Installation Instructions

1. **Upload Plugin**
   ```bash
   # Copy to WordPress plugins directory
   cp -r pledgen /path/to/wordpress/wp-content/plugins/
   ```

2. **Activate Plugin**
   - WordPress Admin → Plugins → Pledgen → Activate

3. **Verify Installation**
   - Check "Pledgen" menu in admin
   - Test API connection
   - Try shortcodes on a page

## 🔄 Update Process

1. Backup current installation
2. Deactivate plugin
3. Upload new version
4. Reactivate plugin
5. Clear cache if needed

## 🧪 Testing

### Test File
- `test-plugin.php` provides basic functionality testing
- Remove before production use
- Tests API connection and plugin constants

### Manual Testing
- Test all shortcode parameters
- Verify responsive design
- Check dark mode functionality
- Test admin features

## 📄 License

GPL v2 or later - WordPress compatible

## 🙏 Credits

- **Author**: Aleksander Kavli
- **API**: Flippio Card Tracker
- **Framework**: WordPress
- **Icons**: SVG icons included

---

**Pledgen** - Complete WordPress integration for trading card data display. 
# WooCommerce Subscription Date Manager

A WordPress plugin that allows users to manage their WooCommerce subscription renewal dates from the My Account section.

## Description

WooCommerce Subscription Date Manager adds a new "Renewal Manager" tab to the WooCommerce My Account section, where customers can view and modify their subscription renewal dates. This plugin provides a user-friendly interface for customers to adjust their subscription payment schedules within allowed limits.

## Features

- Adds a new "Renewal Manager" tab in My Account section
- Displays all active subscriptions with their current renewal dates
- Allows users to select new renewal dates using a date picker
- Sets maximum extension period (default 30 days)
- Shows clear success/error messages
- Maintains subscription payment schedules
- Mobile-responsive interface

## Requirements

- WordPress 5.0 or higher
- WooCommerce 3.0 or higher
- WooCommerce Subscriptions 2.0 or higher
- PHP 7.2 or higher

## Installation

1. Download the plugin zip file
2. Go to WordPress admin > Plugins > Add New
3. Click "Upload Plugin" and select the downloaded zip file
4. Click "Install Now"
5. After installation, click "Activate"
6. Go to Settings > Permalinks and click "Save Changes" to flush rewrite rules

## Usage

### For Customers:
1. Log in to your account
2. Navigate to My Account > Renewal Manager
3. View your active subscriptions
4. Select a new renewal date using the date picker
5. Click "Update Renewal Date" to save changes

### For Administrators:
- No additional configuration required
- Plugin works out of the box with default settings
- Maximum extension period is set to 30 days by default

## Screenshots

1. Renewal Manager tab in My Account
2. Subscription renewal date management interface
3. Success message after date update

## Frequently Asked Questions

**Q: Can users select any date for renewal?**  
A: No, users can only select dates within the allowed extension period (default 30 days from current renewal date).

**Q: Does this affect existing subscription payments?**  
A: Yes, the plugin updates both the renewal date and payment schedule to maintain consistency.

**Q: Is this compatible with all payment gateways?**  
A: Yes, the plugin works with all payment gateways supported by WooCommerce Subscriptions.

## Support

For support, please:
1. Create an issue in this repository
2. Email: support@codesfix.com
3. Visit our website: [CodesFix.com](https://codesfix.com)

## Contributing

We welcome contributions! Please feel free to:
1. Fork the repository
2. Create a feature branch
3. Submit a pull request

## Changelog

### 1.0.0 (2024-12-04)
- Initial release
- Basic renewal date management functionality
- My Account integration
- Mobile-responsive interface

## License

This project is licensed under the GPL v2 or later

## Credits

Developed by CodesFix

## Security Vulnerabilities

If you discover a security vulnerability, please send an email to security@codesfix.com

## Todo List

- [ ] Add email notifications for date changes
- [ ] Include subscription history log
- [ ] Add admin configuration options
- [ ] Implement bulk date update feature
- [ ] Add date change limitations per subscription type

## Testing

### Prerequisites
- WordPress test environment
- WooCommerce installed and activated
- WooCommerce Subscriptions installed and activated

### Running Tests
1. Install PHPUnit
2. Run `phpunit` in the plugin directory

## Development

### Setup Development Environment
```bash
# Clone the repository
git clone https://github.com/codesfix/wc-subscription-date-manager

# Install dependencies
composer install

# Run tests
composer test
```

### Coding Standards
This project follows WordPress coding standards. To check your code:
```bash
composer run phpcs
```

## Additional Information

This plugin is maintained by CodesFix. For custom development or support, contact us at info@codesfix.com

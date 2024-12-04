<?php
/**
 * Plugin Name: WooCommerce Subscription Date Manager
 * Description: Allows customers to adjust their subscription renewal dates
 * Version: 1.0
 * Author: CodesFix
 */

defined('ABSPATH') || exit;

class WC_Subscription_Date_Manager {
    
    public function __construct() {
        if (!class_exists('WooCommerce')) {
            add_action('admin_notices', array($this, 'woocommerce_missing_notice'));
            return;
        }
        $this->init();
    }

    private function init() {
        // Add menu item and endpoint
        add_filter('woocommerce_account_menu_items', array($this, 'add_renewal_menu_item'), 20);
        add_action('init', array($this, 'add_renewal_endpoint'));
        
        // Add content to endpoint
        add_action('woocommerce_account_renewal-date_endpoint', array($this, 'renewal_content'));
        
        // Handle form submission
        add_action('init', array($this, 'handle_date_update'));
    }

    public function add_renewal_menu_item($items) {
        $new_items = array();
        foreach ($items as $key => $item) {
            $new_items[$key] = $item;
            if ($key === 'subscriptions') {
                $new_items['renewal-date'] = 'Renewal Manager';
            }
        }
        return $new_items;
    }

    public function add_renewal_endpoint() {
        add_rewrite_endpoint('renewal-date', EP_ROOT | EP_PAGES);
    }

    public function renewal_content() {
        // Check if WC_Subscriptions class exists
        if (!class_exists('WC_Subscriptions')) {
            echo '<p>WooCommerce Subscriptions is required.</p>';
            return;
        }

        // Get user's subscriptions
        $subscriptions = wcs_get_users_subscriptions(get_current_user_id());
        
        if (empty($subscriptions)) {
            echo '<p>No active subscriptions found.</p>';
            return;
        }

        // Show success message if date was updated
        if (isset($_GET['updated']) && $_GET['updated'] == '1') {
            echo '<div class="woocommerce-message">Renewal date updated successfully.</div>';
        }

        echo '<div class="woocommerce-subscriptions-management">';
        
        foreach ($subscriptions as $subscription) {
            if ($subscription->get_status() == 'active') {
                $this->display_subscription_form($subscription);
            }
        }
        
        echo '</div>';
    }

    private function display_subscription_form($subscription) {
        $subscription_id = $subscription->get_id();
        $next_payment = $subscription->get_date('next_payment');
        $max_days = 30; // Maximum days to extend
        $max_date = date('Y-m-d', strtotime($next_payment . ' + ' . $max_days . ' days'));
        
        // Get subscription details
        $product_names = array();
        foreach ($subscription->get_items() as $item) {
            $product_names[] = $item->get_name();
        }
        
        ?>
        <div class="subscription-card" style="margin-bottom: 30px; padding: 20px; border: 1px solid #ddd; background: #fff;">
            <h3>Subscription #<?php echo $subscription_id; ?></h3>
            
            <div class="subscription-details" style="margin: 15px 0;">
                <p><strong>Products:</strong> <?php echo implode(', ', $product_names); ?></p>
                <p><strong>Status:</strong> <span style="color: #2ecc71;">Active</span></p>
                <p><strong>Current Renewal Date:</strong> <?php echo date_i18n(get_option('date_format'), strtotime($next_payment)); ?></p>
                <p><strong>Maximum Extension Date:</strong> <?php echo date_i18n(get_option('date_format'), strtotime($max_date)); ?></p>
            </div>

            <form method="post" class="renewal-form">
                <input type="hidden" name="subscription_id" value="<?php echo esc_attr($subscription_id); ?>">
                <?php wp_nonce_field('update_renewal_date', 'renewal_nonce'); ?>
                
                <div class="form-field" style="margin-bottom: 15px;">
                    <label for="new_date_<?php echo $subscription_id; ?>" style="display: block; margin-bottom: 5px;">
                        <strong>Select New Renewal Date:</strong>
                    </label>
                    <input 
                        type="date" 
                        id="new_date_<?php echo $subscription_id; ?>"
                        name="new_renewal_date"
                        min="<?php echo esc_attr(date('Y-m-d', strtotime('+1 day'))); ?>"
                        max="<?php echo esc_attr($max_date); ?>"
                        required
                        style="width: 200px; padding: 5px;"
                    >
                </div>

                <button type="submit" class="button" name="update_renewal_date" value="1" 
                        style="background: #2ecc71; color: white; border: none; padding: 10px 20px;">
                    Update Renewal Date
                </button>
            </form>
        </div>
        <?php
    }

    public function handle_date_update() {
        if (!isset($_POST['update_renewal_date']) || !isset($_POST['renewal_nonce'])) {
            return;
        }

        if (!wp_verify_nonce($_POST['renewal_nonce'], 'update_renewal_date')) {
            wc_add_notice('Invalid request.', 'error');
            return;
        }

        $subscription_id = intval($_POST['subscription_id']);
        $new_date = sanitize_text_field($_POST['new_renewal_date']);
        $subscription = wcs_get_subscription($subscription_id);

        if (!$subscription || $subscription->get_user_id() != get_current_user_id()) {
            wc_add_notice('Invalid subscription.', 'error');
            return;
        }

        try {
            // Format the date with time
            $formatted_date = date('Y-m-d H:i:s', strtotime($new_date));

            // Update the dates
            $subscription->update_dates(array(
                'next_payment' => $formatted_date,
                'last_order_date_created' => current_time('mysql'),
            ));

            // Add order note
            $subscription->add_order_note(
                sprintf('Customer changed renewal date to %s', 
                date_i18n(get_option('date_format'), strtotime($new_date)))
            );

            // Save the subscription
            $subscription->save();

            // Redirect with success message
            wp_redirect(add_query_arg('updated', '1', wc_get_account_endpoint_url('renewal-date')));
            exit;

        } catch (Exception $e) {
            wc_add_notice('Error updating renewal date: ' . $e->getMessage(), 'error');
        }
    }
}

// Initialize plugin
function init_subscription_date_manager() {
    new WC_Subscription_Date_Manager();
}
add_action('plugins_loaded', 'init_subscription_date_manager');

// Activation hook
register_activation_hook(__FILE__, 'activate_subscription_date_manager');
function activate_subscription_date_manager() {
    add_rewrite_endpoint('renewal-date', EP_ROOT | EP_PAGES);
    flush_rewrite_rules();
}

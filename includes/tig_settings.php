<?php

/**
 * @package     Woocommerce Tera Bank Installments Gateway
 * @author      Sergi Khizanishvili. https://sweb.ge/
 * @since       1.0.0
 * @license     GPLv3
 */

if (!defined('ABSPATH')) {
	exit;
}

function tig_no_woocommerce() {
	echo '<div class="error notice"><p>' . __('<strong>Terabank Installments Gateway:</strong> In order to use this plugin - Woocommerce should be installed and activated', 'terabank-installments') . '</p></div>';
}

function tig_handling_fee() {	
	global $woocommerce;
	
	if (is_admin() && !defined('DOING_AJAX')) {
		return;
	}
	
	if (!is_checkout()) {
		return;
	}
	
	if (WC()->session->get('chosen_payment_method') != 'terabank_installments') {
		return;
	}
	
	$terabank_settings = get_option('woocommerce_terabank_installments_settings', array());
	
	if (empty($terabank_settings) || empty($terabank_settings['handling_fee']) || !filter_var($terabank_settings['handling_fee'], FILTER_VALIDATE_FLOAT) || $terabank_settings['handling_fee'] <= 0) {
		$fee = 0;
	} else {
		$fee = $terabank_settings['handling_fee'];
	}
	
	$woocommerce->cart->add_fee(__('Handling fee', 'terabank-installments'), round(($woocommerce->cart->cart_contents_total) * $fee / 100, 2), true);
}
add_action('woocommerce_cart_calculate_fees', 'tig_handling_fee');

function tig_update_totals() {
	if (is_checkout()) {
	?>		
	<script>
		jQuery('form.checkout').on('change', 'input[name="payment_method"]', function() {
			jQuery(this).trigger('update_checkout');
		});
	</script>
	<?php		
	}
}
add_action('wp_footer', 'tig_update_totals');

function tig_action_init() {
	load_plugin_textdomain('terabank-installments', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/'); 
}
add_action('init', 'tig_action_init');
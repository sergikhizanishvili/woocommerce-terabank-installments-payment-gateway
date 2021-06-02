<?php

/**
 * Plugin Name: Woocommerce Tera Bank Installments Gateway
 * Plugin URI:  https://sweb.ge/
 * Description: Allow clients to purchase products from your store via Tera Bank Installments
 * Version:     1.0.0
 * Author:      Sergi Khizanishvili
 * Author URI:  https://sweb.ge/
 * License:     GPLv3
 * Domain Path: /lang
 * Text Domain: terabank-installments
 * WC requires at least: 3.0.0
 * WC tested up to: 5.3.0
 *
 * @package     Woocommerce Tera Bank Installments Gateway
 * @author      Sergi Khizanishvili. https://sweb.ge/
 * @since       1.0.0
 * @license     GPLv3
 */

if (!defined('ABSPATH')) {
	exit;
}

require plugin_dir_path( __FILE__ ) . 'includes/tig_settings.php';

if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
	
	require plugin_dir_path( __FILE__ ) . 'includes/tig_gateway.php';
	function add_terabank_installments_class($methods) {
		$methods[] = 'WC_Gateway_Terabank_Installments'; 
		return $methods;
	}
	add_filter('woocommerce_payment_gateways', 'add_terabank_installments_class');
	
} else {
	add_action('admin_notices', 'tig_no_woocommerce');
}
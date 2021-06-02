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

return array(
	'enabled' => array(
		'title'       => __('Enable/Disable', 'terabank-installments'),
		'label'       => __('Enable Tera Bank Installments', 'terabank-installments'),
		'type'        => 'checkbox',
		'default'     => 'no'
	),

	'title' => array(
		'title'       => __('Title', 'terabank-installments'),
		'type'        => 'text',
		'description' => __('This controls the title which the user sees during checkout.', 'terabank-installments'),
		'default'     => __('Tera Bank Installments', 'terabank-installments'),
		'desc_tip'    => true,
	),

	'description' => array(
		'title'       => __('Description', 'terabank-installments'),
		'type'        => 'text',
		'description' => __('This controls the description which the user sees during checkout.', 'terabank-installments'),
		'default'     => __('Proceed to Tera Bank Installment', 'terabank-installments'),
		'desc_tip'    => true,
	),

	'order_button_text' => array(
		'title'       => __('Order button text', 'terabank-installments'),
		'type'        => 'text',
		'description' => __('This controls the order button text which the user sees during checkout.', 'terabank-installments'),
		'default'     => __('Proceed to Tera Bank', 'terabank-installments'),
		'desc_tip'    => true,
	),

	'testmode' => array(
		'title'       => __('Test mode', 'terabank-installments'),
		'label'       => __('Enable Test Mode', 'terabank-installments'),
		'type'        => 'checkbox',
		'description' => __('Place the Tera Bank Installments in test mode', 'terabank-installments'),
		'default'     => 'yes',
		'desc_tip'    => true,
	),

	'testmerchantid' => array(
		'title'       => __('Test Merchant ID', 'terabank-installments'),
		'type'        => 'text',
		'description' => __('Test Merchant ID provided by Tera Bank', 'terabank-installments'),
		'default'     => __('', 'terabank-installments'),
		'desc_tip'    => true,
	),

	'realmerchantid' => array(
		'title'       => __('Merchant ID', 'terabank-installments'),
		'type'        => 'text',
		'description' => __('Merchant ID provided by Tera Bank', 'terabank-installments'),
		'default'     => __('', 'terabank-installments'),
		'desc_tip'    => true,
	),

	'min_amount' => array(
		'title'       => __('Min Installment', 'terabank-installments'),
		'type'        => 'number',
		'default'     => 100,
		'description' => __('Minimum installment amount', 'terabank-installments'),
	),

	'max_amount' => array(
		'title'       => __('Max Installment', 'terabank-installments'),
		'type'        => 'number',
		'default'     => 5000,
		'description' => __('Maximum installment amount', 'terabank-installments'),
	),

	'handling_fee' => array(
		'title'       => __('Handling Fee %', 'terabank-installments'),
		'type'        => 'number',
		'default'     => 0,
		'description' => __('Fee % applied to each product price', 'terabank-installments'),
		'custom_attributes' => array(
			'min'  => 0,
			'max'  => 100,
			'step' => 0.01,
		)
	)
);

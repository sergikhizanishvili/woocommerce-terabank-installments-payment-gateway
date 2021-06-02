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

function init_terabank_installments_class()
{
	class WC_Gateway_Terabank_Installments extends WC_Payment_Gateway
	{

		public function __construct()
		{
			$this->id = 'terabank_installments';
			$this->icon = plugin_dir_url(dirname(__FILE__)) . 'assets/terabank-logo.png';
			$this->has_fields = true;
			$this->method_title = __('Woocommerce Terabank Installments Gateway', 'terabank-installments');
			$this->method_description = __('Allow clients to purchase products from your store via Terabank Installments', 'terabank-installments');
			$this->supports = array(
				'products'
			);

			$this->init_form_fields();

			add_action('admin_notices', array($this, 'show_error'));
			add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
		}

		public function init_form_fields()
		{
			$this->form_fields = include 'tig_from_fields.php';
			$this->init_settings();

			$this->enabled = $this->get_option('enabled', 'no');
			$this->title = $this->get_option('title');
			$this->description = $this->get_option('description');
			$this->order_button_text = $this->get_option('order_button_text');
			$this->testmode = 'yes' === $this->get_option('testmode', 'no');
			$this->testmerchantid = $this->get_option('testmerchantid');
			$this->realmerchantid = $this->get_option('realmerchantid');
			$this->min_amount = $this->get_option('min_amount');
			$this->max_amount = $this->get_option('max_amount');
			$this->handling_fee = $this->get_option('handling_fee');
			$this->request_url = ($this->testmode == 'yes') ? 'https://test01.terabank.ge/CustomerOnBoarding.Retail.Api/api/UserSessions/AddStoreProducts' : 'https://online.terabank.ge/CustomerOnBoardingApi/api/UserSessions/AddStoreProducts';
			$this->redirect_url = ($this->testmode == 'yes') ? 'https://test01.terabank.ge/customer-on-boarding/installments/products/' : 'https://online.terabank.ge/installments/products/';
		}

		private function check_preconditions()
		{
			if ($this->testmode == 'yes') {
				if (!empty($this->testmerchantid) && filter_var($this->min_amount, FILTER_VALIDATE_FLOAT) && $this->min_amount >= 0 && filter_var($this->max_amount, FILTER_VALIDATE_FLOAT) && $this->max_amount > $this->min_amount) {
					return true;
				}
			} else {
				if (!empty($this->realmerchantid) && filter_var($this->min_amount, FILTER_VALIDATE_FLOAT) && $this->min_amount >= 0 && filter_var($this->max_amount, FILTER_VALIDATE_FLOAT) && $this->max_amount > $this->min_amount) {
					return true;
				}
			}


			return false;
		}

		public function show_error()
		{
			if ($this->enabled == 'yes' && !$this->check_preconditions()) {
				echo '<div class="error"><p>' . wp_kses_data(sprintf(__('<strong>Terabank Installments Gateway:</strong> Please fill out all the required fields in <a href="%s">Gateway Settings</a>.', 'terabank-installments'), admin_url('admin.php?page=wc-settings&tab=checkout&section=' . $this->id))) . '</p></div>';
			}
		}

		public function is_available()
		{
			$total = WC()->cart->total;
			if (get_woocommerce_currency() == 'GEL' && $total >= $this->min_amount && $total <= $this->max_amount && $this->check_preconditions() && $this->enabled == 'yes') {
				return true;
			}

			return false;
		}

		private function get_token($request)
		{

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request, JSON_UNESCAPED_UNICODE));
			curl_setopt($ch, CURLOPT_URL, $this->request_url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				'Content-Type: application/json; charset=utf-8',
				'Accept: application/json'
			]);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			$response = curl_exec($ch);
			curl_close($ch);
			$parsed = json_decode($response, true);

			if ($parsed['success'] === true && !empty($parsed['storeSessionId'])) {
				return $parsed['storeSessionId'];
			}

			return false;
		}

		public function process_payment($order_id)
		{
			global $woocommerce;

			$order = wc_get_order($order_id);
			$items = $order->get_items();
			$arr = array();

			if (!empty($items)) {
				foreach ($items as $item) {
					$product = $item->get_product();
					array_push(
						$arr,
						array(
							'Code' => $product->get_id(),
							'Name' => $product->get_name(),
							'Amount' => ($product->get_price() + (round($product->get_price() * $this->handling_fee / 100, 2)))*$item->get_quantity(),
							'CashAmount' => $product->get_price()*$item->get_quantity(),
							'Quantity' => $item->get_quantity()
						)
					);
				}
			}

			$request = array(
				'Culture' => 'ka',
				'StoreId' => ($this->testmode == 'yes') ? $this->testmerchantid : $this->realmerchantid,
				'OrderId' => $order_id,
				'Products' => $arr
			);

			$token = $this->get_token($request);
			if ($token) {
				$order_note = __('Tera Bank Installment request initiated.', 'terabank-installments');
				$order->update_status('on-hold', $order_note);
				$woocommerce->cart->empty_cart();

				return array(
					'result' => 'success',
					'redirect' => $this->redirect_url . $token
				);
			}
		}
	}
}
add_action('plugins_loaded', 'init_terabank_installments_class');

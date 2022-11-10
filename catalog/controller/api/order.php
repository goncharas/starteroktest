<?php
class ControllerApiOrder extends Controller {
// SAVIN 20220901 beg  
	private function getOrderProducts($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
    $products = array();
		foreach($query->rows as $row){
		  $products[$row['product_id']][$row['location_id']] = $row;
		}
		return $products;
	}
	private function getOrder($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order WHERE order_id = '" . (int)$order_id . "'");
		return $query->row;
	}
// SAVIN 20220901 end  
  
	public function add() {
		$this->load->language('api/order');

		$json = array();

		if (!isset($this->session->data['api_id'])) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			// Customer
			if (!isset($this->session->data['customer'])) {
				$json['error'] = $this->language->get('error_customer');
			}

			// Payment Address
			if (!isset($this->session->data['payment_address'])) {
				$json['error'] = $this->language->get('error_payment_address');
			}

			// Payment Method
			if (!$json && !empty($this->request->post['payment_method'])) {
				if (empty($this->session->data['payment_methods'])) {
					$json['error'] = $this->language->get('error_no_payment');
				} elseif (!isset($this->session->data['payment_methods'][$this->request->post['payment_method']])) {
					$json['error'] = $this->language->get('error_payment_method');
				}

				if (!$json) {
					$this->session->data['payment_method'] = $this->session->data['payment_methods'][$this->request->post['payment_method']];
				}
			}

			if (!isset($this->session->data['payment_method'])) {
				$json['error'] = $this->language->get('error_payment_method');
			}

			// Shipping
			if ($this->cart->hasShipping()) {
				// Shipping Address
				if (!isset($this->session->data['shipping_address'])) {
					$json['error'] = $this->language->get('error_shipping_address');
				}

				// Shipping Method
				if (!$json && !empty($this->request->post['shipping_method'])) {
					if (empty($this->session->data['shipping_methods'])) {
						$json['error'] = $this->language->get('error_no_shipping');
					} else {
						$shipping = explode('.', $this->request->post['shipping_method']);

						if (!isset($shipping[0]) || !isset($shipping[1]) || !isset($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]])) {
							$json['error'] = $this->language->get('error_shipping_method');
						}
					}

					if (!$json) {
						$this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
					}
				}

				// Shipping Method
				if (!isset($this->session->data['shipping_method'])) {
					$json['error'] = $this->language->get('error_shipping_method');
				}
			} else {
				unset($this->session->data['shipping_address']);
				unset($this->session->data['shipping_method']);
				unset($this->session->data['shipping_methods']);
			}

			// Cart
			if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
				$json['error'] = $this->language->get('error_stock');
			}

			// Validate minimum quantity requirements.
			$products = $this->cart->getProducts();

			foreach ($products as $product) {
				$product_total = 0;

				foreach ($products as $product_2) {
					if ($product_2['product_id'] == $product['product_id']) {
						$product_total += $product_2['quantity'];
					}
				}

				if ($product['minimum'] > $product_total) {
					$json['error'] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);

					break;
				}
			}

			if (!$json) {
				$json['success'] = $this->language->get('text_success');
				
				$order_data = array();

				// Store Details
				$order_data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
				$order_data['store_id'] = $this->config->get('config_store_id');
				$order_data['store_name'] = $this->config->get('config_name');
				$order_data['store_url'] = $this->config->get('config_url');

				// Customer Details
				$order_data['customer_id'] = $this->session->data['customer']['customer_id'];
				$order_data['customer_group_id'] = $this->session->data['customer']['customer_group_id'];
				$order_data['firstname'] = $this->session->data['customer']['firstname'];
				$order_data['lastname'] = $this->session->data['customer']['lastname'];
				$order_data['email'] = $this->session->data['customer']['email'];
				$order_data['telephone'] = $this->session->data['customer']['telephone'];
				$order_data['custom_field'] = $this->session->data['customer']['custom_field'];

				// Payment Details
				$order_data['payment_firstname'] = $this->session->data['payment_address']['firstname'];
				$order_data['payment_lastname'] = $this->session->data['payment_address']['lastname'];
				$order_data['payment_company'] = $this->session->data['payment_address']['company'];
				$order_data['payment_address_1'] = $this->session->data['payment_address']['address_1'];
				$order_data['payment_address_2'] = $this->session->data['payment_address']['address_2'];
				$order_data['payment_city'] = $this->session->data['payment_address']['city'];
				$order_data['payment_postcode'] = $this->session->data['payment_address']['postcode'];
				$order_data['payment_zone'] = $this->session->data['payment_address']['zone'];
				$order_data['payment_zone_id'] = $this->session->data['payment_address']['zone_id'];
				$order_data['payment_country'] = $this->session->data['payment_address']['country'];
				$order_data['payment_country_id'] = $this->session->data['payment_address']['country_id'];
				$order_data['payment_address_format'] = $this->session->data['payment_address']['address_format'];
				$order_data['payment_custom_field'] = (isset($this->session->data['payment_address']['custom_field']) ? $this->session->data['payment_address']['custom_field'] : array());

				if (isset($this->session->data['payment_method']['title'])) {
					$order_data['payment_method'] = $this->session->data['payment_method']['title'];
				} else {
					$order_data['payment_method'] = '';
				}

				if (isset($this->session->data['payment_method']['code'])) {
					$order_data['payment_code'] = $this->session->data['payment_method']['code'];
				} else {
					$order_data['payment_code'] = '';
				}

				// Shipping Details
				if ($this->cart->hasShipping()) {
					$order_data['shipping_firstname'] = $this->session->data['shipping_address']['firstname'];
					$order_data['shipping_lastname'] = $this->session->data['shipping_address']['lastname'];
					$order_data['shipping_company'] = $this->session->data['shipping_address']['company'];
					$order_data['shipping_address_1'] = $this->session->data['shipping_address']['address_1'];
					$order_data['shipping_address_2'] = $this->session->data['shipping_address']['address_2'];
					$order_data['shipping_city'] = $this->session->data['shipping_address']['city'];
					$order_data['shipping_postcode'] = $this->session->data['shipping_address']['postcode'];
					$order_data['shipping_zone'] = $this->session->data['shipping_address']['zone'];
					$order_data['shipping_zone_id'] = $this->session->data['shipping_address']['zone_id'];
					$order_data['shipping_country'] = $this->session->data['shipping_address']['country'];
					$order_data['shipping_country_id'] = $this->session->data['shipping_address']['country_id'];
					$order_data['shipping_address_format'] = $this->session->data['shipping_address']['address_format'];
					$order_data['shipping_custom_field'] = (isset($this->session->data['shipping_address']['custom_field']) ? $this->session->data['shipping_address']['custom_field'] : array());

					if (isset($this->session->data['shipping_method']['title'])) {
						$order_data['shipping_method'] = $this->session->data['shipping_method']['title'];
					} else {
						$order_data['shipping_method'] = '';
					}

					if (isset($this->session->data['shipping_method']['code'])) {
						$order_data['shipping_code'] = $this->session->data['shipping_method']['code'];
					} else {
						$order_data['shipping_code'] = '';
					}
				} else {
					$order_data['shipping_firstname'] = '';
					$order_data['shipping_lastname'] = '';
					$order_data['shipping_company'] = '';
					$order_data['shipping_address_1'] = '';
					$order_data['shipping_address_2'] = '';
					$order_data['shipping_city'] = '';
					$order_data['shipping_postcode'] = '';
					$order_data['shipping_zone'] = '';
					$order_data['shipping_zone_id'] = '';
					$order_data['shipping_country'] = '';
					$order_data['shipping_country_id'] = '';
					$order_data['shipping_address_format'] = '';
					$order_data['shipping_custom_field'] = array();
					$order_data['shipping_method'] = '';
					$order_data['shipping_code'] = '';
				}

				// Products
				$order_data['products'] = array();

				foreach ($this->cart->getProducts() as $product) {
					$option_data = array();

					foreach ($product['option'] as $option) {
						$option_data[] = array(
							'product_option_id'       => $option['product_option_id'],
							'product_option_value_id' => $option['product_option_value_id'],
							'option_id'               => $option['option_id'],
							'option_value_id'         => $option['option_value_id'],
							'name'                    => $option['name'],
							'value'                   => $option['value'],
							'type'                    => $option['type']
						);
					}

					$order_data['products'][] = array(
						'product_id' => $product['product_id'],
						'name'       => $product['name'],
						'model'      => $product['model'],
						'option'     => $option_data,
						'download'   => $product['download'],
						'quantity'   => $product['quantity'],
						'subtract'   => $product['subtract'],
            'location_id'=> $product['location_id'], // SAVIN 20220730
						'price'      => $product['price'],
						'total'      => $product['total'],
						'tax'        => $this->tax->getTax($product['price'], $product['tax_class_id']),
						'reward'     => $product['reward']
					);
				}

				// Gift Voucher
				$order_data['vouchers'] = array();

				if (!empty($this->session->data['vouchers'])) {
					foreach ($this->session->data['vouchers'] as $voucher) {
						$order_data['vouchers'][] = array(
							'description'      => $voucher['description'],
							'code'             => token(10),
							'to_name'          => $voucher['to_name'],
							'to_email'         => $voucher['to_email'],
							'from_name'        => $voucher['from_name'],
							'from_email'       => $voucher['from_email'],
							'voucher_theme_id' => $voucher['voucher_theme_id'],
							'message'          => $voucher['message'],
							'amount'           => $voucher['amount']
						);
					}
				}

				// Order Totals
				$this->load->model('setting/extension');

				$totals = array();
				$taxes = $this->cart->getTaxes();
				$total = 0;

				// Because __call can not keep var references so we put them into an array.
				$total_data = array(
					'totals' => &$totals,
					'taxes'  => &$taxes,
					'total'  => &$total
				);
			
				$sort_order = array();

				$results = $this->model_setting_extension->getExtensions('total');

				foreach ($results as $key => $value) {
					$sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
				}

				array_multisort($sort_order, SORT_ASC, $results);

				foreach ($results as $result) {
					if ($this->config->get('total_' . $result['code'] . '_status')) {
						$this->load->model('extension/total/' . $result['code']);
						
						// We have to put the totals in an array so that they pass by reference.
						$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
					}
				}

				$sort_order = array();

				foreach ($total_data['totals'] as $key => $value) {
					$sort_order[$key] = $value['sort_order'];
				}

				array_multisort($sort_order, SORT_ASC, $total_data['totals']);

				$order_data = array_merge($order_data, $total_data);

				if (isset($this->request->post['comment'])) {
					$order_data['comment'] = $this->request->post['comment'];
				} else {
					$order_data['comment'] = '';
				}

				if (isset($this->request->post['affiliate_id'])) {
					$subtotal = $this->cart->getSubTotal();

					// Affiliate
					$this->load->model('account/customer');

					$affiliate_info = $this->model_account_customer->getAffiliate($this->request->post['affiliate_id']);

					if ($affiliate_info) {
						$order_data['affiliate_id'] = $affiliate_info['customer_id'];
						$order_data['commission'] = ($subtotal / 100) * $affiliate_info['commission'];
					} else {
						$order_data['affiliate_id'] = 0;
						$order_data['commission'] = 0;
					}

					// Marketing
					$order_data['marketing_id'] = 0;
					$order_data['tracking'] = '';
				} else {
					$order_data['affiliate_id'] = 0;
					$order_data['commission'] = 0;
					$order_data['marketing_id'] = 0;
					$order_data['tracking'] = '';
				}

				$order_data['language_id'] = $this->config->get('config_language_id');
				$order_data['currency_id'] = $this->currency->getId($this->session->data['currency']);
				$order_data['currency_code'] = $this->session->data['currency'];
				$order_data['currency_value'] = $this->currency->getValue($this->session->data['currency']);
				$order_data['ip'] = $this->request->server['REMOTE_ADDR'];

				if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
					$order_data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];
				} elseif (!empty($this->request->server['HTTP_CLIENT_IP'])) {
					$order_data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];
				} else {
					$order_data['forwarded_ip'] = '';
				}

				if (isset($this->request->server['HTTP_USER_AGENT'])) {
					$order_data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];
				} else {
					$order_data['user_agent'] = '';
				}

				if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
					$order_data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
				} else {
					$order_data['accept_language'] = '';
				}

				$this->load->model('checkout/order');

				$json['order_id'] = $this->model_checkout_order->addOrder($order_data);

				// Set the order history
				if (isset($this->request->post['order_status_id'])) {
					$order_status_id = $this->request->post['order_status_id'];
				} else {
					$order_status_id = $this->config->get('config_order_status_id');
				}

				$this->model_checkout_order->addOrderHistory($json['order_id'], $order_status_id);
				
				// clear cart since the order has already been successfully stored.
				$this->cart->clear();
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function edit() {
if(catalog_controller_api_order__edit){
$log = fopen(DIR_LOGS . 'catalog_controller_api_order__edit.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,'$this->session->data ) ' . print_r($this->session->data,true) . chr(13) . chr(13));
fwrite($log,'$this->request->get ) ' . print_r($this->request->get,true) . chr(13) . chr(13));
fclose($log);}
		$this->load->language('api/order');

		$json = array();
    $stock_total = true; // SAVIN 20220901

		if (!isset($this->session->data['api_id'])) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			$this->load->model('checkout/order');

			if (isset($this->request->get['order_id'])) {
				$order_id = $this->request->get['order_id'];
			} else {
				$order_id = 0;
			}

			$order_info = $this->model_checkout_order->getOrder($order_id);

			if ($order_info) {
				// Customer
				if (!isset($this->session->data['customer'])) {
					$json['error'] = $this->language->get('error_customer');
				}

				// Payment Address
				if (!isset($this->session->data['payment_address'])) {
					$json['error'] = $this->language->get('error_payment_address');
				}

				// Payment Method
				if (!$json && !empty($this->request->post['payment_method'])) {
					if (empty($this->session->data['payment_methods'])) {
						$json['error'] = $this->language->get('error_no_payment');
					} elseif (!isset($this->session->data['payment_methods'][$this->request->post['payment_method']])) {
						$json['error'] = $this->language->get('error_payment_method');
					}

					if (!$json) {
						$this->session->data['payment_method'] = $this->session->data['payment_methods'][$this->request->post['payment_method']];
					}
				}

				if (!isset($this->session->data['payment_method'])) {
					$json['error'] = $this->language->get('error_payment_method');
				}

				// Shipping
				if ($this->cart->hasShipping()) {
					// Shipping Address
					if (!isset($this->session->data['shipping_address'])) {
						$json['error'] = $this->language->get('error_shipping_address');
					}

					// Shipping Method
					if (!$json && !empty($this->request->post['shipping_method'])) {
						if (empty($this->session->data['shipping_methods'])) {
							$json['error'] = $this->language->get('error_no_shipping');
						} else {
							$shipping = explode('.', $this->request->post['shipping_method']);

							if (!isset($shipping[0]) || !isset($shipping[1]) || !isset($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]])) {
								$json['error'] = $this->language->get('error_shipping_method');
							}
						}

						if (!$json) {
							$this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
						}
					}

					if (!isset($this->session->data['shipping_method'])) {
						$json['error'] = $this->language->get('error_shipping_method');
					}
				} else {
					unset($this->session->data['shipping_address']);
					unset($this->session->data['shipping_method']);
					unset($this->session->data['shipping_methods']);
				}

				// Cart
				if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
					$json['error'] = $this->language->get('error_stock');
//SAVIN 20220901 beg
          if (isset($this->request->get['order_id'])){
            $order_id = $this->request->get['order_id'];
				// Stock subtraction
				    $order_products = $this->getOrderProducts($order_id);
if(catalog_controller_api_order__edit){
$log = fopen(DIR_LOGS . 'catalog_controller_api_order__edit.log', 'a');
fwrite($log,'**********************************' . chr(13) . chr(13));
fwrite($log,'$order_products = $this->getOrderProducts('.$order_id.'); ) ' . print_r($order_products,true) . chr(13) . chr(13));
fclose($log);}
          }
				}

					// Set the order history
					if (isset($this->request->post['order_status_id'])) {
						$order_status_id = $this->request->post['order_status_id'];
					} else {
						$order_status_id = $this->config->get('config_order_status_id');
					}
          $quantity_enabled = in_array($order_status_id, array_merge($this->config->get('config_processing_status'), $this->config->get('config_complete_status')));
if(catalog_controller_api_order__edit){
$log = fopen(DIR_LOGS . 'catalog_controller_api_order__edit.log', 'a');
fwrite($log,' $order_status_id )' . print_r($order_status_id,true) . '==' . chr(10) . chr(13));
fwrite($log,' $quantity_enabled )' . print_r($quantity_enabled,true) . '==' . chr(10) . chr(13));
fwrite($log,' array_merge(config_processing_status + config_complete_status) )' . print_r(array_merge($this->config->get('config_processing_status'), $this->config->get('config_complete_status')),true) . '==' . chr(10) . chr(13));
fclose($log);}

				// Validate minimum quantity requirements.
				$products = $this->cart->getProducts();
if(catalog_controller_api_order__edit){
$log = fopen(DIR_LOGS . 'catalog_controller_api_order__edit.log', 'a');
fwrite($log,' $json )' . print_r($json,true) . '==' . chr(10) . chr(13));
fwrite($log,' $products = $this->cart->getProducts(); )' . print_r($products,true) . '==' . chr(10) . chr(13));
fclose($log);}

				foreach ($products as $product) {
					$product_total = 0;

					foreach ($products as $product_2) {
						if ($product_2['product_id'] == $product['product_id']) {
							$product_total += $product_2['quantity'];
						}
					}

					if ($product['minimum'] > $product_total) {
						$json['error'] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);

						break;
					}
//SAVIN 20220901 beg
        $stock = $product['stock'] ? true : !(!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning'));
if(catalog_controller_api_order__edit){
$log = fopen(DIR_LOGS . 'catalog_controller_api_order__edit.log', 'a');
fwrite($log,' ONE $stock = ($product[stock] ? true : && !(!$this->config->get(config_stock_checkout) || $this->config->get(config_stock_warning));' . '==' . chr(10) . chr(13));
fwrite($log,' $stock = ('.print_r($product['stock'],true).') ELSE !(!'.$this->config->get('config_stock_checkout').' || '.$this->config->get('config_stock_warning').'); ) ' . print_r($stock,true) . '==' . chr(10) . chr(13));
fclose($log);}

        if (!$stock && !empty($order_products) && isset($order_products[$product['product_id']][$product['location_id']]) && isset($order_products[$product['product_id']][$product['location_id']]['location_id']) && isset($order_products[$product['product_id']][$product['location_id']]['quantity']) && isset($product['stock_quantity']) && isset($product['location_id']) && $order_products[$product['product_id']][$product['location_id']]['location_id'] == $product['location_id']  ){
            if ($product['quantity'] <= $order_products[$product['product_id']][$product['location_id']]['quantity']){
              if($quantity_enabled ){
                $stock = ($product['stock_quantity'] >= $product['quantity']) ? true : !(!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning'));
if(catalog_controller_api_order__edit){
$log = fopen(DIR_LOGS . 'catalog_controller_api_order__edit.log', 'a');
fwrite($log,' if($quantity_enabled) $stock = ($product[stock_quantity] >= $product[quantity]) ? true : !(!$this->config->get(config_stock_checkout) || $this->config->get(config_stock_warning));' . '==' . chr(10) . chr(13));
fwrite($log,' ('.$product['stock_quantity'].' >= ('.$product['quantity'].') ? true : !(!'.$this->config->get('config_stock_checkout').' || '.$this->config->get('config_stock_warning').'); )' . print_r($stock,true) . '==' . chr(10) . chr(13));
fclose($log);}
                
              } else {
                $stock = true;
if(catalog_controller_api_order__edit){
$log = fopen(DIR_LOGS . 'catalog_controller_api_order__edit.log', 'a');
fwrite($log,' if($quantity_enabled) ELSE )' . print_r($stock,true) . '==' . chr(10) . chr(13));
fclose($log);}
              }
if(catalog_controller_api_order__edit){
$log = fopen(DIR_LOGS . 'catalog_controller_api_order__edit.log', 'a');
fwrite($log,' if ($product[quantity] <= $order_products[$product[product_id]][$product[location_id]][quantity]) )' . '==' . chr(10) . chr(13));
fwrite($log,' if ('.$product['quantity'].' <= '.$order_products[$product['product_id']][$product['location_id']]['quantity'].'))' . print_r('  $stock = true;',true) . '==' . chr(10) . chr(13));
fclose($log);}
            } else {
              $stock = ($product['stock_quantity'] >= ($product['quantity'] - $order_products[$product['product_id']][$product['location_id']]['quantity'])) ? true : !(!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning'));
if(catalog_controller_api_order__edit){
$log = fopen(DIR_LOGS . 'catalog_controller_api_order__edit.log', 'a');
fwrite($log,' ELSE $stock = ($product[stock_quantity] >= ($product[quantity] - $order_products[$product[product_id]][$product[location_id]][quantity])) ? true : !(!$this->config->get(config_stock_checkout) || $this->config->get(config_stock_warning));' . '==' . chr(10) . chr(13));
fwrite($log,' ('.$product['stock_quantity'].' >= ('.$product['quantity'].' - '.$order_products[$product['product_id']][$product['location_id']]['quantity'].')) ? true : !(!'.$this->config->get('config_stock_checkout').' || '.$this->config->get('config_stock_warning').'); )' . print_r($stock,true) . '==' . chr(10) . chr(13));
fclose($log);}
            }
        }
        $stock_total = $stock_total && $stock; 

				$json['products'][] = array(
						'product_id' => $product['product_id'],
						'name'       => $product['name'],
						'model'      => $product['model'],
						'stock'      => $stock,
						'download'   => $product['download'],
						'quantity'   => $product['quantity'],
						'subtract'   => $product['subtract'],
            'location_id'=> $product['location_id'], // SAVIN 20220730
						'price'      => $product['price'],
						'total'      => $product['total'],
						'tax'        => $this->tax->getTax($product['price'], $product['tax_class_id']),
						'reward'     => $product['reward']
					);

//SAVIN 20220901 end

          
				}

if(catalog_controller_api_order__edit){
$log = fopen(DIR_LOGS . 'catalog_controller_api_order__edit.log', 'a');
fwrite($log,' TWO $json )' . print_r($json,true) . '==' . chr(10) . chr(13));
fwrite($log,' $stock_total )' . print_r($stock_total,true) . '==' . chr(10) . chr(13));
fclose($log);}
// SAVIN 20220901 beg   
      if(isset($json['error']) && ($json['error'] == $this->language->get('error_stock')) && $stock_total){
        unset($json['error']);
      }       
// SAVIN 20220901 end          
if(catalog_controller_api_order__edit){
$log = fopen(DIR_LOGS . 'catalog_controller_api_order__edit.log', 'a');
fwrite($log,' THREE $json )' . print_r($json,true) . '==' . chr(10) . chr(13));
fclose($log);}

				if (!isset($json['error'])) {
					$json['success'] = $this->language->get('text_success');
					
					$order_data = array();

					// Store Details
					$order_data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
					$order_data['store_id'] = $this->config->get('config_store_id');
					$order_data['store_name'] = $this->config->get('config_name');
					$order_data['store_url'] = $this->config->get('config_url');

					// Customer Details
					$order_data['customer_id'] = $this->session->data['customer']['customer_id'];
					$order_data['customer_group_id'] = $this->session->data['customer']['customer_group_id'];
					$order_data['firstname'] = $this->session->data['customer']['firstname'];
					$order_data['lastname'] = $this->session->data['customer']['lastname'];
					$order_data['email'] = $this->session->data['customer']['email'];
					$order_data['telephone'] = $this->session->data['customer']['telephone'];
					$order_data['custom_field'] = $this->session->data['customer']['custom_field'];

					// Payment Details
					$order_data['payment_firstname'] = $this->session->data['payment_address']['firstname'];
					$order_data['payment_lastname'] = $this->session->data['payment_address']['lastname'];
					$order_data['payment_company'] = $this->session->data['payment_address']['company'];
					$order_data['payment_address_1'] = $this->session->data['payment_address']['address_1'];
					$order_data['payment_address_2'] = $this->session->data['payment_address']['address_2'];
					$order_data['payment_city'] = $this->session->data['payment_address']['city'];
					$order_data['payment_postcode'] = $this->session->data['payment_address']['postcode'];
					$order_data['payment_zone'] = $this->session->data['payment_address']['zone'];
					$order_data['payment_zone_id'] = $this->session->data['payment_address']['zone_id'];
					$order_data['payment_country'] = $this->session->data['payment_address']['country'];
					$order_data['payment_country_id'] = $this->session->data['payment_address']['country_id'];
					$order_data['payment_address_format'] = $this->session->data['payment_address']['address_format'];
					$order_data['payment_custom_field'] = $this->session->data['payment_address']['custom_field'];

					if (isset($this->session->data['payment_method']['title'])) {
						$order_data['payment_method'] = $this->session->data['payment_method']['title'];
					} else {
						$order_data['payment_method'] = '';
					}

					if (isset($this->session->data['payment_method']['code'])) {
						$order_data['payment_code'] = $this->session->data['payment_method']['code'];
					} else {
						$order_data['payment_code'] = '';
					}

					// Shipping Details
					if ($this->cart->hasShipping()) {
						$order_data['shipping_firstname'] = $this->session->data['shipping_address']['firstname'];
						$order_data['shipping_lastname'] = $this->session->data['shipping_address']['lastname'];
						$order_data['shipping_company'] = $this->session->data['shipping_address']['company'];
						$order_data['shipping_address_1'] = $this->session->data['shipping_address']['address_1'];
						$order_data['shipping_address_2'] = $this->session->data['shipping_address']['address_2'];
						$order_data['shipping_city'] = $this->session->data['shipping_address']['city'];
						$order_data['shipping_postcode'] = $this->session->data['shipping_address']['postcode'];
						$order_data['shipping_zone'] = $this->session->data['shipping_address']['zone'];
						$order_data['shipping_zone_id'] = $this->session->data['shipping_address']['zone_id'];
						$order_data['shipping_country'] = $this->session->data['shipping_address']['country'];
						$order_data['shipping_country_id'] = $this->session->data['shipping_address']['country_id'];
						$order_data['shipping_address_format'] = $this->session->data['shipping_address']['address_format'];
						$order_data['shipping_custom_field'] = $this->session->data['shipping_address']['custom_field'];

						if (isset($this->session->data['shipping_method']['title'])) {
							$order_data['shipping_method'] = $this->session->data['shipping_method']['title'];
						} else {
							$order_data['shipping_method'] = '';
						}

						if (isset($this->session->data['shipping_method']['code'])) {
							$order_data['shipping_code'] = $this->session->data['shipping_method']['code'];
						} else {
							$order_data['shipping_code'] = '';
						}
					} else {
						$order_data['shipping_firstname'] = '';
						$order_data['shipping_lastname'] = '';
						$order_data['shipping_company'] = '';
						$order_data['shipping_address_1'] = '';
						$order_data['shipping_address_2'] = '';
						$order_data['shipping_city'] = '';
						$order_data['shipping_postcode'] = '';
						$order_data['shipping_zone'] = '';
						$order_data['shipping_zone_id'] = '';
						$order_data['shipping_country'] = '';
						$order_data['shipping_country_id'] = '';
						$order_data['shipping_address_format'] = '';
						$order_data['shipping_custom_field'] = array();
						$order_data['shipping_method'] = '';
						$order_data['shipping_code'] = '';
					}

					// Products
					$order_data['products'] = array();

					foreach ($this->cart->getProducts() as $product) {
						$option_data = array();

						foreach ($product['option'] as $option) {
							$option_data[] = array(
								'product_option_id'       => $option['product_option_id'],
								'product_option_value_id' => $option['product_option_value_id'],
								'option_id'               => $option['option_id'],
								'option_value_id'         => $option['option_value_id'],
								'name'                    => $option['name'],
								'value'                   => $option['value'],
								'type'                    => $option['type']
							);
						}

						$order_data['products'][] = array(
							'product_id' => $product['product_id'],
							'name'       => $product['name'],
							'model'      => $product['model'],
							'option'     => $option_data,
							'download'   => $product['download'],
							'quantity'   => $product['quantity'],
							'subtract'   => $product['subtract'],
              'location_id'=> $product['location_id'], // SAVIN 20220730
							'price'      => $product['price'],
							'total'      => $product['total'],
							'tax'        => $this->tax->getTax($product['price'], $product['tax_class_id']),
							'reward'     => $product['reward']
						);
					}

					// Gift Voucher
					$order_data['vouchers'] = array();

					if (!empty($this->session->data['vouchers'])) {
						foreach ($this->session->data['vouchers'] as $voucher) {
							$order_data['vouchers'][] = array(
								'description'      => $voucher['description'],
								'code'             => token(10),
								'to_name'          => $voucher['to_name'],
								'to_email'         => $voucher['to_email'],
								'from_name'        => $voucher['from_name'],
								'from_email'       => $voucher['from_email'],
								'voucher_theme_id' => $voucher['voucher_theme_id'],
								'message'          => $voucher['message'],
								'amount'           => $voucher['amount']
							);
						}
					}

					// Order Totals
					$this->load->model('setting/extension');

					$totals = array();
					$taxes = $this->cart->getTaxes();
					$total = 0;
					
					// Because __call can not keep var references so we put them into an array. 
					$total_data = array(
						'totals' => &$totals,
						'taxes'  => &$taxes,
						'total'  => &$total
					);
			
					$sort_order = array();

					$results = $this->model_setting_extension->getExtensions('total');

					foreach ($results as $key => $value) {
						$sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
					}

					array_multisort($sort_order, SORT_ASC, $results);

					foreach ($results as $result) {
						if ($this->config->get('total_' . $result['code'] . '_status')) {
							$this->load->model('extension/total/' . $result['code']);
							
							// We have to put the totals in an array so that they pass by reference.
							$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
						}
					}

					$sort_order = array();

					foreach ($total_data['totals'] as $key => $value) {
						$sort_order[$key] = $value['sort_order'];
					}

					array_multisort($sort_order, SORT_ASC, $total_data['totals']);

					$order_data = array_merge($order_data, $total_data);

					if (isset($this->request->post['comment'])) {
						$order_data['comment'] = $this->request->post['comment'];
					} else {
						$order_data['comment'] = '';
					}

					if (isset($this->request->post['affiliate_id'])) {
						$subtotal = $this->cart->getSubTotal();

						// Affiliate
						$this->load->model('account/customer');

						$affiliate_info = $this->model_account_customer->getAffiliate($this->request->post['affiliate_id']);

						if ($affiliate_info) {
							$order_data['affiliate_id'] = $affiliate_info['customer_id'];
							$order_data['commission'] = ($subtotal / 100) * $affiliate_info['commission'];
						} else {
							$order_data['affiliate_id'] = 0;
							$order_data['commission'] = 0;
						}
					} else {
						$order_data['affiliate_id'] = 0;
						$order_data['commission'] = 0;
					}

					$this->model_checkout_order->editOrder($order_id, $order_data);

					// Set the order history
					if (isset($this->request->post['order_status_id'])) {
						$order_status_id = $this->request->post['order_status_id'];
					} else {
						$order_status_id = $this->config->get('config_order_status_id');
					}
					
					$this->model_checkout_order->addOrderHistory($order_id, $order_status_id);
				}
			} else {
				$json['error'] = $this->language->get('error_not_found');
			}
		}
if(catalog_controller_api_order__edit){
$log = fopen(DIR_LOGS . 'catalog_controller_api_order__edit.log', 'a');
fwrite($log,' RETURN $json )' . print_r($json,true) . '==' . chr(10) . chr(13));
fclose($log);}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function delete() {
		$this->load->language('api/order');

		$json = array();

		if (!isset($this->session->data['api_id'])) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			$this->load->model('checkout/order');

			if (isset($this->request->get['order_id'])) {
				$order_id = $this->request->get['order_id'];
			} else {
				$order_id = 0;
			}

			$order_info = $this->model_checkout_order->getOrder($order_id);

			if ($order_info) {
				$this->model_checkout_order->deleteOrder($order_id);

				$json['success'] = $this->language->get('text_success');
			} else {
				$json['error'] = $this->language->get('error_not_found');
			}
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function info() {
		$this->load->language('api/order');

		$json = array();

		if (!isset($this->session->data['api_id'])) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			$this->load->model('checkout/order');

			if (isset($this->request->get['order_id'])) {
				$order_id = $this->request->get['order_id'];
			} else {
				$order_id = 0;
			}

			$order_info = $this->model_checkout_order->getOrder($order_id);

			if ($order_info) {
				$json['order'] = $order_info;

				$json['success'] = $this->language->get('text_success');
			} else {
				$json['error'] = $this->language->get('error_not_found');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function history() {
if(catalog_controller_api_order__history){
$log = fopen(DIR_LOGS . 'catalog_controller_api_order__history.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,'$this->session->data ) ' . print_r($this->session->data,true) . chr(13) . chr(13));
fwrite($log,'$this->request->get ) ' . print_r($this->request->get,true) . chr(13) . chr(13));
fwrite($log,'$this->request->post ) ' . print_r($this->request->post,true) . chr(13) . chr(13));
fclose($log);}
		$this->load->language('api/order');

		$json = array();
    $stock_total = true; // SAVIN 20220901
    if (isset($this->request->get['store_id'])){
      $store_id = $this->request->get['store_id'];
    } else { 
      $store_id = $this->config->get('config_store_id');
    }
		if (!isset($this->session->data['api_id'])) {
			$json['error'] = $this->language->get('error_permission');
		} else {
//SAVIN 20220901 beg
//        $hasStock = $this->cart->hasStock();
        $hasStock = false;
if(catalog_controller_api_order__history){
$log = fopen(DIR_LOGS . 'catalog_controller_api_order__history.log', 'a');
fwrite($log,' $hasStock )' . print_r($hasStock,true) . '==' . chr(10) . chr(13));
fwrite($log,' config_stock_checkout )' . print_r($this->config->get('config_stock_checkout'),true) . '==' . chr(10) . chr(13));
fwrite($log,' config_stock_warning )' . print_r($this->config->get('config_stock_warning'),true) . '==' . chr(10) . chr(13));
fclose($log);}
			// Stock
			if (!$hasStock && (!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning'))) {
				$json['error'] = $this->language->get('error_stock');
        if (isset($this->request->get['order_id'])){
            $order_id = $this->request->get['order_id'];
            $order = $this->getOrder($order_id);
if(catalog_controller_api_order__history){
$log = fopen(DIR_LOGS . 'catalog_controller_api_order__history.log', 'a');
fwrite($log,'**********************************' . chr(13) . chr(13));
fwrite($log,'$order = $this->getOrder('.$order_id.'); ) ' . print_r($order,true) . chr(13) . chr(13));
fclose($log);}
            $order_status_old = $order['order_status_id'];
				// Stock subtraction
				    $order_products = $this->getOrderProducts($order_id);
if(catalog_controller_api_order__history){
$log = fopen(DIR_LOGS . 'catalog_controller_api_order__history.log', 'a');
fwrite($log,'**********************************' . chr(13) . chr(13));
fwrite($log,'$order_products = $this->getOrderProducts('.$order_id.'); ) ' . print_r($order_products,true) . chr(13) . chr(13));
fclose($log);}
        }
      }

					// Set the order history
					if (isset($this->request->post['order_status_id'])) {
						$order_status_id = $this->request->post['order_status_id'];
					} else {
						$order_status_id = $this->config->get('config_order_status_id');
					}
          $quantity_enabled = in_array($order_status_id, array_merge($this->config->get('config_processing_status'), $this->config->get('config_complete_status')));
          $quantity_old_enabled = in_array($order_status_old, array_merge($this->config->get('config_processing_status'), $this->config->get('config_complete_status')));
if(catalog_controller_api_order__history){
$log = fopen(DIR_LOGS . 'catalog_controller_api_order__history.log', 'a');
fwrite($log,' $order_status_id )' . print_r($order_status_id,true) . '==' . chr(10) . chr(13));
fwrite($log,' $quantity_enabled )' . print_r($quantity_enabled,true) . '==' . chr(10) . chr(13));
fwrite($log,' $order_status_old )' . print_r($order_status_old,true) . '==' . chr(10) . chr(13));
fwrite($log,' $quantity_old_enabled )' . print_r($quantity_old_enabled,true) . '==' . chr(10) . chr(13));
fwrite($log,' array_merge(config_processing_status + config_complete_status) )' . print_r(array_merge($this->config->get('config_processing_status'), $this->config->get('config_complete_status')),true) . '==' . chr(10) . chr(13));
fclose($log);}

//				$products = $this->cart->getProducts();
				$products = $order_products;
if(catalog_controller_api_order__history){
$log = fopen(DIR_LOGS . 'catalog_controller_api_order__history.log', 'a');
fwrite($log,' $json )' . print_r($json,true) . '==' . chr(10) . chr(13));
fwrite($log,' $products = $this->cart->getProducts(); )' . print_r($products,true) . '==' . chr(10) . chr(13));
fclose($log);}

				foreach ($products as $locations) {
				foreach ($locations as $product){  
          $stock = true;
          $cart = $product;
          $sql = "SELECT p2s.*, p.*, pd.*, (SELECT pp.price FROM " . DB_PREFIX . "product_price pp WHERE pp.product_id = p.product_id  AND pp.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "'  AND pp.location_id = '" . (int)$cart['location_id'] . "') AS price, IF(" . (empty($cart['location_id']) ? '0' : $cart['location_id']) . " = 0, (SELECT SUM(ps.quantity) FROM " . DB_PREFIX . "product_stock ps WHERE ps.product_id = p.product_id), (SELECT ps.quantity FROM " . DB_PREFIX . "product_stock ps WHERE ps.product_id = p.product_id AND ps.location_id = '" . (int)$cart['location_id'] . "' )) AS quantity,  IF(" . (empty($cart['location_id']) ? '0' : $cart['location_id']) . " = 0, 'AutoSelect', (SELECT IF(ld.city IS NOT NULL AND ld.name IS NOT NULL, CONCAT(ld.city,', ',ld.name),IF(ld.city IS NOT NULL, ld.city, ld.name)) FROM " . DB_PREFIX . "location_description ld WHERE ld.language_id = '" . (int)$this->config->get('config_language_id') . "' AND  ld.location_id = " . (empty($cart['location_id']) ? '0' : $cart['location_id']) . ") ) as location FROM " . DB_PREFIX . "product_to_store p2s LEFT JOIN " . DB_PREFIX . "product p ON (p2s.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p2s.store_id = '" . (int)$store_id . "' AND p2s.product_id = '" . (int)$cart['product_id'] . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.date_available <= NOW() AND p.status = '1'";
if(catalog_controller_api_order__history){
$log = fopen(DIR_LOGS . 'catalog_controller_api_order__history.log', 'a');
fwrite($log,' $sql )' . print_r($sql,true) . '==' . chr(10) . chr(13));
fclose($log);}
			   $product_query = $this->db->query($sql);
if(catalog_controller_api_order__history){
$log = fopen(DIR_LOGS . 'catalog_controller_api_order__history.log', 'a');
fwrite($log,' $product_query )' . print_r($product_query,true) . '==' . chr(10) . chr(13));
fwrite($log,' $cart )' . print_r($cart,true) . '==' . chr(10) . chr(13));
fclose($log);}
         $stock = ($product_query->row['quantity'] >= $cart['quantity']) ? true : !(!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning'));
if(catalog_controller_api_order__history){
$log = fopen(DIR_LOGS . 'catalog_controller_api_order__history.log', 'a');
fwrite($log,' ONE $stock = (($product_query->row[quantity] >= $cart[quantity]) ? true : && !(!$this->config->get(config_stock_checkout) || $this->config->get(config_stock_warning));' . '==' . chr(10) . chr(13));
fwrite($log,' $stock = ('.print_r($product_query->row['quantity'],true).' >= '.print_r($cart['quantity'],true).' ) ? true : !(!'.$this->config->get('config_stock_checkout').' || '.$this->config->get('config_stock_warning').'); ) ' . print_r($stock,true) . '==' . chr(10) . chr(13));
fclose($log);}

         if (!$stock && !empty($order_products) ){
//            if ($product['quantity'] <= $order_products[$product['product_id']][$product['location_id']]['quantity']){
              if($quantity_enabled && !$quantity_old_enabled ){
                $stock = ($product_query->row['quantity'] >= $product['quantity']) ? true : !(!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning'));
if(catalog_controller_api_order__history){
$log = fopen(DIR_LOGS . 'catalog_controller_api_order__history.log', 'a');
fwrite($log,' if($quantity_enabled) $stock = ($product_query->row[quantity] >= $product[quantity]) ? true : !(!$this->config->get(config_stock_checkout) || $this->config->get(config_stock_warning));' . '==' . chr(10) . chr(13));
fwrite($log,' ('.$product_query->row['quantity'].' >= ('.$product['quantity'].') ? true : !(!'.$this->config->get('config_stock_checkout').' || '.$this->config->get('config_stock_warning').'); )' . print_r($stock,true) . '==' . chr(10) . chr(13));
fclose($log);}
                
              } else {
                $stock = true;
if(catalog_controller_api_order__history){
$log = fopen(DIR_LOGS . 'catalog_controller_api_order__history.log', 'a');
fwrite($log,' if($quantity_enabled) ELSE )' . print_r($stock,true) . '==' . chr(10) . chr(13));
fclose($log);}
              }
//            } else {
//              $stock = ($product['stock_quantity'] >= ($product['quantity'] - $order_products[$product['product_id']][$product['location_id']]['quantity'])) ? true : !(!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning'));
//if(catalog_controller_api_order__history){
//$log = fopen(DIR_LOGS . 'catalog_controller_api_order__history.log', 'a');
//fwrite($log,' ELSE $stock = ($product[stock_quantity] >= ($product[quantity] - $order_products[$product[product_id]][$product[location_id]][quantity])) ? true : !(!$this->config->get(config_stock_checkout) || $this->config->get(config_stock_warning));' . '==' . chr(10) . chr(13));
//fwrite($log,' ('.$product['stock_quantity'].' >= ('.$product['quantity'].' - '.$order_products[$product['product_id']][$product['location_id']]['quantity'].')) ? true : !(!'.$this->config->get('config_stock_checkout').' || '.$this->config->get('config_stock_warning').'); )' . print_r($stock,true) . '==' . chr(10) . chr(13));
//fclose($log);}
//            }
        } 
        $stock_total = $stock_total && $stock; 

				$json['products'][] = array(
					'cart_id'    => $product['order_id'],
					'product_id' => $product['product_id'],
					'name'       => $product['name'],
					'model'      => $product['model'],
				//	'option'     => $option_data,
					'quantity'   => $product['quantity'],
					'location_id'=> $product['location_id'],
//					'location'   => $product['location'],
          'stock'      => $stock,
//					'shipping'   => $product['shipping'],
					'price'      => $product['price'],
					'total'      => $product['total'],
					'reward'     => $product['reward']
				);

          
				}   }

if(catalog_controller_api_order__history){
$log = fopen(DIR_LOGS . 'catalog_controller_api_order__history.log', 'a');
fwrite($log,' TWO $json )' . print_r($json,true) . '==' . chr(10) . chr(13));
fwrite($log,' $stock_total )' . print_r($stock_total,true) . '==' . chr(10) . chr(13));
fclose($log);}
      if(isset($json['error']) && ($json['error'] == $this->language->get('error_stock')) && $stock_total){
        unset($json['error']);
      }       
if(catalog_controller_api_order__history){
$log = fopen(DIR_LOGS . 'catalog_controller_api_order__history.log', 'a');
fwrite($log,' THREE $json )' . print_r($json,true) . '==' . chr(10) . chr(13));
fclose($log);}
 
     if(!isset($json['error'])){
// SAVIN 20220901 end          



     
			// Add keys for missing post vars
			$keys = array(
				'order_status_id',
				'notify',
				'override',
				'comment'
			);

			foreach ($keys as $key) {
				if (!isset($this->request->post[$key])) {
					$this->request->post[$key] = '';
				}
			}

			$this->load->model('checkout/order');

			if (isset($this->request->get['order_id'])) {
				$order_id = $this->request->get['order_id'];
			} else {
				$order_id = 0;
			}

			$order_info = $this->model_checkout_order->getOrder($order_id);
 
			if ($order_info) {
				$this->model_checkout_order->addOrderHistory($order_id, $this->request->post['order_status_id'], $this->request->post['comment'], $this->request->post['notify'], $this->request->post['override']);

				$json['success'] = $this->language->get('text_success');
			} else {
				$json['error'] = $this->language->get('error_not_found');
			}
		}
} // SAVIN 20220901 
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
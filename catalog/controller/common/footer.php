<?php
class ControllerCommonFooter extends Controller {
	public function index() {
		$this->load->language('common/footer');

		$this->load->model('catalog/information');

		$this->load->model('localisation/location');

		$data['informations'] = array();

		foreach ($this->model_catalog_information->getInformations() as $result) {
			if ($result['bottom']) {
				$data['informations'][] = array(
					'title' => $result['title'],
					'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
				);
			}
		}

		$data['contact'] = $this->url->link('information/contact');
		$data['return'] = $this->url->link('account/return/add', '', true);
		$data['sitemap'] = $this->url->link('information/sitemap');
		$data['tracking'] = $this->url->link('information/tracking');
		$data['manufacturer'] = $this->url->link('product/manufacturer');
		$data['voucher'] = $this->url->link('account/voucher', '', true);
		$data['affiliate'] = $this->url->link('affiliate/login', '', true);
		$data['special'] = $this->url->link('product/special');
		$data['account'] = $this->url->link('account/account', '', true);
		$data['order'] = $this->url->link('account/order', '', true);
		$data['wishlist'] = $this->url->link('account/wishlist', '', true);
		$data['newsletter'] = $this->url->link('account/newsletter', '', true);

		$data['powered'] = sprintf($this->language->get('text_powered'), $this->config->get('config_name'), date('Y', time()));

		$data['text_contacts'] =  $this->language->get('text_contacts');
    $data['config_startjivo'] = $this->config->get('config_startjivo');

		$data['location_list'] = array();
		$cities = [];

		foreach ($this->model_localisation_location->getLocationList($this->config->get('config_language_id')) as $result) {
			if ($result['parent_id'] == 0) {
				$shops_qnt = 0;

				$cities[ $result['city'] ] = [
                    'phone' => str_replace(' ', '', $result['fax']),
                    'fax' => $result['fax'],
                    'viber' => $result['viber'],
                    'telegram' => $result['telegram'],
                    'whatsapp' => $result['whatsapp'],
                    'skype' => $result['skype'],
                    'shops' => [],
                    'shops_qnt' => $shops_qnt
                ];
            } 

            $phones = array();
            foreach ($this->model_localisation_location->getStuffList($result['location_id'], $this->config->get('config_language_id')) as $res) {	
                foreach ($this->model_localisation_location->getStuffPhones($res['employee_id']) as $rest) {
            	    $phones[] = array(
				        'phone' => $rest['telephone'],
				        'phone_link' => preg_replace('/[^\d+]/', '', $rest['telephone'])
				    );	
            	}
            }

            $cities[ $result['city'] ]['shops'][] = [
                'name' => $result['name'],
                'address' => $result['address'],
                'phone' => $phones,
                'fax_link' => preg_replace('/[^\d+]/', '', $result['fax']),
			    'fax' => $result['fax']
            ];

            $shops_qnt = $shops_qnt + 1;

            $cities[ $result['city'] ]['shops_qnt'] = $shops_qnt;
		}
		$data['location_list'] = $cities;

		// Whos Online
		if ($this->config->get('config_customer_online')) {
			$this->load->model('tool/online');

			if (isset($this->request->server['REMOTE_ADDR'])) {
				$ip = $this->request->server['REMOTE_ADDR'];
			} else {
				$ip = '';
			}

			if (isset($this->request->server['HTTP_HOST']) && isset($this->request->server['REQUEST_URI'])) {
				$url = ($this->request->server['HTTPS'] ? 'https://' : 'http://') . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI'];
			} else {
				$url = '';
			}

			if (isset($this->request->server['HTTP_REFERER'])) {
				$referer = $this->request->server['HTTP_REFERER'];
			} else {
				$referer = '';
			}

			$this->model_tool_online->addOnline($ip, $this->customer->getId(), $url, $referer);
		}

		$data['scripts'] = $this->document->getScripts('footer');
if(catalog_controller_common_footer__index){
$log = fopen(DIR_LOGS . 'catalog_controller_common_footer__index.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
//fwrite($log,' $this->config->get(config_startjivo) )' . print_r($this->config->get('config_startjivo'), true) . '==' . chr(10) . chr(13));
fwrite($log,' $data )' . print_r($data, true) . '==' . chr(10) . chr(13));
fclose($log);}
		
		return $this->load->view('common/footer', $data);
	}
	
	public function callback() {
if(catalog_controller_common_footer__callback){
$log = fopen(DIR_LOGS . 'catalog_controller_common_footer__callback.log', 'w');
fwrite($log,'>> ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,' $this->request->get )' . print_r($this->request->get, true) . '==' . chr(10) . chr(13));
fwrite($log,' $this->request->post )' . print_r($this->request->post, true) . '==' . chr(10) . chr(13));
fwrite($log,' $this->session->data )' . print_r($this->session->data, true) . '==' . chr(10) . chr(13));
fclose($log);}
		$this->load->language('common/footer');
		$this->load->language('mail/callback');

		$json = array();
		$data = array();
		if (isset($this->request->post['callback-name']) && !empty($this->request->post['callback-name'])) {
			$data['name'] = $this->request->post['callback-name'];
		} else {
			$json['error']['warning'] = sprintf($this->language->get('error_required'),$this->language->get('text_message_name'));
			$json['error']['name'] = 'callback-name'; 
		}
		if (!$json) {
			if (isset($this->request->post['callback-phone']) && !empty($this->request->post['callback-phone'])) {
				$data['phone'] = $this->request->post['callback-phone'];
			} else {
				$json['error']['warning'] = sprintf($this->language->get('error_required'),$this->language->get('text_message_phone'));
				$json['error']['name'] = 'callback-phone';
			}
		}
		if (isset($this->request->post['callback-message']) && !empty($this->request->post['callback-message'])) {
			$data['message'] = $this->request->post['callback-message'];
		} else {
			$data['message'] = '';
		}

		if (isset($this->request->post['letter_product_id']) && !empty($this->request->post['letter_product_id'])) {
			$product_id = $this->request->post['letter_product_id'];
		} else {
			$product_id = '';
		}
		
		if (!$json) {
			$this->load->language('mail/callback');
			
			$data['text_subject'] = sprintf($this->language->get('text_subject'), html_entity_decode($this->request->post['callback-name'], ENT_QUOTES, 'UTF-8'));
			$data['text_thanks'] = $this->language->get('text_thanks');
			$data['text_firstname'] = $this->language->get('text_firstname');
			$data['text_lastname'] = $this->language->get('text_lastname');
			$data['text_email'] = $this->language->get('text_email');
			$data['text_telephone'] = $this->language->get('text_telephone');

			$mail = new Mail($this->config->get('config_mail_engine'));
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

			// $mail->setTo($args[0]['email']);
			$mail->setTo($this->config->get('config_email'));
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$mail->setSubject($data['text_subject']);
			
	if(!empty($product_id)){
		$this->load->model('catalog/product');

		$product_info = $this->model_catalog_product->getProduct($product_id);

		$data['href'] = $this->url->link('product/product', 'product_id=' . $product_id);
		$data['product_id'] = $product_id;
		$data['model'] = $product_info['model'];
		$data['sku'] = $product_info['sku'];
		$data['description'] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');
		$data['product_name'] = $product_info['name'];

		if ($product_info['quantity'] <= 0) {
			$data['stock'] = $product_info['stock_status'];
		} elseif ($this->config->get('config_stock_display')) {
			$data['stock'] = $product_info['quantity'];
		} else {
			$data['stock'] = 'text_instock'; //$this->language->get('text_instock');
		}

		$this->load->model('tool/image');

		if ($product_info['image']) {
			$data['image'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height'));
		} else {
			$data['image'] = '';
		}

		if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
			$data['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
		} else {
			$data['price'] = false;
		}

		if ((float)$product_info['special']) {
			$data['special'] = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
		} else {
			$data['special'] = false;
		}

		if ($this->config->get('config_tax')) {
			$data['tax'] = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
		} else {
			$data['tax'] = false;
		}

				
	}
if(catalog_controller_common_footer__callback){
$log = fopen(DIR_LOGS . 'catalog_controller_common_footer__callback.log', 'a');
fwrite($log,' $product_id )' . print_r($product_id, true) . '==' . chr(10) . chr(13));
fwrite($log,' $data )' . print_r($data, true) . '==' . chr(10) . chr(13));
fclose($log);}
			
			
			$mail->setHtml($this->load->view('mail/callback', $data));
			$mail->send(); 
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
		
	}

	public function director_message() {
		$this->load->language('common/footer');

		$json = array();
		$data = array();
		if (isset($this->request->post['director-message-name']) && !empty($this->request->post['director-message-name'])) {
			$data['name'] = $this->request->post['director-message-name'];
		} else {
			$json['error']['warning'] = sprintf($this->language->get('error_required'),$this->language->get('text_message_name'));
			$json['error']['name'] = 'director-message-name'; 
		}
		if (!$json) {
			if (isset($this->request->post['director-message-message']) && !empty($this->request->post['director-message-message'])) {
				$data['message'] = $this->request->post['director-message-message'];
			} else {
				$json['error']['warning'] = sprintf($this->language->get('error_required'),$this->language->get('text_message'));
				$json['error']['name'] = 'director-message-message';
			}
		}
		if (isset($this->request->post['director-message-phone']) && !empty($this->request->post['director-message-phone'])) {
			$data['phone'] = $this->request->post['director-message-phone'];
		} else {
			$data['phone'] = '';
		}
		if (isset($this->request->post['director-message-email']) && !empty($this->request->post['director-message-email'])) {
			$data['email'] = $this->request->post['director-message-email'];
		} else {
			$data['email'] = '';
		}

		if (!$json) {
			$this->load->language('mail/director_message');
			
			$data['text_subject'] = sprintf($this->language->get('text_subject'), html_entity_decode($this->request->post['director-message-name'], ENT_QUOTES, 'UTF-8'));
			$data['text_thanks'] = $this->language->get('text_thanks');
			$data['text_firstname'] = $this->language->get('text_firstname');
			$data['text_lastname'] = $this->language->get('text_lastname');
			$data['text_email'] = $this->language->get('text_email');
			$data['text_telephone'] = $this->language->get('text_telephone');

			$mail = new Mail($this->config->get('config_mail_engine'));
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

			// $mail->setTo($args[0]['email']);
			$mail->setTo($this->config->get('config_email'));
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$mail->setSubject($data['text_subject']);
			$mail->setText($this->load->view('mail/director_message', $data));
			$mail->send(); 
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
		
	}
	
	
	public function carlinkshow() {
if(catalog_controller_common_footer__car_link__show){
$log = fopen(DIR_LOGS . 'catalog_controller_common_footer__car_link__show.log', 'w');
fwrite($log,'>> ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fclose($log);}
		$json = array();
		// $url = "https://cax.hc-cargo.com/sso/redirect.ashx?apikey=b302a509-8eba-4676-b743-37144a7147d3"; // test-key
		$url = "https://hc-cargo.com/sso/redirect.ashx?apikey=cfd56eea-128c-4cce-8cd6-9a754a795aec"; // buy-key
		// $url = "https://www.postindexapi.ru/json/308/308017.json";

		$curl = curl_init($url);
if(catalog_controller_common_footer__car_link__show){
$log = fopen(DIR_LOGS . 'catalog_controller_common_footer__car_link__show.log', 'a');
fwrite($log,'AFTER $curl = curl_init($url); )' . '==' . chr(10) . chr(13));
fclose($log);}
		curl_setopt($curl, CURLOPT_URL, $url);
if(catalog_controller_common_footer__car_link__show){
$log = fopen(DIR_LOGS . 'catalog_controller_common_footer__car_link__show.log', 'a');
fwrite($log,'AFTER curl_setopt($curl, CURLOPT_URL, $url); )' . '==' . chr(10) . chr(13));
fclose($log);}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
if(catalog_controller_common_footer__car_link__show){
$log = fopen(DIR_LOGS . 'catalog_controller_common_footer__car_link__show.log', 'a');
fwrite($log,'AFTER curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); )' . '==' . chr(10) . chr(13));
fclose($log);}

		$headers = array(
			"Accept: application/json",
		);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
if(catalog_controller_common_footer__car_link__show){
$log = fopen(DIR_LOGS . 'catalog_controller_common_footer__car_link__show.log', 'a');
fwrite($log,'AFTER curl_setopt($curl, CURLOPT_HTTPHEADER, $headers); )' . '==' . chr(10) . chr(13));
fclose($log);}
		//for debug only!
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
if(catalog_controller_common_footer__car_link__show){
$log = fopen(DIR_LOGS . 'catalog_controller_common_footer__car_link__show.log', 'a');
fwrite($log,'AFTER curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); )' . '==' . chr(10) . chr(13));
fclose($log);}
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
if(catalog_controller_common_footer__car_link__show){
$log = fopen(DIR_LOGS . 'catalog_controller_common_footer__car_link__show.log', 'a');
fwrite($log,'AFTER curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); )' . '==' . chr(10) . chr(13));
fclose($log);}

		$json = curl_exec($curl);
if(catalog_controller_common_footer__car_link__show){
$log = fopen(DIR_LOGS . 'catalog_controller_common_footer__car_link__show.log', 'a');
fwrite($log,'AFTER $json = curl_exec($curl); )' . '==' . chr(10) . chr(13));
fclose($log);}
		// var_dump($json);
if(catalog_controller_common_footer__car_link__show){
$log = fopen(DIR_LOGS . 'catalog_controller_common_footer__car_link__show.log', 'a');
fwrite($log,'RETURN $url )' . print_r($url, true) . '==' . chr(10) . chr(13));
fwrite($log,'RETURN $json )' . print_r($json, true) . '==' . chr(10) . chr(13));
fclose($log);}

	
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
		
	}

	

	
}

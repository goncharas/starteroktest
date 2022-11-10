<?php
// *	@source		See SOURCE.txt for source and other copyright.
// *	@license	GNU General Public License version 3; see LICENSE.txt

class ControllerCommonHeader extends Controller {
	public function index() {
if(catalog_controler_common_header__index){
$log = fopen(DIR_LOGS . 'catalog_controler_common_header__index.log', 'w');
fwrite($log,'**********************************' . chr(13) . chr(13));
$t = microtime(true);
$micro = sprintf("%06d",($t - floor($t)) * 1000000);
$d = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );
fwrite($log, $d->format("Y-m-d H:i:s.u") . '==' . chr(10) . chr(13));
fwrite($log,'$this->request->get) ' . print_r($this->request->get,true) . chr(13) . chr(13));
fwrite($log,'$this->request->post) ' . print_r($this->request->post,true) . chr(13) . chr(13));
fclose($log);}
		// Analytics
		$this->load->model('setting/extension');
        $this->load->model('localisation/location');
        $this->load->model('setting/module');
        
        $data['analytics_noscript'] = array();
		$data['analytics'] = array();

		$analytics = $this->model_setting_extension->getExtensions('analytics');

		foreach ($analytics as $analytic) {
			if ($this->config->get('analytics_' . $analytic['code'] . '_status')) {
				$data['analytics'][] = $this->load->controller('extension/analytics/' . $analytic['code'], $this->config->get('analytics_' . $analytic['code'] . '_status'));
			}
		}
        if (defined('GOOGLE_ANALYTICS')) {
            $data['analytics'][] = GOOGLE_ANALYTICS;
        }
        if (defined('GOOGLE_ANALYTICS_NOSCRIPT')) {
            $data['analytics_noscript'][] = GOOGLE_ANALYTICS_NOSCRIPT;
        }

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
			$this->document->addLink($server . 'image/' . $this->config->get('config_icon'), 'icon');
		}

		$data['title'] = $this->document->getTitle();
		$data['tc_og'] = $this->document->getTc_og();

		$data['base'] = $server;
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		$data['links'] = $this->document->getLinks();
		$data['robots'] = $this->document->getRobots();
		$data['styles'] = $this->document->getStyles();
		$data['scripts'] = $this->document->getScripts('header');
		$data['lang'] = $this->language->get('code');
		$data['direction'] = $this->language->get('direction');

		$data['name'] = $this->config->get('config_name');

		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = $server . 'image/' . $this->config->get('config_logo');
		} else {
			$data['logo'] = '';
		}

		$this->load->language('common/header');
		
		
		$host = isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1')) ? HTTPS_SERVER : HTTP_SERVER;
		if ($this->request->server['REQUEST_URI'] == '/') {
			$data['og_url'] = $this->url->link('common/home');
		} else {
			$data['og_url'] = $host . substr($this->request->server['REQUEST_URI'], 1, (strlen($this->request->server['REQUEST_URI'])-1));
		}
		
		$data['og_image'] = $this->document->getOgImage();
		


		// Wishlist
		if ($this->customer->isLogged()) {
			$this->load->model('account/wishlist');

			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), $this->model_account_wishlist->getTotalWishlist());
		} else {
			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
		}

		$data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', true), $this->customer->getFirstName(), $this->url->link('account/logout', '', true));
		
		$data['home'] = $this->url->link('common/home');
		$data['wishlist'] = $this->url->link('account/wishlist', '', true);
		$data['logged'] = $this->customer->isLogged();
		$data['account'] = $this->url->link('account/account', '', true);
		$data['register'] = $this->url->link('account/register', '', true);
		$data['login'] = $this->url->link('account/login', '', true);
		$data['order'] = $this->url->link('account/order', '', true);
		$data['transaction'] = $this->url->link('account/transaction', '', true);
		$data['download'] = $this->url->link('account/download', '', true);
		$data['logout'] = $this->url->link('account/logout', '', true);
		$data['shopping_cart'] = $this->url->link('checkout/cart');
		$data['checkout'] = $this->url->link('checkout/checkout', '', true);
		$data['contact'] = $this->url->link('information/contact');
		$data['optcustomer'] = $this->url->link('product/optcustomer');
		$data['telephone'] = $this->config->get('config_telephone');
		
		$data['language'] = $this->load->controller('common/language');
		$data['currency'] = $this->load->controller('common/currency');
if(catalog_controler_common_header__index){
$log = fopen(DIR_LOGS . 'catalog_controler_common_header__index.log', 'a');
fwrite($log,'++++++++++++++++++++++++++++++++++' . chr(13) . chr(13));
fwrite($log,' $data[language]) ' . print_r($data['language'],true) . chr(13) . chr(13));
fwrite($log,' $data[currency]) ' . print_r($data['currency'],true) . chr(13) . chr(13));
fclose($log);}
		// $data['currency'] = $this->load->controller('common/currency');
		if ($this->config->get('configblog_blog_menu')) {
			$data['blog_menu'] = $this->load->controller('blog/menu');
		} else {
			$data['blog_menu'] = '';
		}
		$data['search'] = $this->load->controller('common/search');
		$data['cart'] = $this->load->controller('common/cart');
		$data['menu'] = $this->load->controller('common/menu');

		$data['location_list'] = array();
		$cities = [];
// error_log('sava: config_language_id = '.$this->config->get('config_language_id'));
		foreach ($this->model_localisation_location->getLocationList($this->config->get('config_language_id')) as $result) {
			if ($result['parent_id'] == 0) {
				$shops_qnt = 0;

				$cities[ $result['city'] ] = [
                    'phone' => preg_replace('/[^\d+]/', '', $result['fax']),
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
			    'fax' => $result['fax']
            ];

            $shops_qnt = $shops_qnt + 1;

            $cities[ $result['city'] ]['shops_qnt'] = $shops_qnt;
		}
		$data['location_list'] = $cities;
		$data['text_sales_department_phone_link'] = preg_replace('/[^\d+]/', '', $this->language->get('text_sales_department_phone'));
        
        $data['logo_link'] = $this->url->link('common/home', '', $this->request->server['HTTPS']);
        //$data['mark_link'] = $this->url->link('search/index', '', $this->request->server['HTTPS']);
        $data['mark_link'] = ($this->registry->has('urlLang') && !empty($this->registry->get('urlLang'))? '/'.$this->registry->get('urlLang'):''). '/mark';
		
		{
			$part = explode('.', 'jetimpex_megamenu.51');

			if (isset($part[0]) && $this->config->get('module_' . $part[0] . '_status')) {
				$module_data = $this->load->controller('extension/module/' . $part[0]);

				if ($module_data) {
					$data_header_top['modules'][] = $module_data;
				}
			}

			if (isset($part[1])) {
				$setting_info = $this->model_setting_module->getModule($part[1]);

				if ($setting_info && $setting_info['status']) {
					$output = $this->load->controller('extension/module/' . $part[0], $setting_info);

					if ($output) {
						$data_header_top['modules'][] = $output;
					}
				}
			}
			if(!empty($data_header_top))
				$data['header_top']    = $this->load->view('common/position', $data_header_top);
		}
		
		// $data['header_top']    = $this->load->controller('common/header_top');

		
		{
			$part = explode('.', 'jetimpex_megamenu.50');

			if (isset($part[0]) && $this->config->get('module_' . $part[0] . '_status')) {
				$module_data = $this->load->controller('extension/module/' . $part[0]);

				if ($module_data) {
					$data_header_nav['modules'][] = $module_data;
				}
			}

			if (isset($part[1])) {
				$setting_info = $this->model_setting_module->getModule($part[1]);

				if ($setting_info && $setting_info['status']) {
					$output = $this->load->controller('extension/module/' . $part[0], $setting_info);

					if ($output) {
						$data_header_nav['modules'][] = $output;
					}
				}
			}
			if(!empty($data_header_nav))
				$data['header_nav']    = $this->load->view('common/position', $data_header_nav);
		}
if(catalog_controler_common_header__index){
$log = fopen(DIR_LOGS . 'catalog_controler_common_header__index.log', 'a');
fwrite($log,'++++++++++++++++++++++++++++++++++' . chr(13) . chr(13));
fwrite($log,'RETURN  $data) ' . print_r($data,true) . chr(13) . chr(13));
fclose($log);}
		return $this->load->view('common/header', $data);
	}
}
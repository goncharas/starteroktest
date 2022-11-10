<?php
class ControllerCatalogPartnerprice extends Controller {
	private $error = array();
	

	public function index() {
//		$this->load->language('catalog/category');

		$this->document->setTitle('Закачка цены');

		$this->getList();
	}

	protected function getList() {
if(admin_controller_catalog_partnerprice__getList){
$log = fopen(DIR_LOGS . 'admin_controller_catalog_partnerprice__getList.log', 'w');
fwrite($log,'**********************************' . chr(13) . chr(13));
$t = microtime(true);
$micro = sprintf("%06d",($t - floor($t)) * 1000000);
$d = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );
fwrite($log, $d->format("Y-m-d H:i:s.u") . '==' . chr(10) . chr(13));
fwrite($log,'$this->request->get) ' . print_r($this->request->get,true) . chr(13) . chr(13));
fwrite($log,'$this->request->post) ' . print_r($this->request->post,true) . chr(13) . chr(13));
fclose($log);}
    $data = array();
    $url = '';

		if (isset($this->request->post['location_id'])) {
				$url .= '&location_id=' . $this->request->post['location_id'];
		} else if (isset($this->request->get['location_id'])) {
				$url .= '&location_id=' . $this->request->get['location_id'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => 'Меню уровень вверх', //$this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => '[Обновить]', // $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/partnerprice', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['user_token'] = $this->session->data['user_token'];


		if (isset($this->error['warning'])) {

			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

    $data['config_pumpprice_debug'] = $this->config->get('config_pumpprice_debug');
		$this->getExpSettings($data, $url);

	  $this->getLog($data, $url);

	  $this->getLogpump($data, $url); 
 
//		$this->getmanual($data, $url); 

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		//$this->load->model('localisation/currency');
		//$data['currencies'] = $this->model_localisation_currency->getCurrencies();
		$this->load->model('catalog/partnerprice');
		$data['currencies'] = $this->model_catalog_partnerprice->getCurrencies();
    $this->session->data['location_id'] = $data['pumpprice_currency'];
    $html = $this->load->controller('catalog/partnerpricelist');
    $data['form_browse'] = $html;
if(admin_controller_catalog_partnerprice__getList){
$log = fopen(DIR_LOGS . 'admin_controller_catalog_partnerprice__getList.log', 'a');
fwrite($log,'**********************************' . chr(13) . chr(13));
fwrite($log,'$data ) ' . print_r($data,true) . chr(13) . chr(13));
fclose($log);}


		$this->response->setOutput($this->load->view('catalog/partnerprice', $data));
	}

	
	protected function return_bytes($val) {
		$val = trim($val);
	
		switch (strtolower(substr($val, -1)))
		{
			case 'm': $val = (int)substr($val, 0, -1) * 1048576; break;
			case 'k': $val = (int)substr($val, 0, -1) * 1024; break;
			case 'g': $val = (int)substr($val, 0, -1) * 1073741824; break;
			case 'b':
				switch (strtolower(substr($val, -2, 1)))
				{
					case 'm': $val = (int)substr($val, 0, -2) * 1048576; break;
					case 'k': $val = (int)substr($val, 0, -2) * 1024; break;
					case 'g': $val = (int)substr($val, 0, -2) * 1073741824; break;
					default : break;
				} break;
			default: break;
		}
		return $val;
	}

	protected function isInteger($input){
		return(ctype_digit(strval($input)));
	}

	
	protected function getExpSettings(&$data, $url) {
if(admin_controller_catalog_partnerprice__getExpSettings){
$log = fopen(DIR_LOGS . 'admin_controller_catalog_partnerprice__getExpSettings.log', 'w');
fwrite($log,'**********************************' . chr(13) . chr(13));
$t = microtime(true);
$micro = sprintf("%06d",($t - floor($t)) * 1000000);
$d = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );
fwrite($log, $d->format("Y-m-d H:i:s.u") . '==' . chr(10) . chr(13));
fwrite($log,'$data ) ' . print_r($data,true) . chr(13) . chr(13));
fclose($log);}
		$this->load->language('tool/export_import');
		$data['error_select_file'] = $this->language->get('error_select_file');
		$data['error_post_max_size'] = str_replace( '%1', ini_get('post_max_size'), $this->language->get('error_post_max_size') );
		$data['error_upload_max_filesize'] = str_replace( '%1', ini_get('upload_max_filesize'), $this->language->get('error_upload_max_filesize') );
		$data['error_id_no_data'] = $this->language->get('error_id_no_data');
		$data['error_page_no_data'] = $this->language->get('error_page_no_data');
		$data['error_param_not_number'] = $this->language->get('error_param_not_number');
		$data['error_notifications'] = $this->language->get('error_notifications');
		$data['error_no_news'] = $this->language->get('error_no_news');
		$data['error_batch_number'] = $this->language->get('error_batch_number');
		$data['error_min_item_id'] = $this->language->get('error_min_item_id');
		

		if (!empty($this->session->data['export_import_error']['errstr'])) {
			$this->error['warning'] = $this->session->data['export_import_error']['errstr'];
		}

 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
			if (!empty($this->session->data['export_import_nochange'])) {
				$data['error_warning'] .= "<br />\n".$this->language->get( 'text_nochange' );
			}
		} else {
			$data['error_warning'] = '';
		}

		unset($this->session->data['export_import_error']);
		unset($this->session->data['export_import_nochange']);

		$data['import'] = $this->url->link('catalog/partnerprice/upload', 'user_token=' . $this->session->data['user_token'], true);
		$data['partner_compare_product'] = $this->url->link('catalog/partnerprice/partner_compare_product', 'user_token=' . $this->session->data['user_token'], true);
		$data['partner_set_extra'] = $this->url->link('catalog/partnerprice/partner_set_extra', 'user_token=' . $this->session->data['user_token'], true);
		$data['settings'] = $this->url->link('catalog/partnerprice/settings', 'user_token=' . $this->session->data['user_token'], true);
		$data['post_max_size'] = $this->return_bytes( ini_get('post_max_size') );
		$data['upload_max_filesize'] = $this->return_bytes( ini_get('upload_max_filesize') );
    
    $data['showbutton_deletedoublerec'] = $this->config->get('config_showbutton_admin_import_prices_deletedoublerec');

		$this->document->addStyle('view/stylesheet/export_import.css');

		$currency = ''; $location_id = '';
		$this->load->model('catalog/partnerprice');
    $currencies = $this->model_catalog_partnerprice->getCurrencies();
    $data['currencies'] = $currencies;

//		if (isset($this->request->get['location_id'])) {
//				$url .= '&location_id=' . $this->request->get['location_id'];
//		}

    
		if (!isset($this->request->post['location_id']) && !isset($this->request->get['location_id'])) {
      if(!empty($currencies) ){
        $rec = array_shift($currencies);
        $location_id = $rec['id'];
        $currency = $rec['code'];
      }
		}
     
    $currencies = $this->model_catalog_partnerprice->getCurrencies();
		$this->load->model('setting/setting');
    
		if (isset($this->request->post['location_id'])) {
			$data['pumpprice_currency'] = $this->request->post['location_id'];
      $location_id = $this->request->post['location_id'];
      $currency = $this->model_catalog_partnerprice->getLocation2Currency($location_id);
		} else if (isset($this->request->get['location_id'])) {
			$data['pumpprice_currency'] = $this->request->get['location_id'];
      $location_id = $this->request->get['location_id'];
      $currency = $this->model_catalog_partnerprice->getLocation2Currency($location_id);
		} else {
			$data['pumpprice_currency'] = $location_id;
		}
if(admin_controller_catalog_partnerprice__getExpSettings){
$log = fopen(DIR_LOGS . 'admin_controller_catalog_partnerprice__getExpSettings.log', 'a');
fwrite($log,'**********************************' . chr(13) . chr(13));
fwrite($log,'$location_id ) ' . print_r($location_id,true) . chr(13) . chr(13));
fwrite($log,'$currency ) ' . print_r($currency,true) . chr(13) . chr(13));
fclose($log);}
    
    
    $settings_array = array();
    
    foreach($currencies as $rec){
      $currency = $rec['code'];
      $loc_id = $rec['id'];
      $setting = array();
			$store_info = $this->model_setting_setting->getSetting('pumpprice_' . $loc_id);
if(admin_controller_catalog_partnerprice__getExpSettings){
$log = fopen(DIR_LOGS . 'admin_controller_catalog_partnerprice__getExpSettings.log', 'a');
fwrite($log,'**********************************' . chr(13) . chr(13));
fwrite($log,'$store_info  = $this->model_setting_setting->getSetting(pumpprice_ . $loc_id); ) ' . print_r($store_info,true) . chr(13) . chr(13));
fclose($log);}
    
		  if (isset($this->request->post['pumpprice_' . $loc_id . '_currency'])) {
			  $setting['pumpprice_' . $loc_id . '_currency'] = $this->request->post['pumpprice_' . $loc_id . '_currency'];
		  } elseif (isset($store_info['pumpprice_' . $loc_id . '_currency'])) {
			  $setting['pumpprice_' . $loc_id . '_currency'] = $store_info['pumpprice_' . $loc_id . '_currency'];
		  } else {
			  $setting['pumpprice_' . $loc_id . '_currency'] = '';
		  }
      $setting['location_id'] = $rec['id'];
      $setting['title'] = $rec['title'];
      $setting['partner_module'] = $this->model_catalog_partnerprice->getpartner_module($loc_id);
		  if (isset($this->request->post['pumpprice_' . $loc_id . '_sheetname'])) {
			  $setting['pumpprice_' . $loc_id . '_sheetname'] = $this->request->post['pumpprice_' . $loc_id . '_sheetname'];
		  } elseif (isset($store_info['pumpprice_' . $loc_id . '_sheetname'])) {
			  $setting['pumpprice_' . $loc_id . '_sheetname'] = $store_info['pumpprice_' . $loc_id . '_sheetname'];
		  } else {
			  $setting['pumpprice_' . $loc_id . '_sheetname'] = '';
		  }
		  if (isset($this->request->post['pumpprice_' . $loc_id . '_firstrow'])) {
			  $setting['pumpprice_' . $loc_id . '_firstrow'] = $this->request->post['pumpprice_' . $loc_id . '_firstrow'];
		  } elseif (isset($store_info['pumpprice_' . $loc_id . '_firstrow'])) {
			  $setting['pumpprice_' . $loc_id . '_firstrow'] = $store_info['pumpprice_' . $loc_id . '_firstrow'];
		  } else {
			  $setting['pumpprice_' . $loc_id . '_firstrow'] = '';
		  }
		  if (isset($this->request->post['pumpprice_' . $loc_id . '_lastrow'])) {
			  $setting['pumpprice_' . $loc_id . '_lastrow'] = $this->request->post['pumpprice_' . $loc_id . '_lastrow'];
		  } elseif (isset($store_info['pumpprice_' . $loc_id . '_lastrow'])) {
			  $setting['pumpprice_' . $loc_id . '_lastrow'] = $store_info['pumpprice_' . $loc_id . '_lastrow'];
		  } else {
			  $setting['pumpprice_' . $loc_id . '_lastrow'] = '';
		  }
		  if (isset($this->request->post['pumpprice_' . $loc_id . '_name'])) {
			  $setting['pumpprice_' . $loc_id . '_name'] = $this->request->post['pumpprice_' . $loc_id . '_name'];
		  } elseif (isset($store_info['pumpprice_' . $loc_id . '_name'])) {
			  $setting['pumpprice_' . $loc_id . '_name'] = $store_info['pumpprice_' . $loc_id . '_name'];
		  } else {
			  $setting['pumpprice_' . $loc_id . '_name'] = '';
		  }
		  if (isset($this->request->post['pumpprice_' . $loc_id . '_modelnum'])) {
			  $setting['pumpprice_' . $loc_id . '_modelnum'] = $this->request->post['pumpprice_' . $loc_id . '_modelnum'];
		  } elseif (isset($store_info['pumpprice_' . $loc_id . '_modelnum'])) {
			  $setting['pumpprice_' . $loc_id . '_modelnum'] = $store_info['pumpprice_' . $loc_id . '_modelnum'];
		  } else {
			  $setting['pumpprice_' . $loc_id . '_modelnum'] = '';
		  }
		  if (isset($this->request->post['pumpprice_' . $loc_id . '_manufacture'])) {
			  $setting['pumpprice_' . $loc_id . '_manufacture'] = $this->request->post['pumpprice_' . $loc_id . '_manufacture'];
		  } elseif (isset($store_info['pumpprice_' . $loc_id . '_manufacture'])) {
			  $setting['pumpprice_' . $loc_id . '_manufacture'] = $store_info['pumpprice_' . $loc_id . '_manufacture'];
		  } else {
			  $setting['pumpprice_' . $loc_id . '_manufacture'] = '';
		  }
		  if (isset($this->request->post['pumpprice_' . $loc_id . '_automanufacture'])) {
			  $setting['pumpprice_' . $loc_id . '_automanufacture'] = $this->request->post['pumpprice_' . $loc_id . '_automanufacture'];
		  } elseif (isset($store_info['pumpprice_' . $loc_id . '_automanufacture'])) {
			  $setting['pumpprice_' . $loc_id . '_automanufacture'] = $store_info['pumpprice_' . $loc_id . '_automanufacture'];
		  } else {
			  $setting['pumpprice_' . $loc_id . '_automanufacture'] = '';
		  }
		  if (isset($this->request->post['pumpprice_' . $loc_id . '_automanufacture_number'])) {
			  $setting['pumpprice_' . $loc_id . '_automanufacture_number'] = $this->request->post['pumpprice_' . $loc_id . '_automanufacture_number'];
		  } elseif (isset($store_info['pumpprice_' . $loc_id . '_automanufacture_number'])) {
			  $setting['pumpprice_' . $loc_id . '_automanufacture_number'] = $store_info['pumpprice_' . $loc_id . '_automanufacture_number'];
		  } else {
			  $setting['pumpprice_' . $loc_id . '_automanufacture_number'] = '';
		  }
		  if (isset($this->request->post['pumpprice_' . $loc_id . '_price'])) {
			  $setting['pumpprice_' . $loc_id . '_price'] = $this->request->post['pumpprice_' . $loc_id . '_price'];
		  } elseif (isset($store_info['pumpprice_' . $loc_id . '_price'])) {
			  $setting['pumpprice_' . $loc_id . '_price'] = $store_info['pumpprice_' . $loc_id . '_price'];
		  } else {
			  $setting['pumpprice_' . $loc_id . '_price'] = '';
		  }
		  if (isset($this->request->post['pumpprice_' . $loc_id . '_quantity'])) {
			  $setting['pumpprice_' . $loc_id . '_quantity'] = $this->request->post['pumpprice_' . $loc_id . '_quantity'];
		  } elseif (isset($store_info['pumpprice_' . $loc_id . '_quantity'])) {
			  $setting['pumpprice_' . $loc_id . '_quantity'] = $store_info['pumpprice_' . $loc_id . '_quantity'];
		  } else {
			  $setting['pumpprice_' . $loc_id . '_quantity'] = '';
		  }
		  if (isset($this->request->post['pumpprice_' . $loc_id . '_nonUniquiModelPump'])) {
			  $setting['pumpprice_' . $loc_id . '_nonUniquiModelPump'] = $this->request->post['pumpprice_' . $loc_id . '_nonUniquiModelPump'];
		  } elseif (isset($store_info['pumpprice_' . $loc_id . '_nonUniquiModelPump'])) {
			  $setting['pumpprice_' . $loc_id . '_nonUniquiModelPump'] = $store_info['pumpprice_' . $loc_id . '_nonUniquiModelPump'];
		  } else {
			  $setting['pumpprice_' . $loc_id . '_nonUniquiModelPump'] = '1';
		  }
      $settings_array[$currency] = $setting; 
if(admin_controller_catalog_partnerprice__getExpSettings){
$log = fopen(DIR_LOGS . 'admin_controller_catalog_partnerprice__getExpSettings.log', 'a');
fwrite($log,'**********************************' . chr(13) . chr(13));
fwrite($log,'$setting ) ' . print_r($setting,true) . chr(13) . chr(13));
fclose($log);}
    }
    $data['settings_array'] =  $settings_array;
if(admin_controller_catalog_partnerprice__getExpSettings){
$log = fopen(DIR_LOGS . 'admin_controller_catalog_partnerprice__getExpSettings.log', 'a');
fwrite($log,'**********************************' . chr(13) . chr(13));
fwrite($log,'$settings_array ) ' . print_r($settings_array,true) . chr(13) . chr(13));
fwrite($log,'$data ) ' . print_r($data,true) . chr(13) . chr(13));
fclose($log);}
	}

	public function settings() {
if(admin_controller_catalog_partnerprice__Settings){
$log = fopen(DIR_LOGS . 'admin_controller_catalog_partnerprice__Settings.log', 'w');
fwrite($log,'**********************************' . chr(13) . chr(13));
$t = microtime(true);
$micro = sprintf("%06d",($t - floor($t)) * 1000000);
$d = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );
fwrite($log, $d->format("Y-m-d H:i:s.u") . '==' . chr(10) . chr(13));
fwrite($log,'$this->request->post ) ' . print_r($this->request->post,true) . chr(13) . chr(13));
fclose($log);}
		$url = '';

		$this->load->language('extension/export_import');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('extension/export_import');
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validateSettingsForm())) {
			if (!isset($this->request->post['export_import_settings_use_export_cache'])) {
				$this->request->post['export_import_settings_use_export_cache'] = '0';
			}
			if (!isset($this->request->post['export_import_settings_use_import_cache'])) {
				$this->request->post['export_import_settings_use_import_cache'] = '0';
			}
      $this->load->model('catalog/partnerprice');
      $location_id = $this->request->post['pumpprice_currency'];
      $currency = $this->model_catalog_partnerprice->getLocation2Currency($location_id);
if(admin_controller_catalog_partnerprice__Settings){
$log = fopen(DIR_LOGS . 'admin_controller_catalog_partnerprice__Settings.log', 'a');
fwrite($log,'**********************************' . chr(13) . chr(13));
fwrite($log,'$location_id ) ' . print_r($location_id,true) . chr(13) . chr(13));
fwrite($log,'$currency ) ' . print_r($currency,true) . chr(13) . chr(13));
fclose($log);}
			$this->load->model('setting/setting');
			$this->model_setting_setting->editSetting('pumpprice_' . $location_id, $this->request->post);
			$this->session->data['success'] = 'Настройки формы XLS выполнено  УСПЕШНО';
			if (isset($this->request->post['pumpprice_currency'])) {
			  //$url = rtrim(str_replace("&amp;","&",$this->request->post['pumpprice_currency']),"&");
			  $url .= '&location_id=' . $this->request->post['pumpprice_currency'];
			}
if(admin_controller_catalog_partnerprice__Settings){
$log = fopen(DIR_LOGS . 'admin_controller_catalog_partnerprice__Settings.log', 'a');
fwrite($log,'**********************************' . chr(13) . chr(13));
fwrite($log,'$url ) ' . print_r($url,true) . chr(13) . chr(13));
fclose($log);}
			$this->response->redirect($this->url->link('catalog/partnerprice', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}
		$this->getList();
	}
	
	protected function validateSettingsForm() {
if(admin_controller_catalog_partnerprice__Settings){
$log = fopen(DIR_LOGS . 'admin_controller_catalog_partnerprice__validateSettingsForm.log', 'w');
fwrite($log,'**********************************' . chr(13) . chr(13));
$t = microtime(true);
$micro = sprintf("%06d",($t - floor($t)) * 1000000);
$d = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );
fwrite($log, $d->format("Y-m-d H:i:s.u") . '==' . chr(10) . chr(13));
fwrite($log,'$this->request->post ) ' . print_r($this->request->post,true) . chr(13) . chr(13));
fclose($log);}

		if (!$this->user->hasPermission('access', 'extension/export_import')) {
			$this->error['warning'] = 'Нет прав доступа редактирования Настроек';
			return false;
    } else if (empty($this->request->post['pumpprice_currency'])) {
      $this->error['warning'] = 'Код Партнера ОБЯЗАТЕЛЕН для редактирования Настроек';
      return false;
		}
    $this->load->model('catalog/partnerprice');
    $location_id = $this->request->post['pumpprice_currency'];
    $currency = $this->model_catalog_partnerprice->getLocation2Currency($location_id);
    
if(admin_controller_catalog_partnerprice__validateSettingsForm){
$log = fopen(DIR_LOGS . 'admin_controller_catalog_partnerprice__validateSettingsForm.log', 'a');
fwrite($log,'**********************************' . chr(13) . chr(13));
fwrite($log,'$location_id ) ' . print_r($location_id,true) . chr(13) . chr(13));
fwrite($log,'$currency ) ' . print_r($currency,true) . chr(13) . chr(13));
fclose($log);}

    if (!isset( $this->request->post['pumpprice_' . $location_id . '_incremental'] )) {
			$this->error['warning'] = $this->language->get( 'error_incremental' );
		} else if ($this->request->post['pumpprice_' . $location_id . '_incremental'] != '0') {
			if ($this->request->post['pumpprice_' . $location_id . '_incremental'] != '1') {
				$this->error['warning'] = $this->language->get( 'error_incremental' );
			}
		}
		if (!empty($location_id) && ($this->request->post['pumpprice_' . $location_id . '_nonUniquiModelPump'])) {
		  $this->load->model('extension/export_import');
			$option_names = $this->model_extension_export_import->getModelPumpNameCounts($location_id);
			foreach ($option_names as $option_name) {
				if ($option_name['count'] > 1) {
					$this->error['warning'] = str_replace( '%1', $option_name['model'], $this->language->get( 'error_nonUniquiModel' ) );
		
					return false;
				}
			}
		}

		return true;
	}

	protected function validateUploadForm($partner_module) {
		if (!$this->user->hasPermission('modify', 'tool/export_import')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!isset($this->request->files['upload']['name'])) {
			if (isset($this->error['warning'])) {
				$this->error['warning'] .= "<br /\n" . $this->language->get( 'error_upload_name' );
			} else {
				$this->error['warning'] = $this->language->get( 'error_upload_name' );
			}
		} else {
			$ext = strtolower(pathinfo($this->request->files['upload']['name'], PATHINFO_EXTENSION));
			if ((strpos($partner_module,'xlsx') !== false) && ($ext != 'xls') && ($ext != 'xlsx') && ($ext != 'ods')) {
				if (isset($this->error['warning'])) {
					$this->error['warning'] .= "<br /\n" . $this->language->get( 'error_upload_ext' );
				} else {
					$this->error['warning'] = $this->language->get( 'error_upload_ext' );
				}
			}
      
			if ((strpos($partner_module,'csv') !== false) && ($ext != 'csv')) {
				if (isset($this->error['warning'])) {
					$this->error['warning'] .= "<br /\n" . $this->language->get( 'error_upload_ext_csv' );
				} else {
					$this->error['warning'] = $this->language->get( 'error_upload_ext_csv' );
				}
			}
      
		}
    if (($this->request->server['REQUEST_METHOD'] == 'POST') && ( empty($this->request->post['pumpprice_currency']) )) 
      if (isset($this->error['warning'])) {
        $this->error['warning'] .= "<br /\n" . $this->language->get('error_empty_currency_code');
			} else {
					$this->error['warning'] = $this->language->get( 'error_empty_currency_code' );
			}
      
    if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	public function upload() {
if(admin_controller_catalog_partnerprice__upload){
$log = fopen(DIR_LOGS . 'admin_controller_catalog_partnerprice__upload.log', 'w');
fwrite($log,'**********************************' . chr(13) . chr(13));
$t = microtime(true);
$micro = sprintf("%06d",($t - floor($t)) * 1000000);
$d = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );
fwrite($log, $d->format("Y-m-d H:i:s.u") . '==' . chr(10) . chr(13));
fwrite($log,'$this->request->get ) ' . print_r($this->request->get,true) . chr(13) . chr(13));
fwrite($log,'$this->request->post ) ' . print_r($this->request->post,true) . chr(13) . chr(13));
fclose($log);}
		$url = '';

    if (($this->request->server['REQUEST_METHOD'] == 'POST') && (!empty($this->request->post['pumpprice_currency'])))
      $location_id = $this->request->post['pumpprice_currency'];
    else $location_id = '';

    $this->load->model('catalog/partnerprice');
    $file_csv = $this->model_catalog_partnerprice->getpartner_module($location_id);

    $this->load->language('tool/export_import');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('extension/export_import');
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validateUploadForm($file_csv))) {
		  
if(admin_controller_catalog_partnerprice__upload){
$log = fopen(DIR_LOGS . 'admin_controller_catalog_partnerprice__upload.log', 'a');
fwrite($log,'********************************** ' . chr(13) . chr(13));
fwrite($log,'$location_id ) ' . print_r($location_id,true) . chr(13) . chr(13));
fwrite($log,'$file_csv ) ' . print_r($file_csv,true) . chr(13) . chr(13));
fclose($log);}

			if ((isset( $this->request->files['upload'] )) && (is_uploaded_file($this->request->files['upload']['tmp_name']))) {
			 
if(admin_controller_catalog_partnerprice__upload){
$log = fopen(DIR_LOGS . 'admin_controller_catalog_partnerprice__upload.log', 'a');
fwrite($log,'********************************** after  if ((isset( $this->request->files[upload] )) && (is_uploaded_file($this->request->files[upload][tmp_name])))' . chr(13) . chr(13));
$t = microtime(true);
$micro = sprintf("%06d",($t - floor($t)) * 1000000);
$d = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );
fwrite($log, $d->format("Y-m-d H:i:s.u") . '==' . chr(10) . chr(13));
fclose($log);}

				$file = $this->request->files['upload']['tmp_name'];
				if( 
            ($file_csv && $file_csv != 'csv' && $this->model_extension_export_import->uploadpumpprice($file, $this->request->post['pumpprice_currency'])) ||
            ($file_csv && $file_csv == 'csv' && $this->model_extension_export_import->uploadpumpprice_csv($file, $this->request->post['pumpprice_currency']))
           ) {
				  
if(admin_controller_catalog_partnerprice__upload){
$log = fopen(DIR_LOGS . 'admin_controller_catalog_partnerprice__upload.log', 'a');
fwrite($log,'********************************** after  if ($this->model_extension_export_import->upload($file,$this->request->post[incremental])==true)' . chr(13) . chr(13));
$t = microtime(true);
$micro = sprintf("%06d",($t - floor($t)) * 1000000);
$d = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );
fwrite($log, $d->format("Y-m-d H:i:s.u") . '==' . chr(10) . chr(13));
fclose($log);}

		       $this->load->model('catalog/partnerprice');
		       if (($this->request->server['REQUEST_METHOD'] == 'POST') && !empty($this->request->post['pumpprice_currency']) && ($this->request->post['pumpprice_currency'] == '1001' || $this->request->post['pumpprice_currency'] == '1002')) {
		        
if(admin_controller_catalog_partnerprice__upload){
$log = fopen(DIR_LOGS . 'admin_controller_catalog_partnerprice__upload.log', 'a');
fwrite($log,'********************************** after  if (($this->request->server[REQUEST_METHOD] == POST) && (!empty($this->request->post[pumpprice_currency])) && ($this->request->post[pumpprice_currency] == 1001 || $this->request->post[pumpprice_currency] == 1002))' . chr(13) . chr(13));
$t = microtime(true);
$micro = sprintf("%06d",($t - floor($t)) * 1000000);
$d = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );
fwrite($log, $d->format("Y-m-d H:i:s.u") . '==' . chr(10) . chr(13));
fclose($log);}

		          $res = $this->model_catalog_partnerprice->deletedoubleprices($this->request->post['pumpprice_currency']);
if(admin_controller_catalog_partnerprice__upload){
$log = fopen(DIR_LOGS . 'admin_controller_catalog_partnerprice__upload.log', 'a');
fwrite($log,'********************************** after  if (($this->request->server[REQUEST_METHOD] == POST) && (!empty($this->request->post[pumpprice_currency])) && ($this->request->post[pumpprice_currency] == 1001))' . chr(13) . chr(13));
$t = microtime(true);
$micro = sprintf("%06d",($t - floor($t)) * 1000000);
$d = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );
fwrite($log, $d->format("Y-m-d H:i:s.u") . '==' . chr(10) . chr(13));
fwrite($log,'$res = $this->model_catalog_partnerprice->deletedoubleprices( ' . $this->request->post['pumpprice_currency'] . ' ) = ' . print_r($res,true) . chr(13) . chr(13));
fclose($log);}
           }
		      $this->load->model('extension/export_import');
          
					$this->session->data['success'] = 'Файл <'.$file.'> закачан успешно';
	        if (($this->request->server['REQUEST_METHOD'] == 'POST') && (isset($this->request->post['pumpprice_currency']))) {
            $url .= '&location_id=' . $this->request->post['pumpprice_currency'];
	        }
					$this->response->redirect($this->url->link('catalog/partnerprice', 'user_token=' . $this->session->data['user_token'] . $url, true));
				}
				else {
					$this->error['warning'] = $this->language->get('error_upload');
					if (defined('VERSION')) {
						if (version_compare(VERSION,'2.1.0.0') > 0) {
							$this->error['warning'] .= "<br />\n".$this->language->get( 'text_log_details_2_1_x' );
						} else
							$this->error['warning'] .= "<br />\n".$this->language->get( 'text_log_details_2_0_x' );
					} else {
						$this->error['warning'] .= "<br />\n".$this->language->get( 'text_log_details' );
					}
					if (isset($this->session->data['export_import_error']))
					  $this->error['warning'] .= "<br />\n".print_r($this->session->data['export_import_error'],true);
				}
			}
		}
		$this->getList();
	}




	protected function validatePartner_compare_product() {
    if (($this->request->server['REQUEST_METHOD'] == 'POST')) { 
      if ( empty($this->request->post['location_id'])  || 
           ($this->model_catalog_partnerprice->getLocation2Currency($this->request->post['location_id']) === false )
          ) 
        $this->error['warning'] .= "<br /\n" . $this->language->get( 'error_empty_currency_code' );
    }
      
    if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	public function partner_compare_product() {
		$url = '';

    $this->load->language('tool/export_import');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('catalog/partnerprice');
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validatePartner_compare_product())) {
				if ($this->model_catalog_partnerprice->partner_compare_product($this->request->post['location_id'])){ 
					$this->session->data['success'] = 'Процедура "Проверка соответствия товаров и добавление новых" выполнена успешно';
			        if (($this->request->server['REQUEST_METHOD'] == 'POST') && (isset($this->request->post['url']))) {
			           $url = rtrim(str_replace("&amp;","&",$this->request->post['url']),"&");
			        }
					$this->response->redirect($this->url->link('catalog/partnerprice', 'user_token=' . $this->session->data['user_token'] . $url, true));
				}
				else {
					$this->error['warning'] = 'Ошибка выполнения процедуры "Проверка соответствия товаров и добавление новых" ';
				}
		}
		$this->getList();
	}


	protected function validatePartner_set_extra() {
    if (($this->request->server['REQUEST_METHOD'] == 'POST')) { 
      if ( empty($this->request->post['location_id'])  || 
           ($this->model_catalog_partnerprice->getLocation2Currency($this->request->post['location_id']) === false )
          ) 
        $this->error['warning'] .= "<br /\n" . $this->language->get( 'error_empty_currency_code' );
    }
      
    if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	public function partner_set_extra() {
		$url = '';

    $this->load->language('tool/export_import');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('catalog/partnerprice');
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validatePartner_set_extra())) {
				if ($this->model_catalog_partnerprice->partner_set_extra($this->request->post['location_id'])){ 
					$this->session->data['success'] = 'Процедура "Выполнение Переоценки и внесение остатков" выполнена успешно';
			        if (($this->request->server['REQUEST_METHOD'] == 'POST') && (isset($this->request->post['url']))) {
			           $url = rtrim(str_replace("&amp;","&",$this->request->post['url']),"&");
			        }
					$this->response->redirect($this->url->link('catalog/partnerprice', 'user_token=' . $this->session->data['user_token'] . $url, true));
				}
				else {
					$this->error['warning'] = 'Ошибка выполнения процедуры "Выполнение Переоценки и внесение остатков" ';
				}
		}
		$this->getList();
	}

	protected function validatedoubleprice($location) {
    if (($this->request->server['REQUEST_METHOD'] == 'POST')) { 
      if ( $location_id > 0 )
        $this->error['warning'] .= "<br /\n" . $this->language->get( 'error_empty_currency_code' );
    }
      
    if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

  public function deletedoubleprices(){
if(admin_controller_catalog_partnerprice__deletedoubleprices){
$log = fopen(DIR_LOGS . 'admin_controller_catalog_partnerprice__deletedoubleprices.log', 'w');
fwrite($log,'**********************************' . chr(13) . chr(13));
$t = microtime(true);
$micro = sprintf("%06d",($t - floor($t)) * 1000000);
$d = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );
fwrite($log, $d->format("Y-m-d H:i:s.u") . '==' . chr(10) . chr(13));
fwrite($log,'$this->request->get ) ' . print_r($this->request->get,true) . chr(13) . chr(13));
fwrite($log,'$this->request->post ) ' . print_r($this->request->post,true) . chr(13) . chr(13));
fclose($log);}
    $json = array();
		$this->load->model('catalog/partnerprice');
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && (!empty($this->request->get['location_id'])) && ($this->request->get['location_id'] == '1001')) {
		    $res = $this->model_catalog_partnerprice->deletedoubleprices($this->request->get['location_id']);
				$json['success'] = 'Процедура "Удаление одинаковых строк" выполнена успешно. Удалено ' . (int)$res . ' строк';
     }
     if(empty($json))
			$json['warning'] = 'Процедура "Удаление одинаковых строк" НЕ выполнена -- не удовлетворяет условиям вызова процедуры. Обратитесь к разработчикам';
      
if(admin_controller_catalog_partnerprice__deletedoubleprices){
$log = fopen(DIR_LOGS . 'admin_controller_catalog_partnerprice__deletedoubleprices.log', 'a');
fwrite($log,'return $json ) ' . print_r($json,true) . chr(13) . chr(13));
fclose($log);}
      
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    
  }

	
    protected function getLog(&$data, $url) {
		$this->load->language('tool/log');
		$data['download'] = $this->url->link('catalog/partnerprice/logg_download', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['clear'] = $this->url->link('catalog/partnerprice/logg_clear', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['log'] = '';

		$file = DIR_LOGS . $this->config->get('config_error_filename');

		if ($data['config_pumpprice_debug'] == '1' && file_exists($file)) {
			$size = filesize($file);

			if ($size >= 5242880) {
				$suffix = array(
					'B',
					'KB',
					'MB',
					'GB',
					'TB',
					'PB',
					'EB',
					'ZB',
					'YB'
				);

				$i = 0;

				while (($size / 1024) > 1) {
					$size = $size / 1024;
					$i++;
				}

				$data['error_warning'] = sprintf($this->language->get('error_warning'), basename($file), round(substr($size, 0, strpos($size, '.') + 4), 2) . $suffix[$i]);
			} else {
				$data['log'] = file_get_contents($file, FILE_USE_INCLUDE_PATH, null);
			}
		}


    }
	
    protected function getLogpump(&$data, $url) {
		$this->load->language('tool/log');

		$data['logpump'] = '';

		$file = DIR_LOGS . 'partnerprice.log';

		if ($data['config_pumpprice_debug'] == '1' && file_exists($file)) {
			$size = filesize($file);

			if ($size >= 5242880) {
				$suffix = array(
					'B',
					'KB',
					'MB',
					'GB',
					'TB',
					'PB',
					'EB',
					'ZB',
					'YB'
				);

				$i = 0;

				while (($size / 1024) > 1) {
					$size = $size / 1024;
					$i++;
				}

				$data['error_warning'] = sprintf($this->language->get('error_warning'), basename($file), round(substr($size, 0, strpos($size, '.') + 4), 2) . $suffix[$i]);
			} else {
				$data['logpump'] = file_get_contents($file, FILE_USE_INCLUDE_PATH, null);
			}
		}


    }

	public function logg_download() {
		$url = '';

	    $this->load->language('tool/log');

		$file = DIR_LOGS . $this->config->get('config_error_filename');

		if (file_exists($file) && filesize($file) > 0) {
			$this->response->addheader('Pragma: public');
			$this->response->addheader('Expires: 0');
			$this->response->addheader('Content-Description: File Transfer');
			$this->response->addheader('Content-Type: application/octet-stream');
			$this->response->addheader('Content-Disposition: attachment; filename="' . $this->config->get('config_name') . '_' . date('Y-m-d_H-i-s', time()) . '_error.log"');
			$this->response->addheader('Content-Transfer-Encoding: binary');

			$this->response->setOutput(file_get_contents($file, FILE_USE_INCLUDE_PATH, null));
		} else {
			$this->session->data['error'] = sprintf($this->language->get('error_warning'), basename($file), '0B');


			$this->response->redirect($this->url->link('catalog/partnerprice', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}
	}
	
	public function logg_clear() {
		$url = '';

		$this->load->language('tool/log');

		if (!$this->user->hasPermission('modify', 'tool/log')) {
			$this->session->data['error'] = $this->language->get('error_permission');
		} else {
			$file = DIR_LOGS . $this->config->get('config_error_filename');

			$handle = fopen($file, 'w+');

			fclose($handle);

			$this->session->data['success'] = 'Протокол  работы очищен';
		}
  	if (isset($this->request->post['location_id'])) {
			  $url .= '&location_id=' . $this->request->post['location_id'];
			}

		$this->response->redirect($this->url->link('catalog/partnerprice', 'user_token=' . $this->session->data['user_token'] . $url, true));
	}	


  public function getpartnerprice_list(){
if(admin_controller_catalog_partnerprice__getpartnerprice_list){
$log = fopen(DIR_LOGS . 'admin_controller_catalog_partnerprice__getpartnerprice_list.log', 'w');
fwrite($log,'**********************************' . chr(13) . chr(13));
$t = microtime(true);
$micro = sprintf("%06d",($t - floor($t)) * 1000000);
$d = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );
fwrite($log, $d->format("Y-m-d H:i:s.u") . '==' . chr(10) . chr(13));
fwrite($log,'$this->request->get) ' . print_r($this->request->get,true) . chr(13) . chr(13));
fwrite($log,'$this->request->post) ' . print_r($this->request->post,true) . chr(13) . chr(13));
fclose($log);}
    $json = array();
    $this->load->model('catalog/partnerprice');
    if (($this->request->server['REQUEST_METHOD'] == 'GET')) { 
      if ( empty($this->request->get['location_id'])  || 
           ($this->model_catalog_partnerprice->getLocation2Currency($this->request->get['location_id']) === false )
          ) {
        $json['warning'] = $this->language->get( 'Выбор Партнера Обязателен' );
            } else {

        $location_id = $this->request->get['location_id'];
//		  $this->load->model('catalog/partnerprice');
//		  $json['currencies'] = $this->model_catalog_partnerprice->getCurrencies();
//      $json['partnerprice_list'] = $this->model_catalog_partnerprice->getpartnerprice_list($location_id, $filter_data);
      $html = $this->load->controller('catalog/partnerpricelist');
      $json['form_browse'] = $html;
    } }
if(admin_controller_catalog_partnerprice__getpartnerprice_list){
$log = fopen(DIR_LOGS . 'admin_controller_catalog_partnerprice__getpartnerprice_list.log', 'a');
fwrite($log,'**********************************' . chr(13) . chr(13));
fwrite($log,'$json) ' . print_r($json,true) . chr(13) . chr(13));
fclose($log);}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    
  }


	
}

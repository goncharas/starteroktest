<?php
class ControllerCommonTop extends Controller {
	public function index() {
		$this->load->model('design/layout');

		if (isset($this->request->get['route'])) {
			$route = (string)$this->request->get['route'];
		} else {
			$route = 'common/home';
		}
if(catalog_controller_common_top__index){
$log = fopen(DIR_LOGS . 'catalog_controller_common_top__index.log', 'w');
fwrite($log,'**********************************' . chr(13) . chr(13));
$t = microtime(true);
$micro = sprintf("%06d",($t - floor($t)) * 1000000);
$d = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );
fwrite($log, $d->format("Y-m-d H:i:s.u") . '==' . chr(10) . chr(13));
fwrite($log, '$route)' . $route . chr(13) . chr(13));
fclose($log);}

		$layout_id = 0;

		if ($route == 'product/category' && isset($this->request->get['path'])) {
			$this->load->model('catalog/category');

			$path = explode('_', (string)$this->request->get['path']);

			$layout_id = $this->model_catalog_category->getCategoryLayoutId(end($path));
		}

		if ($route == 'product/product' && isset($this->request->get['product_id'])) {
			$this->load->model('catalog/product');

			$layout_id = $this->model_catalog_product->getProductLayoutId($this->request->get['product_id']);
		}

		if ($route == 'information/information' && isset($this->request->get['information_id'])) {
			$this->load->model('catalog/information');

			$layout_id = $this->model_catalog_information->getInformationLayoutId($this->request->get['information_id']);
		}
if(catalog_controller_common_top__index){
$log = fopen(DIR_LOGS . 'catalog_controller_common_top__index.log', 'a');
fwrite($log,'++++++++++++++++++++++++++++++++++++++++++++++++++++' . chr(13) . chr(13));
fwrite($log, '$layout_id)' . $layout_id . chr(13) . chr(13));
fclose($log);}

		if (!$layout_id) {
			$layout_id = $this->model_design_layout->getLayout($route);
		}

		if (!$layout_id) {
			$layout_id = $this->config->get('config_layout_id');
		}
if(catalog_controller_common_top__index){
$log = fopen(DIR_LOGS . 'catalog_controller_common_top__index.log', 'a');
fwrite($log,'++++++++++++++++++++++++++++++++++++++++++++++++++++' . chr(13) . chr(13));
fwrite($log, '$layout_id)' . $layout_id . chr(13) . chr(13));
fclose($log);}

		$this->load->model('setting/module');

		$data['modules'] = array();

		$modules = $this->model_design_layout->getLayoutModules($layout_id, 'top');
if(catalog_controller_common_top__index){
$log = fopen(DIR_LOGS . 'catalog_controller_common_top__index.log', 'a');
fwrite($log,'++++++++++++++++++++++++++++++++++++++++++++++++++++' . chr(13) . chr(13));
fwrite($log, '$modules)' . print_r($modules,true) . chr(13) . chr(13));
fclose($log);}

		foreach ($modules as $module) {
			$part = explode('.', $module['code']);
if(catalog_controller_common_top__index){
$log = fopen(DIR_LOGS . 'catalog_controller_common_top__index.log', 'a');
fwrite($log,'++++++++++++++++++++++++++++++++++++++++++++++++++++' . chr(13) . chr(13));
fwrite($log, '$part)' . print_r($part,true) . chr(13) . chr(13));
fclose($log);}

			if (isset($part[0]) && $this->config->get('module_' . $part[0] . '_status')) {
				$module_data = $this->load->controller('extension/module/' . $part[0]);
if(catalog_controller_common_top__index){
$log = fopen(DIR_LOGS . 'catalog_controller_common_top__index.log', 'a');
fwrite($log,'++++++++++++++++++++++++++++++++++++++++++++++++++++' . chr(13) . chr(13));
fwrite($log, '$module_data)' . print_r($module_data,true) . chr(13) . chr(13));
fclose($log);}

				if ($module_data) {
					$data['modules'][] = $module_data;
				}
			}

			if (isset($part[1])) {
				$setting_info = $this->model_setting_module->getModule($part[1]);
if(catalog_controller_common_top__index){
$log = fopen(DIR_LOGS . 'catalog_controller_common_top__index.log', 'a');
fwrite($log,'++++++++++++++++++++++++++++++++++++++++++++++++++++' . chr(13) . chr(13));
fwrite($log, '$setting_info)' . print_r($setting_info,true) . chr(13) . chr(13));
fclose($log);}

				if ($setting_info && $setting_info['status']) {
					$output = $this->load->controller('extension/module/' . $part[0], $setting_info);
if(catalog_controller_common_top__index){
$log = fopen(DIR_LOGS . 'catalog_controller_common_top__index.log', 'a');
fwrite($log,'++++++++++++++++++++++++++++++++++++++++++++++++++++' . chr(13) . chr(13));
fwrite($log, '$output)' . print_r($output,true) . chr(13) . chr(13));
fclose($log);}

					if ($output) {
						$data['modules'][] = $output;
					}
				}
			}
		}
if(catalog_controller_common_top__index){
$log = fopen(DIR_LOGS . 'catalog_controller_common_top__index.log', 'a');
fwrite($log,'====================================================' . chr(13) . chr(13));
fwrite($log, '$data)' . print_r($data,true) . chr(13) . chr(13));
fclose($log);}

		return $this->load->view('common/position', $data);
	}
}

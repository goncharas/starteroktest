<?php
class ControllerCommonHome extends Controller {
	public function index() {
if(catalog_controller_common_home__index){
$log = fopen(DIR_LOGS . 'catalog_controller_common_home__index.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fclose($log);}
		$this->document->setTitle($this->config->get('config_meta_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));

		if (isset($this->request->get['route'])) {
			$canonical = $this->url->link('common/home');
if(catalog_controller_common_home__index){
$log = fopen(DIR_LOGS . 'catalog_controller_common_home__index.log', 'a');
fwrite($log,' $this->request->get[route] ) ' . print_r($this->request->get['route'],true) . '==' . chr(10) . chr(13));
fwrite($log,' $canonical ) ' . print_r($canonical,true) . '==' . chr(10) . chr(13));
fclose($log);}
      if ($this->request->get['route'] == 'common/home' && $canonical == rtrim($canonical, '/'))
        $canonical .= '/';    
			if ($this->request->get['route'] != 'common/home' && $this->config->get('config_seo_pro') && !$this->config->get('config_seopro_addslash')) {
				$canonical = rtrim($canonical, '/');
			}
			$this->document->addLink($canonical, 'canonical');
		}

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');

				$data['top']    = $this->load->controller('common/top');
				// $data['header_top']    = $this->load->controller('common/header_top');
				$data['navigation'] = $this->load->controller('common/navigation');
				$data['bottom'] = $this->load->controller('common/bottom');
				
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		// $this->response->setOutput($this->load->view('common/home', $data));
if(catalog_controller_common_home__index){
$log = fopen(DIR_LOGS . 'catalog_controller_common_home__index.log', 'a');
fwrite($log,' RETURN $data ) ' . print_r($data,true) . '==' . chr(10) . chr(13));}
		$html = $this->load->view('common/home', $data);
if(catalog_controller_common_home__index){
fwrite($log,' RETURN $html ) ' . $html . '==' . chr(10) . chr(13));
fclose($log);}
		$this->response->setOutput($html);
	}
}
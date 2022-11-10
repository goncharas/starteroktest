<?php
class ControllerReportPartnerprice extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('report/partnerprice');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('report/partnerprice');

		$this->getList();
	}


	protected function getList() {
if(admin_controller_report_partnerprice__getList){
$log = fopen(DIR_LOGS . 'admin_controller_report_partnerprice__getList.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,' $this->request->get )' . print_r($this->request->get, true) . '==' . chr(10) . chr(13));
fwrite($log,' $this->request->post )' . print_r($this->request->post, true) . '==' . chr(10) . chr(13));
fclose($log);}
		if (isset($this->request->post['deviation'])) {
			$deviation = $this->request->post['deviation'];
		} else if (isset($this->request->get['deviation'])) {
			$deviation = $this->request->get['deviation'];
		} else {
			$deviation = '20';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'partner_brand';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->post['deviation'])) {
			$url .= '&deviation=' . $this->request->post['deviation'];
		} else if (isset($this->request->get['deviation'])) {
			$url .= '&deviation=' . $this->request->get['deviation'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('report/partnerprice', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);
    $data['refresh'] = $this->url->link('report/partnerprice', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['partnerprices'] = array();

		$filter_data = array(
      'deviation'  => $deviation, 
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);
if(admin_controller_report_partnerprice__getList){
$log = fopen(DIR_LOGS . 'admin_controller_report_partnerprice__getList.log', 'a');
fwrite($log,' =========================== ' . '==' . chr(10) . chr(13));
fwrite($log,' $url )' . print_r($url, true) . '==' . chr(10) . chr(13));
fwrite($log,' $filter_data )' . print_r($filter_data, true) . '==' . chr(10) . chr(13));
fclose($log);}

		$partnerbrand_total = $this->model_report_partnerprice->getTotalpartnerprices($filter_data);
if(admin_controller_report_partnerprice__getList){
$log = fopen(DIR_LOGS . 'admin_controller_report_partnerprice__getList.log', 'a');
fwrite($log,' =========================== ' . '==' . chr(10) . chr(13));
fwrite($log,' $partnerbrand_total = $this->model_report_partnerprice->getTotalpartnerprices($filter_data); )' . print_r($partnerbrand_total, true) . '==' . chr(10) . chr(13));
fclose($log);}

		$results = $this->model_report_partnerprice->getpartnerprices($filter_data);
if(admin_controller_report_partnerprice__getList){
$log = fopen(DIR_LOGS . 'admin_controller_report_partnerprice__getList.log', 'a');
fwrite($log,' =========================== ' . '==' . chr(10) . chr(13));
fwrite($log,' $results = $this->model_report_partnerprice->getpartnerprices($filter_data); )' . print_r($results, true) . '==' . chr(10) . chr(13));
fclose($log);}

		foreach ($results as $result) {
			$data['partnerprices'][] = array(
				'product_id'        => $result['product_id'],
				'name'              => $result['name'],
				'cgd_name'          => $result['cgd_name'],
				'partner_brand'     => $result['partner_brand'],
				'location'          => $result['location'],
				'minprice'          => $result['minprice'],
				'maxprice'          => $result['maxprice'],
				'deviation'         => $result['deviation'],
				'edit'              => $this->url->link('catalog/product/edit', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $result['product_id'] . $url, true)
			);
		}

    $data['deviation'] = $deviation;

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

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if (isset($this->request->post['deviation'])) {
			$url .= '&deviation=' . $this->request->post['deviation'];
		} else if (isset($this->request->get['deviation'])) {
			$url .= '&deviation=' . $this->request->get['deviation'];
		}
    
		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_location'] = $this->url->link('report/partnerprice', 'user_token=' . $this->session->data['user_token'] . '&sort=location' . $url, true);
		$data['sort_name'] = $this->url->link('report/partnerprice', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
		$data['sort_cgd_name'] = $this->url->link('report/partnerprice', 'user_token=' . $this->session->data['user_token'] . '&sort=cgd_name' . $url, true);
		$data['sort_partner_brand'] = $this->url->link('report/partnerprice', 'user_token=' . $this->session->data['user_token'] . '&sort=partner_brand' . $url, true);

		$url = '';

		if (isset($this->request->post['deviation'])) {
			$url .= '&deviation=' . $this->request->post['deviation'];
		} else if (isset($this->request->get['deviation'])) {
			$url .= '&deviation=' . $this->request->get['deviation'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
    

		$data['sort'] = $sort;
		$data['order'] = $order;
    
    
if(admin_controller_report_partnerprice__getList){
$log = fopen(DIR_LOGS . 'admin_controller_report_partnerprice__getList.log', 'a');
fwrite($log,' =========================== ' . '==' . chr(10) . chr(13));
fwrite($log,' $url )' . print_r($url, true) . '==' . chr(10) . chr(13));
fwrite($log,' $data )' . print_r($data, true) . '==' . chr(10) . chr(13));
fclose($log);}

		$pagination = new Pagination();
		$pagination->total = $partnerbrand_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('report/partnerprice', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($partnerbrand_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($partnerbrand_total - $this->config->get('config_limit_admin'))) ? $partnerbrand_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $partnerbrand_total, ceil($partnerbrand_total / $this->config->get('config_limit_admin')));

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('report/partnerprice', $data));
	}
}
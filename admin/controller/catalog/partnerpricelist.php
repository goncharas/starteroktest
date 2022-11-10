<?php
class ControllerCatalogpartnerpricelist extends Controller {
	private $error = array();
	

	public function index() {
//		$this->load->language('catalog/category');

//		$this->document->setTitle('Закачка цены');

if(admin_controller_catalog_partnerpricelist__getList){
$log = fopen(DIR_LOGS . 'admin_controller_catalog_partnerpricelist__getList.log', 'w');
fwrite($log,'**********************************' . chr(13) . chr(13));
$t = microtime(true);
$micro = sprintf("%06d",($t - floor($t)) * 1000000);
$d = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );
fwrite($log, $d->format("Y-m-d H:i:s.u") . '==' . chr(10) . chr(13));
fwrite($log,'$this->request->get) ' . print_r($this->request->get,true) . chr(13) . chr(13));
fwrite($log,'$this->request->post) ' . print_r($this->request->post,true) . chr(13) . chr(13));
fclose($log);}
    $data = array();

		$data['user_token'] = $this->session->data['user_token'];


		if (isset($this->error['warning'])) {

			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success_browse'] = $this->session->data['success_browse'];
			unset($this->session->data['success_browse']);
		} else {
			$data['success_browse'] = '';
		}

    $this->load->model('catalog/partnerprice');
    if (true || ($this->request->server['REQUEST_METHOD'] == 'GET')) { 
      if ( !empty($this->request->get['location_id'])) {
           $location_id = $this->request->get['location_id']; }
      else if(!empty($this->session->data['location_id'])){
           $location_id = $this->session->data['location_id'];
           unset($this->session->data['location_id']);
      }
      if(!isset($location_id) ||     
           ($this->model_catalog_partnerprice->getLocation2Currency($location_id) == false )
          ) {
        $data['warning'] = 'Выбор Партнера Обязателен';
            } else {
              
      $data['location_id'] = $location_id;
    
  		if (isset($this->request->get['sort'])) {
	 		   $sort = $this->request->get['sort'];
		  } else {
			   $sort = 'partnerprice_id';
		  }
      $data['sort'] = $sort;

		  if (isset($this->request->get['order'])) {
			   $order = $this->request->get['order'];
		  } else {
			   $order = 'ASC';
		  }
      $data['order'] = $order;

		  if (isset($this->request->get['page'])) {
			   $page = (int)$this->request->get['page'];
		  } else {
			   $page = 1;
		  }
      $data['page'] = empty($page) ? 1 : $page;
    
	    $filter_data = array(
			   'sort'  => $sort,
			   'order' => $order,
			   'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			   'limit' => $this->config->get('config_limit_admin')
		  );
if(admin_controller_catalog_partnerpricelist__getList){
$log = fopen(DIR_LOGS . 'admin_controller_catalog_partnerpricelist__getList.log', 'a');
fwrite($log,'**********************************' . chr(13) . chr(13));
fwrite($log,'$filter_data) ' . print_r($filter_data,true) . chr(13) . chr(13));
fclose($log);}

//		  $this->load->model('catalog/partnerprice');
		  $data['currencies'] = $this->model_catalog_partnerprice->getCurrencies();
      foreach($data['currencies'] as $curr){
        if($curr['id'] == $location_id)
          $data['title'] = $curr['title'];
      }
      $data['pumpprice_currency'] = $location_id;
      $data['partnerprice_list'] = $this->model_catalog_partnerprice->getpartnerprice_list($location_id, $filter_data);

      $total = $this->model_catalog_partnerprice->getpartnerprice_total($location_id, $filter_data);;

		  $pagination = new Pagination();
		  $pagination->total = $total;
		  $pagination->page = $page;
		  $pagination->limit = $this->config->get('config_limit_admin');
		  $pagination->url = 'getpartnerprice_list(\'{page}\')';

		  $pagination = $pagination->render();
      $pagination = str_replace('href="','href="#" onclick="',$pagination);
      $pagination = str_replace(')">','); return false;">',$pagination);
      $data['pagination'] = $pagination;
if(admin_controller_catalog_partnerpricelist__getList){
$log = fopen(DIR_LOGS . 'admin_controller_catalog_partnerpricelist__getList.log', 'a');
fwrite($log,'**********************************' . chr(13) . chr(13));
fwrite($log,'$data ) ' . print_r($data,true) . chr(13) . chr(13));
fclose($log);}
    } }
    $html = $this->load->view('catalog/partnerpricelist', $data);
//		$this->response->setOutput($this->load->view('catalog/partnerpricelist', $data));   
if(admin_controller_catalog_partnerpricelist__getList){
$log = fopen(DIR_LOGS . 'admin_controller_catalog_partnerpricelist__getList.log', 'a');
fwrite($log,'**********************************' . chr(13) . chr(13));
fwrite($log,'$html ) ' . print_r($html,true) . chr(13) . chr(13));
fclose($log);}
    return $html;
	}

	
}

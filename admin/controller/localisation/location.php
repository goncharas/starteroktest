<?php
class ControllerLocalisationLocation extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('localisation/location');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('localisation/location');

		$this->getList();
	}

	public function add() {
		$this->load->language('localisation/location');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('localisation/location');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_localisation_location->addLocation($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('localisation/location', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('localisation/location');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('localisation/location');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_localisation_location->editLocation($this->request->get['location_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('localisation/location', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('localisation/location');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('localisation/location');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $location_id) {
				$this->model_localisation_location->deleteLocation($location_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('localisation/location', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
$log = fopen(DIR_LOGS . 'admin_controller_localisation_location__getList.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,' $this->request->get )' . print_r($this->request->get, true) . '==' . chr(10) . chr(13));
fclose($log);
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
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

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] =   array();

		$data['breadcrumbs'][] =   array(
			'text' =>  $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] =   array(
			'text' =>  $this->language->get('heading_title'),
			'href' =>  $this->url->link('localisation/location', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('localisation/location/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('localisation/location/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['location'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$location_total = $this->model_localisation_location->getTotalLocations();

		$results = $this->model_localisation_location->getLocations($filter_data);

		foreach ($results as $result) {
			$data['location'][] =   array(
				'location_id' => $result['location_id'],
				'name'        => $result['name'],
				'address'     => $result['address'],
				'edit'        => $this->url->link('localisation/location/edit', 'user_token=' . $this->session->data['user_token'] . '&location_id=' . $result['location_id'] . $url, true)
			);
		}

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

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('localisation/location', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
		$data['sort_address'] = $this->url->link('localisation/location', 'user_token=' . $this->session->data['user_token'] . '&sort=address' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $location_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('localisation/location', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($location_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($location_total - $this->config->get('config_limit_admin'))) ? $location_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $location_total, ceil($location_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;
$log = fopen(DIR_LOGS . 'admin_controller_localisation_location__getList.log', 'a');
fwrite($log,' $data )' . print_r($data, true) . '==' . chr(10) . chr(13));
fclose($log);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('localisation/location_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['location_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		if (isset($this->error['address'])) {
			$data['error_address'] = $this->error['address'];
		} else {
			$data['error_address'] = '';
		}

		if (isset($this->error['telephone'])) {
			$data['error_telephone'] = $this->error['telephone'];
		} else {
			$data['error_telephone'] = '';
		}

		if (isset($this->error['fax'])) {
			$data['error_fax'] = $this->error['fax'];
		} else {
			$data['error_fax'] = '';
		}

		if (isset($this->error['geocode'])) {
			$data['error_geocode'] = $this->error['geocode'];
		} else {
			$data['error_geocode'] = '';
		}

		if (isset($this->error['open'])) {
			$data['error_open'] = $this->error['open'];
		} else {
			$data['error_open'] = '';
		}

		if (isset($this->error['comment'])) {
			$data['error_comment'] = $this->error['comment'];
		} else {
			$data['error_comment'] = '';
		}

		if (isset($this->error['viber'])) {
			$data['error_viber'] = $this->error['viber'];
		} else {
			$data['error_viber'] = '';
		}

		if (isset($this->error['telegram'])) {
			$data['error_telegram'] = $this->error['telegram'];
		} else {
			$data['error_telegram'] = '';
		}

		if (isset($this->error['whatsapp'])) {
			$data['error_whatsapp'] = $this->error['whatsapp'];
		} else {
			$data['error_whatsapp'] = '';
		}

		if (isset($this->error['skype'])) {
			$data['error_skype'] = $this->error['skype'];
		} else {
			$data['error_skype'] = '';
		}

		if (isset($this->error['youtube_id'])) {
			$data['error_youtube_id'] = $this->error['youtube_id'];
		} else {
			$data['error_youtube_id'] = '';
		}

		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}

		if (!empty($this->error['description_city'])) {
			$data['error_description_city'] = $this->error['description_city'];
		} else {
			$data['error_description_city'] = '';
		}		

		if (!empty($this->error['description_comment'])) {
			$data['error_description_comment'] = $this->error['description_comment'];
		} else {
			$data['error_description_comment'] = '';
		}		

		if (!empty($this->error['description_name'])) {
			$data['error_description_name'] = $this->error['description_name'];
		} else {
			$data['error_description_name'] = '';
		}		

		if (!empty($this->error['description_email'])) {
			$data['error_description_email'] = $this->error['description_email'];
		} else {
			$data['error_description_email'] = '';
		}		

		if (!empty($this->error['description_location_type'])) {
			$data['error_description_location_type'] = $this->error['description_location_type'];
		} else {
			$data['error_description_location_type'] = '';
		}		

		if (!empty($this->error['shipping_term'])) {
			$data['error_shipping_term'] = $this->error['shipping_term'];
		} else {
			$data['error_shipping_term'] = '';
		}		

		if (!empty($this->error['stock_lot'])) {
			$data['error_stock_lot'] = $this->error['stock_lot'];
		} else {
			$data['error_stock_lot'] = '';
		}		


		$url = '';

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
			'href' => $this->url->link('localisation/location', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['location_id'])) {
			$data['action'] = $this->url->link('localisation/location/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('localisation/location/edit', 'user_token=' . $this->session->data['user_token'] .  '&location_id=' . $this->request->get['location_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('localisation/location', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['location_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$location_info = $this->model_localisation_location->getLocation($this->request->get['location_id']);
$log = fopen(DIR_LOGS . 'admin_controller_localisation_location__getForm.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,' $location_info )' . print_r($location_info, true) . '==' . chr(10) . chr(13));
fclose($log);
		} else {
$log = fopen(DIR_LOGS . 'admin_controller_localisation_location__getForm.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,' $this->request->post )' . print_r($this->request->post, true) . '==' . chr(10) . chr(13));
fclose($log);
		}

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('setting/store');


		if (isset($this->request->post['category'])) {
			$data['category'] = $this->request->post['category'];
			if (isset($this->request->get['sub_category'])) {
				$data['sub_category'] = $this->request->post['sub_category'];
      } 
		} elseif (!empty($location_info)) {
			$data['category'] = $location_info['category'];
			if (!empty($location_info['category'])) {
				$data['sub_category'] = '1';
      } 
		} else {
			$data['category'] =   '';
			$data['sub_category'] = '0';
		}

		if (isset($this->request->post['category_id'])) {
			$data['category_id'] = $this->request->post['category_id'];
		} elseif (!empty($location_info)) {
			$data['category_id'] = $location_info['category_id'];
		} else {
			$data['category_id'] =   '';
		}



		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($location_info)) {
			$data['name'] = $location_info['name'];
		} else {
			$data['name'] =   '';
		}

		if (isset($this->request->post['address'])) {
			$data['address'] = $this->request->post['address'];
		} elseif (!empty($location_info)) {
			$data['address'] = $location_info['address'];
		} else {
			$data['address'] = '';
		}

		if (isset($this->request->post['geocode'])) {
			$data['geocode'] = $this->request->post['geocode'];
		} elseif (!empty($location_info)) {
			$data['geocode'] = $location_info['geocode'];
		} else {
			$data['geocode'] = '49.986279, 36.202844';
		}

		if (isset($this->request->post['telephone'])) {
			$data['telephone'] = $this->request->post['telephone'];
		} elseif (!empty($location_info)) {
			$data['telephone'] = // htmlspecialchars
								htmlspecialchars(html_entity_decode ($location_info['telephone'],ENT_HTML5|ENT_QUOTES));
		} else {
			$data['telephone'] = '';
		}
		
		if (isset($this->request->post['fax'])) {
			$data['fax'] = $this->request->post['fax'];
		} elseif (!empty($location_info)) {
			$data['fax'] = $location_info['fax'];
		} else {
			$data['fax'] = '';
		}


		
		if (isset($this->request->post['path'])) {
			$data['path'] = $this->request->post['path'];
		} elseif (!empty($location_info)) {
			$data['path'] = htmlspecialchars(html_entity_decode ($location_info['path'],ENT_HTML5|ENT_QUOTES));
		} else {
			$data['path'] = '';
		}

		if (isset($this->request->post['parent_id'])) {
			$data['parent_id'] = $this->request->post['parent_id'];
		} elseif (!empty($location_info)) {
			$data['parent_id'] = $location_info['parent_id'];
		} else {
			$data['parent_id'] = '';
		}

		if (isset($this->request->post['is_partner'])) {
			$data['is_partner'] = $this->request->post['is_partner'];
		} elseif (!empty($location_info)) {
			$data['is_partner'] = $location_info['is_partner'];
		} else {
			$data['is_partner'] = '0';
		}

		if (isset($this->request->post['currency_code'])) {
			$data['currency_code'] = $this->request->post['currency_code'];
		} elseif (!empty($location_info)) {
			$data['currency_code'] = $location_info['currency_code'];
		} else {
			$data['currency_code'] = '';
		}

		$data['currencies'] = array();
		$results = $this->model_localisation_location->getCurrencies();
    if(!empty($results))
      $data['currencies'] = $results;


		if (isset($this->request->post['shipping_term'])) {
			$data['shipping_term'] = $this->request->post['shipping_term'];
		} elseif (!empty($location_info)) {
			$data['shipping_term'] = $location_info['shipping_term'];
		} else {
			$data['shipping_term'] = '';
		}

		$data['currencies'] = array();
		$results = $this->model_localisation_location->getCurrencies();
    if(!empty($results))
      $data['currencies'] = $results;


		
		if (isset($this->request->post['order'])) {
			$data['order'] = $this->request->post['order'];
		} elseif (!empty($location_info)) {
			$data['order'] = $location_info['order'];
		} else {
			$data['order'] = '';
		}
		
		if (isset($this->request->post['google_map_iframe_src'])) {
			$data['google_map_iframe_src'] = $this->request->post['google_map_iframe_src'];
		} elseif (!empty($location_info)) {
			$data['google_map_iframe_src'] = html_entity_decode($location_info['google_map_iframe_src'],ENT_HTML5);
		} else {
			$data['google_map_iframe_src'] = '';
		}
		
		if (isset($this->request->post['viber'])) {
			$data['viber'] = $this->request->post['viber'];
		} elseif (!empty($location_info)) {
			$data['viber'] = html_entity_decode($location_info['viber'],ENT_HTML5);
		} else {
			$data['viber'] = '';
		}
		
		if (isset($this->request->post['telegram'])) {
			$data['telegram'] = $this->request->post['telegram'];
		} elseif (!empty($location_info)) {
			$data['telegram'] = html_entity_decode($location_info['telegram'],ENT_HTML5);
		} else {
			$data['telegram'] = '';
		}
		
		if (isset($this->request->post['whatsapp'])) {
			$data['whatsapp'] = $this->request->post['whatsapp'];
		} elseif (!empty($location_info)) {
			$data['whatsapp'] = html_entity_decode($location_info['whatsapp'],ENT_HTML5);
		} else {
			$data['whatsapp'] = '';
		}
		
		if (isset($this->request->post['skype'])) {
			$data['skype'] = $this->request->post['skype'];
		} elseif (!empty($location_info)) {
			$data['skype'] = html_entity_decode($location_info['skype'],ENT_HTML5);
		} else {
			$data['skype'] = '';
		}

		
		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} elseif (!empty($location_info)) {
			$data['image'] = $location_info['image'];
		} else {
			$data['image'] = '';
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
		} elseif (!empty($location_info) && is_file(DIR_IMAGE . $location_info['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($location_info['image'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		if (isset($this->request->post['open'])) {
			$data['open'] = $this->request->post['open'];
		} elseif (!empty($location_info)) {
			$data['open'] = $location_info['open'];
		} else {
			$data['open'] = '';
		}

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();
		
		$data['reference_location_type'] = $this->config->get('config_location_type');

		if (isset($this->request->post['location_description'])) {
			$data['location_description'] = $this->request->post['location_description'];
		} elseif (isset($this->request->get['location_id'])) {
			$data['location_description'] = $this->model_localisation_location->getLocationDescriptions($this->request->get['location_id']);
		} else {
			$data['location_description'] = array();
		}
		
		
		if (isset($this->request->post['comment'])) {
			$data['comment'] = $this->request->post['comment'];
		} elseif (!empty($location_info)) {
			$data['comment'] = $location_info['comment'];
		} else {
			$data['comment'] = '';
		}
		
		
		if (isset($this->request->post['youtube_id'])) {
			$data['youtube_id'] = $this->request->post['youtube_id'];
		} elseif (!empty($location_info)) {
			$data['youtube_id'] = html_entity_decode($location_info['youtube_id'],ENT_HTML5);
		} else {
			$data['youtube_id'] = '';
		}
		
		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} elseif (!empty($location_info)) {
			$data['email'] = $location_info['email'];
		} else {
			$data['email'] = '';
		}
	

		if (isset($this->request->post['stock_lot'])) {
			$data['stock_lot'] = $this->request->post['stock_lot'];
		} elseif (!empty($location_info)) {
			$data['stock_lot'] = $location_info['stock_lot'];
		} else {
			$data['stock_lot'] =   '';
		}
  
  	
		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($location_info)) {
			$data['status'] = $location_info['status'];
		} else {
			$data['status'] = true;
		}
$log = fopen(DIR_LOGS . 'admin_controller_localisation_location__getForm.log', 'a');
// fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
// fwrite($log,' $filter_data )' . print_r($filter_data, true) . '==' . chr(10) . chr(13));
fwrite($log,' $data )' . print_r($data, true) . '==' . chr(10) . chr(13));
fclose($log);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('localisation/location_form', $data));
	}

	protected function validateForm() {
$log = fopen(DIR_LOGS . 'admin_controller_localisation_location__validateForm.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,' $this->request->post )' . print_r($this->request->post, true) . '==' . chr(10) . chr(13));
fwrite($log,' $this->error )' . print_r($this->error, true) . '==' . chr(10) . chr(13));
fclose($log);
		if (!$this->user->hasPermission('modify', 'localisation/location')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 32)) {
			$this->error['name'] = sprintf($this->language->get('error_length_between'),'name',3,32);
		}

//		if ((utf8_strlen($this->request->post['address']) < 3) || (utf8_strlen($this->request->post['address']) > 1332)) {
//			$this->error['address'] = sprintf($this->language->get('error_length_between'),'address',3,1332);
//		}

		if (!empty(utf8_strlen($this->request->post['telephone'])) && (utf8_strlen($this->request->post['telephone']) > 300)) {
			$this->error['telephone'] = sprintf($this->language->get('error_length_between'),'telephone',3,300);
		}

		if (!empty(utf8_strlen($this->request->post['fax'])) && (utf8_strlen($this->request->post['fax']) > 32)) {
			$this->error['fax'] = sprintf($this->language->get('error_length_between'),'fax',3,32);
		}

		if (!empty(utf8_strlen($this->request->post['geocode'])) && (utf8_strlen($this->request->post['geocode']) > 32)) {
			$this->error['geocode'] = sprintf($this->language->get('error_length_between'),'geocode',3,32);
		}

//		if ((utf8_strlen($this->request->post['open']) < 3) || (utf8_strlen($this->request->post['open']) > 1332)) {
//			$this->error['open'] = sprintf($this->language->get('error_length_between'),'open',3,1332);
//		}

//		if ((utf8_strlen($this->request->post['comment']) < 3) || (utf8_strlen($this->request->post['comment']) > 1332)) {
//			$this->error['comment'] = sprintf($this->language->get('error_length_between'),'comment',3,1332);
//		}

		if (!empty(utf8_strlen($this->request->post['viber'])) && (utf8_strlen($this->request->post['viber']) > 32)) {
			$this->error['viber'] = sprintf($this->language->get('error_length'),'viber',32);
		}

		if (!empty(utf8_strlen($this->request->post['telegram'])) && (utf8_strlen($this->request->post['telegram']) > 32)) {
			$this->error['telegram'] = sprintf($this->language->get('error_length'),'telegram',32);
		}

		if (!empty(utf8_strlen($this->request->post['whatsapp'])) && (utf8_strlen($this->request->post['whatsapp']) > 32)) {
			$this->error['whatsapp'] = sprintf($this->language->get('error_length'),'whatsapp',32);
		}

		if (!empty(utf8_strlen($this->request->post['skype'])) && (utf8_strlen($this->request->post['skype']) > 32)) {
			$this->error['skype'] = sprintf($this->language->get('error_length'),'skype',32);
		}

		if (!empty(utf8_strlen($this->request->post['youtube_id'])) && (utf8_strlen($this->request->post['youtube_id']) > 32)) {
			$this->error['youtube_id'] = sprintf($this->language->get('error_length'),'youtube_id',32);
		}

		if (!empty(utf8_strlen($this->request->post['email'])) && (utf8_strlen($this->request->post['email']) > 32)) {
			$this->error['email'] = sprintf($this->language->get('error_length'),'email',32);
		}

		if (!empty(utf8_strlen($this->request->post['shipping_term'])) && (utf8_strlen($this->request->post['shipping_term']) > 20)) {
			$this->error['shipping_term'] = sprintf($this->language->get('error_length'),'shipping_term',20);
		}

        if(!empty($this->request->post['location_description'])){
			$this->error['description_city'] = array();
			$this->error['description_position'] = array();
			$this->error['description_name'] = array();
			$this->error['description_email'] = array();
			$this->error['description_location_type'] = array();
			foreach($this->request->post['location_description'] as $language_id => $value){
				if (!empty(utf8_strlen($value['city'])) && (utf8_strlen($value['city']) > 45))
					$this->error['description_city'][$language_id] = sprintf($this->language->get('error_length'),'description_city',45);
				if (!empty(utf8_strlen($value['comment'])) && (utf8_strlen($value['comment']) > 45))
					$this->error['description_position'][$language_id] = sprintf($this->language->get('error_length'),'description_position',45);
				if (!empty(utf8_strlen($value['name'])) && (utf8_strlen($value['name']) > 32))
					$this->error['description_name'][$language_id] = sprintf($this->language->get('error_length'),'description_name',32);
				if (!empty(utf8_strlen($value['email'])) && (utf8_strlen($value['email']) > 32))
					$this->error['description_email'][$language_id] = sprintf($this->language->get('error_length'),'description_email',32);
				if ((utf8_strlen($value['location_type']) < 3) || (utf8_strlen($value['location_type']) > 32))
					$this->error['description_location_type'][$language_id] = sprintf($this->language->get('error_length_between'),'description_location_type',3,32);
			}
		}
		if(empty($this->error['description_city']))
			unset($this->error['description_city']);
		if(empty($this->error['description_comment']))
			unset($this->error['description_comment']);
		if(empty($this->error['description_name']))
			unset($this->error['description_name']);
		if(empty($this->error['description_position']))
			unset($this->error['description_position']);
		if(empty($this->error['description_email']))
			unset($this->error['description_email']);
		if(empty($this->error['description_location_type']))
			unset($this->error['description_location_type']);
		
$log = fopen(DIR_LOGS . 'admin_controller_localisation_location__validateForm.log', 'a');
fwrite($log,'RETURN $this->error )' . print_r($this->error, true) . '==' . chr(10) . chr(13));
fclose($log);
		
		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'localisation/location')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
	
	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('localisation/location');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'sort'        => 'name',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_localisation_location->getLocations($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'location_id' => $result['location_id'],
					'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
}
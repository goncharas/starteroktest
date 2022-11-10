<?php
class ControllerLocalisationEmployee extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('localisation/employee');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('localisation/employee');

		$this->getList();
	}

	public function add() {
		$this->load->language('localisation/employee');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('localisation/employee');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_localisation_employee->addEmployee($this->request->post);

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

			$this->response->redirect($this->url->link('localisation/employee', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('localisation/employee');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('localisation/employee');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_localisation_employee->editEmployee($this->request->get['employee_id'], $this->request->post);

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

			$this->response->redirect($this->url->link('localisation/employee', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('localisation/employee');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('localisation/employee');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $employee_id) {
				$this->model_localisation_employee->deleteEmployee($employee_id);
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

			$this->response->redirect($this->url->link('localisation/employee', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
$log = fopen(DIR_LOGS . 'admin_controller_localisation_employee__getList.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,' $this->request->get )' . print_r($this->request->get, true) . '==' . chr(10) . chr(13));
fclose($log);
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'nickname';
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
			'href' =>  $this->url->link('localisation/employee', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('localisation/employee/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('localisation/employee/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['employees'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$employee_total = $this->model_localisation_employee->getTotalEmployees();

		$results = $this->model_localisation_employee->getEmployees($filter_data);
$log = fopen(DIR_LOGS . 'admin_controller_localisation_employee__getList.log', 'a');
fwrite($log,' $this->model_localisation_employee->getEmployees )' . print_r($results, true) . '==' . chr(10) . chr(13));
fclose($log);

		foreach ($results as $result) {
			$data['employees'][] =   array(
				'employee_id' => $result['employee_id'],
				'nickname'    => $result['nickname'],
			    'name'        => $result['name'],
				'path'        => $result['path'],
				'edit'        => $this->url->link('localisation/employee/edit', 'user_token=' . $this->session->data['user_token'] . '&employee_id=' . $result['employee_id'] . $url, true)
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

		$data['sort_name'] = $this->url->link('localisation/employee', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
		$data['sort_path'] = $this->url->link('localisation/employee', 'user_token=' . $this->session->data['user_token'] . '&sort=path' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $employee_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('localisation/employee', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($employee_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($employee_total - $this->config->get('config_limit_admin'))) ? $employee_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $employee_total, ceil($employee_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;
$log = fopen(DIR_LOGS . 'admin_controller_localisation_employee__getList.log', 'a');
fwrite($log,' $data )' . print_r($data, true) . '==' . chr(10) . chr(13));
fclose($log);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('localisation/employee_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['employee_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['nickname'])) {
			$data['error_nickname'] = $this->error['nickname'];
		} else {
			$data['error_nickname'] = '';
		}

		if (isset($this->error['path'])) {
			$data['error_path'] = $this->error['path'];
		} else {
			$data['error_path'] = '';
		}

		if (!empty($this->error['contact_telephone'])) {
			$data['error_contact_telephone'] = $this->error['contact_telephone'];
		} else {
			$data['error_contact_telephone'] = '';
		}

		if (!empty($this->error['contact_viber'])) {
			$data['error_contact_viber'] = $this->error['contact_viber'];
		} else {
			$data['error_contact_viber'] = '';
		}

		if (!empty($this->error['contact_telegram'])) {
			$data['error_contact_telegram'] = $this->error['contact_telegram'];
		} else {
			$data['error_contact_telegram'] = '';
		}

		if (!empty($this->error['contact_whatsapp'])) {
			$data['error_contact_whatsapp'] = $this->error['contact_whatsapp'];
		} else {
			$data['error_contact_whatsapp'] = '';
		}

		if (!empty($this->error['details_name'])) {
			$data['error_details_name'] = $this->error['details_name'];
		} else {
			$data['error_details_name'] = '';
		}

		if (!empty($this->error['details_position'])) {
			$data['error_details_position'] = $this->error['details_position'];
		} else {
			$data['error_details_position'] = '';
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
			'href' => $this->url->link('localisation/employee', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['employee_id'])) {
			$data['action'] = $this->url->link('localisation/employee/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('localisation/employee/edit', 'user_token=' . $this->session->data['user_token'] .  '&employee_id=' . $this->request->get['employee_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('localisation/employee', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['employee_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$employee_info = $this->model_localisation_employee->getEmployee($this->request->get['employee_id']);
$log = fopen(DIR_LOGS . 'admin_controller_localisation_employee__getForm.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,' $employee_info )' . print_r($employee_info, true) . '==' . chr(10) . chr(13));
fclose($log);
		} else {
$log = fopen(DIR_LOGS . 'admin_controller_localisation_employee__getForm.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,' $this->request->post )' . print_r($this->request->post, true) . '==' . chr(10) . chr(13));
fclose($log);
		}

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('setting/store');

		if (isset($this->request->post['path'])) {
			$data['path'] = $this->request->post['path'];
		} elseif (!empty($employee_info)) {
			$data['path'] = htmlspecialchars(html_entity_decode ($employee_info['path'],ENT_HTML5|ENT_QUOTES));
		} else {
			$data['path'] = '';
		}

		if (isset($this->request->post['department_id'])) {
			$data['department_id'] = $this->request->post['department_id'];
		} elseif (!empty($employee_info)) {
			$data['department_id'] = $employee_info['department_id'];
		} else {
			$data['department_id'] = '';
		}

		if (isset($this->request->post['nickname'])) {
			$data['nickname'] = $this->request->post['nickname'];
		} elseif (!empty($employee_info)) {
			$data['nickname'] = $employee_info['nickname'];
		} else {
			$data['nickname'] =   '';
		}
		
		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} elseif (!empty($employee_info)) {
			$data['image'] = $employee_info['image'];
		} else {
			$data['image'] = '';
		}
		
		$this->load->model('tool/image');
		if(empty($data['image'])) {
			$data['thumb'] = $this->model_tool_image->resize('profile.png', 100, 100);
		} else { $data['thumb'] = $this->model_tool_image->resize($data['image'], 100, 100); }
		
		if (isset($this->request->post['skype'])) {
			$data['skype'] = $this->request->post['skype'];
		} elseif (!empty($employee_info)) {
			$data['skype'] = html_entity_decode($employee_info['skype'],ENT_HTML5);
		} else {
			$data['skype'] = '';
		}
		
		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} elseif (!empty($employee_info)) {
			$data['email'] = $employee_info['email'];
		} else {
			$data['email'] = '';
		}
		
		if (isset($this->request->post['order'])) {
			$data['order'] = $this->request->post['order'];
		} elseif (!empty($employee_info)) {
			$data['order'] = $employee_info['order'];
		} else {
			$data['order'] = '';
		}
		
		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($employee_info)) {
			$data['status'] = $employee_info['status'];
		} else {
			$data['status'] = true;
		}

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();
		
		$data['reference_employee_contact'] = $this->config->get('config_employee_contact');

		if (isset($this->request->post['employee_contact'])) {
			$data['employee_contact'] = $this->request->post['employee_contact'];
		} elseif (isset($this->request->get['employee_id'])) {
			$data['employee_contact'] = $this->model_localisation_employee->getEmployeeContact($this->request->get['employee_id']);
		} else {
			$data['employee_contact'] = array();
		}

		if (isset($this->request->post['employee_details'])) {
			$data['employee_details'] = $this->request->post['employee_details'];
		} elseif (isset($this->request->get['employee_id'])) {
			$data['employee_details'] = $this->model_localisation_employee->getEmployeeDetails($this->request->get['employee_id']);
		} else {
			$data['employee_details'] = array();
		}
		
$log = fopen(DIR_LOGS . 'admin_controller_localisation_employee__getForm.log', 'a');
fwrite($log,' $data )' . print_r($data, true) . '==' . chr(10) . chr(13));
fclose($log);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('localisation/employee_form', $data));
	}

	protected function validateForm() {
$log = fopen(DIR_LOGS . 'admin_controller_localisation_employee__validateForm.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,' $this->request->post )' . print_r($this->request->post, true) . '==' . chr(10) . chr(13));
fwrite($log,' $this->error )' . print_r($this->error, true) . '==' . chr(10) . chr(13));
fclose($log);
		unset($this->error['warning']);
		if (!$this->user->hasPermission('modify', 'localisation/employee')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!empty(utf8_strlen($this->request->post['nickname'])) && (utf8_strlen($this->request->post['nickname']) > 32)) {
			$this->error['nickname'] = sprintf($this->language->get('error_length'),'nickname',32);
		}

		if (!empty(utf8_strlen($this->request->post['skype'])) && (utf8_strlen($this->request->post['skype']) > 32)) {
			$this->error['skype'] = sprintf($this->language->get('error_length'),'skype',32);
		}

		if (!empty(utf8_strlen($this->request->post['email'])) && (utf8_strlen($this->request->post['email']) > 32)) {
			$this->error['email'] = sprintf($this->language->get('error_length'),'email',32);
		}

        if(!empty($this->request->post['employee_contact'])){
			$arr = array();
			$ununiqui = array();
			$this->error['contact_telephone'] = array();
			$this->error['contact_viber'] = array();
			$this->error['contact_telegram'] = array();
			$this->error['contact_whatsapp'] = array();
			foreach($this->request->post['employee_contact'] as $key => $value){
				if(!empty($value['telephone']))
					$arr[] = $value['telephone'];
				if (!empty(utf8_strlen($value['telephone'])) && (utf8_strlen($value['telephone']) > 20))
					$this->error['contact_telephone'][$key] = sprintf($this->language->get('error_length'),'telephone',20);
				if (!empty(utf8_strlen($value['viber'])) && (utf8_strlen($value['viber']) > 32))
					$this->error['contact_viber'][$key] = sprintf($this->language->get('error_length'),'viber',32);
				if (!empty(utf8_strlen($value['telegram'])) && (utf8_strlen($value['telegram']) > 32))
					$this->error['contact_telegram'][$key] = sprintf($this->language->get('error_length'),'telegram',32);
				if (!empty(utf8_strlen($value['whatsapp'])) && (utf8_strlen($value['whatsapp']) > 32))
					$this->error['contact_whatsapp'][$key] = sprintf($this->language->get('error_length'),'whatsapp',32);
			}
			sort($arr);
			for ($i = 1; $i < count($arr); $i++) 
				if ($arr[$i] == $arr[$i-1]) 
					$ununiqui[] = $arr[$i];
			if(!empty($ununiqui))
				$this->error['warning'] = sprintf($this->language->get('error_uniniqui'),implode(',',$ununiqui),'contacts');
				
		}
		if(empty($this->error['contact_telephone']))
			unset($this->error['contact_telephone']);
		if(empty($this->error['contact_viber']))
			unset($this->error['contact_viber']);
		if(empty($this->error['contact_telegram']))
			unset($this->error['contact_telegram']);
		if(empty($this->error['contact_whatsapp']))
			unset($this->error['contact_whatsapp']);

        if(!empty($this->request->post['employee_details'])){
			$this->error['details_name'] = array();
			$this->error['details_position'] = array();
			foreach($this->request->post['employee_details'] as $language_id => $value){
				if (!empty(utf8_strlen($value['name'])) && (utf8_strlen($value['name']) > 32))
					$this->error['details_name'][$language_id] = sprintf($this->language->get('error_length'),'details_name',32);
				if (!empty(utf8_strlen($value['position'])) && (utf8_strlen($value['position']) > 32))
					$this->error['details_position'][$language_id] = sprintf($this->language->get('error_length'),'details_position',32);
			}
		}
		if(empty($this->error['details_name']))
			unset($this->error['details_name']);
		if(empty($this->error['details_position']))
			unset($this->error['details_position']);
		
$log = fopen(DIR_LOGS . 'admin_controller_localisation_employee__validateForm.log', 'a');
fwrite($log,'RETURN $this->error )' . print_r($this->error, true) . '==' . chr(10) . chr(13));
fclose($log);
		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'localisation/employee')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
	
	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('localisation/department');

			$filter_data = array(
				'filter_path_or_name' => $this->request->get['filter_name'],
				'sort'        => 'path',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => 15
			);

			$results = $this->model_localisation_department->getDepartments($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'department_id' => $result['department_id'], 
					'name'        => strip_tags(html_entity_decode($result['path'] . '&nbsp;&nbsp;&gt;&nbsp;&nbsp;' . $result['name'], ENT_QUOTES, 'UTF-8'))
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
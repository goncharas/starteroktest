<?php
class ControllerLocalisationDepartment extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('localisation/department');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('localisation/department');

		$this->getList();
	}

	public function add() {
		$this->load->language('localisation/department');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('localisation/department');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_localisation_department->addDepartment($this->request->post);

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

			$this->response->redirect($this->url->link('localisation/department', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('localisation/department');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('localisation/department');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_localisation_department->editDepartment($this->request->get['department_id'], $this->request->post);

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

			$this->response->redirect($this->url->link('localisation/department', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
$log = fopen(DIR_LOGS . 'admin_controller_localisation_department__delete.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,' $this->request->post )' . print_r($this->request->post, true) . '==' . chr(10) . chr(13));
fclose($log);
		$this->load->language('localisation/department');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('localisation/department');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $department_id) {
				$this->model_localisation_department->deleteDepartment($department_id);
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

			$this->response->redirect($this->url->link('localisation/department', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
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
			'href' =>  $this->url->link('localisation/department', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('localisation/department/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('localisation/department/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['departments'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$department_total = $this->model_localisation_department->getTotalDepartments();

		$results = $this->model_localisation_department->getDepartments($filter_data);

		foreach ($results as $result) {
			$data['departments'][] =   array(
				'department_id' => $result['department_id'],
				'name'        => $result['name'],
				'path'        => $result['path'],
				'edit'        => $this->url->link('localisation/department/edit', 'user_token=' . $this->session->data['user_token'] . '&department_id=' . $result['department_id'] . $url, true)
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

		$data['sort_name'] = $this->url->link('localisation/department', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
		$data['sort_path'] = $this->url->link('localisation/department', 'user_token=' . $this->session->data['user_token'] . '&sort=path' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $department_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('localisation/department', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($department_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($department_total - $this->config->get('config_limit_admin'))) ? $department_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $department_total, ceil($department_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;
$log = fopen(DIR_LOGS . 'admin_controller_localisation_department__getList.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,' $data )' . print_r($data, true) . '==' . chr(10) . chr(13));
fclose($log);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('localisation/department_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['department_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['description_name'])) {
			$data['error_description_name'] = $this->error['description_name'];
		} else {
			$data['error_description_name'] = '';
		}

		if (isset($this->error['path'])) {
			$data['error_path'] = $this->error['path'];
		} else {
			$data['error_path'] = '';
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
			'href' => $this->url->link('localisation/department', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['department_id'])) {
			$data['action'] = $this->url->link('localisation/department/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('localisation/department/edit', 'user_token=' . $this->session->data['user_token'] .  '&department_id=' . $this->request->get['department_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('localisation/department', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['department_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$department_info = $this->model_localisation_department->getDepartment($this->request->get['department_id']);
$log = fopen(DIR_LOGS . 'admin_controller_localisation_department__getForm.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,' $department_info )' . print_r($department_info, true) . '==' . chr(10) . chr(13));
fclose($log);
		} else {
$log = fopen(DIR_LOGS . 'admin_controller_localisation_department__getForm.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,' $this->request->post )' . print_r($this->request->post, true) . '==' . chr(10) . chr(13));
fclose($log);
		}

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('setting/store');

		if (isset($this->request->post['path'])) {
			$data['path'] = $this->request->post['path'];
		} elseif (!empty($department_info)) {
			$data['path'] = htmlspecialchars(html_entity_decode ($department_info['path'],ENT_HTML5|ENT_QUOTES));
		} else {
			$data['path'] = '';
		}

		if (isset($this->request->post['parent_id'])) {
			$data['parent_id'] = $this->request->post['parent_id'];
		} elseif (!empty($department_info)) {
			$data['parent_id'] = $department_info['location_id'];
		} else {
			$data['parent_id'] = '';
		}
		
		if (isset($this->request->post['order'])) {
			$data['order'] = $this->request->post['order'];
		} elseif (!empty($department_info)) {
			$data['order'] = $department_info['order'];
		} else {
			$data['order'] = '';
		}

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['department_description'])) {
			$data['department_description'] = $this->request->post['department_description'];
		} elseif (isset($this->request->get['department_id'])) {
			$data['department_description'] = $this->model_localisation_department->getDepartmentDescriptions($this->request->get['department_id']);
		} else {
			$data['department_description'] = array();
		}
		
$log = fopen(DIR_LOGS . 'admin_controller_localisation_department__getForm.log', 'a');
// fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
// fwrite($log,' $filter_data )' . print_r($filter_data, true) . '==' . chr(10) . chr(13));
fwrite($log,' $data )' . print_r($data, true) . '==' . chr(10) . chr(13));
fclose($log);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('localisation/department_form', $data));
	}

	protected function validateForm() {
$log = fopen(DIR_LOGS . 'admin_controller_localisation_department__validateForm.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,' $this->request->post )' . print_r($this->request->post, true) . '==' . chr(10) . chr(13));
fwrite($log,' $this->error )' . print_r($this->error, true) . '==' . chr(10) . chr(13));
fclose($log);
		if (!$this->user->hasPermission('modify', 'localisation/department')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

        if(!empty($this->request->post['department_description'])){
			$this->error['description_name'] = array();
			foreach($this->request->post['department_description'] as $language_id => $value){
				if (!empty(utf8_strlen($value['name'])) && (utf8_strlen($value['name']) > 32))
					$this->error['description_name'][$language_id] = $this->language->get('error_name');
			}
		}
		if(empty($this->error['description_name']))
			unset($this->error['description_name']);

		// if ((utf8_strlen($this->request->post['address']) < 3) || (utf8_strlen($this->request->post['address']) > 128)) {
			// $this->error['address'] = $this->language->get('error_address');
		// }

		// if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 1332)) {
			// $this->error['telephone'] = $this->language->get('error_telephone');
		// }
$log = fopen(DIR_LOGS . 'admin_controller_localisation_department__validateForm.log', 'a');
fwrite($log,'RETURN $this->error )' . print_r($this->error, true) . '==' . chr(10) . chr(13));
fclose($log);
		return !$this->error;

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'localisation/department')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
	
	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('localisation/department');

			$filter_data = array(
				'filter_path' => $this->request->get['filter_name'],
				'sort'        => 'path',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_localisation_department->getDepartments($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'department_id' => $result['department_id'],
					'name'        => strip_tags(html_entity_decode($result['path'], ENT_QUOTES, 'UTF-8'))
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
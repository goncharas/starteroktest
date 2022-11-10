<?php
// *	@source		See SOURCE.txt for source and other copyright.
// *	@license	GNU General Public License version 3; see LICENSE.txt

class ControllerTecdocSupplier extends Controller {
	private $error = array();
    
    public function __construct($registry)
    {
        parent::__construct($registry);
        
        $this->connectToTecdocDb();        
    }

	public function index() {
		$this->load->language('tecdoc/supplier');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('tecdoc/supplier');
        
//        $this->connectToTecdocDb();

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['filter_description'])) {
			$filter_description = $this->request->get['filter_description'];
		} else {
			$filter_description = '';
		}
        if (isset($this->request->get['filter_description_changed'])) {
			$filter_description_changed = $this->request->get['filter_description_changed'];
		} else {
			$filter_description_changed = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'description';
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

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}
        if (isset($this->request->get['filter_changed'])) {
			$url .= '&filter_changed=' . urlencode($this->request->get['filter_changed']);
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
			'href' => $this->url->link('tecdoc/supplier', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['suppliers'] = array();

		$filter_data = array(
			'filter_description'	  => $filter_description,
			'filter_description_changed'  => $filter_description_changed,
			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'           => $this->config->get('config_limit_admin')
		);

		$total = $this->model_tecdoc_supplier->getTotal($filter_data);
		$results = $this->model_tecdoc_supplier->getList($filter_data);

		foreach ($results as $result) {
			$data['suppliers'][] = array(
				'supplier_id' => $result['id'],
				'description'  => $result['description'],
                'description_changed'  => $result['description_changed'],
				'edit'       => $this->url->link('tecdoc/supplier/edit', 'user_token=' . $this->session->data['user_token'] . '&supplier_id=' . $result['id'] . $url, true)
			);
		}

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
		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}
        if (isset($this->request->get['filter_changed'])) {
			$url .= '&filter_changed=' . urlencode($this->request->get['filter_changed']);
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_description'] = $this->url->link('tecdoc/supplier', 'user_token=' . $this->session->data['user_token'] . '&sort=description' . $url, true);
		$data['sort_description_changed'] = $this->url->link('tecdoc/supplier', 'user_token=' . $this->session->data['user_token'] . '&sort=description_changed' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}
        if (isset($this->request->get['filter_changed'])) {
			$url .= '&filter_changed=' . urlencode($this->request->get['filter_changed']);
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('tecdoc/supplier', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($total - $this->config->get('config_limit_admin'))) ? $total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $total, ceil($total / $this->config->get('config_limit_admin')));

		$data['filter_description'] = $filter_description;
        $data['filter_description_changed'] = $filter_description_changed;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('tecdoc/supplier_list', $data));
	}
    
    public function edit() {
		$this->load->language('tecdoc/supplier');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('tecdoc/supplier');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
//            var_dump($this->request->post); die;
			$this->model_tecdoc_supplier->editSupplier($this->request->get['supplier_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_description'])) {
				$url .= '&filter_description=' . urlencode(html_entity_decode($this->request->get['filter_description'], ENT_QUOTES, 'UTF-8'));
			}
            if (isset($this->request->get['filter_description_changed'])) {
				$url .= '&filter_description_changed=' . urlencode($this->request->get['filter_description_changed']);
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

			$this->response->redirect($this->url->link('tecdoc/supplier', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}
    
    protected function getForm() {
		$data['text_form'] = !isset($this->request->get['supplier_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['description'])) {
			$data['error_description'] = $this->error['description'];
		} else {
			$data['error_description'] = array();
		}

		$url = '';

		if (isset($this->request->get['filter_description'])) {
			$url .= '&filter_description=' . urlencode(html_entity_decode($this->request->get['filter_description'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_description_changed'])) {
			$url .= '&description_changed=' . $this->request->get['filter_description_changed'];
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
			'href' => $this->url->link('tecdoc/supplier', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['supplier_id'])) {
			$data['action'] = $this->url->link('tecdoc/supplier/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('tecdoc/supplier/edit', 'user_token=' . $this->session->data['user_token'] . '&supplier_id=' . $this->request->get['supplier_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('tecdoc/supplier', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['supplier_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$supplier_info = $this->model_tecdoc_supplier->getSupplier($this->request->get['supplier_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

//		$this->load->model('localisation/language');
//
//		$data['languages'] = $this->model_localisation_language->getLanguages();


		if (isset($this->request->post['description'])) {
			$data['description'] = $this->request->post['description'];
		} elseif (!empty($supplier_info)) {
			$data['description'] = $supplier_info['description'];
		} else {
			$data['description'] = '';
		}

		if (isset($this->request->post['description_changed'])) {
			$data['description_changed'] = $this->request->post['description_changed'];
		} elseif (!empty($supplier_info)) {
			$data['description_changed'] = $supplier_info['description_changed'];
		} else {
			$data['description_changed'] = '';
		}

		$this->load->model('design/layout');

		$data['layouts'] = $this->model_design_layout->getLayouts();
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('tecdoc/supplier_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'tecdoc/supplier')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

//		if ((utf8_strlen($this->request->post['description']) < 1) || (utf8_strlen($this->request->post['description']) > 45)) {
//			$this->error['model'] = $this->language->get('error_model');
//		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	public function autocomplete() {
//        $this->connectToTecdocDb();
        
		$json = array();

		if (isset($this->request->get['filter_descrirption'])) {
			$this->load->model('tecdoc/supplier');

			if (isset($this->request->get['filter_description'])) {
				$filter_description = $this->request->get['filter_description'];
			} else {
				$filter_description = '';
			}
            if (isset($this->request->get['filter_description_changed'])) {
				$filter_description_changed = $this->request->get['filter_description_changed'];
			} else {
				$filter_description_changed = '';
			}

			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$filter_data = array(
				'filter_description'  => $filter_description,
				'filter_description_changed'  => $filter_description_changed,
				'start'        => 0,
				'limit'        => $limit
			);

			$results = $this->model_tecdoc_supplier->getList($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'supplier_id' => $result['id'],
					'description' => strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')),
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
    
    private function connectToTecdocDb() {
        // модель tecdoc работает с объектом подключения tecdoc
        $tecdocDb = new DB(DB_DRIVER_TECDOC, DB_HOSTNAME_TECDOC, DB_USERNAME_TECDOC, DB_PASSWORD_TECDOC, DB_DATABASE_TECDOC);
        $this->registry->set('tecdoc', $tecdocDb);
    }
}

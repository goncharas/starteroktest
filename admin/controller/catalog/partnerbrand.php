<?php
class ControllerCatalogPartnerbrand extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('catalog/partnerbrand');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/partnerbrand');

		$this->getList();
	}

	public function add() {
		$this->load->language('catalog/partnerbrand');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/partnerbrand');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_partnerbrand->addpartnerbrand($this->request->post);

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

			$this->response->redirect($this->url->link('catalog/partnerbrand', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('catalog/partnerbrand');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/partnerbrand');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_partnerbrand->editpartnerbrand($this->request->get['partnerbrand_id'], $this->request->post);

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

			$this->response->redirect($this->url->link('catalog/partnerbrand', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('catalog/partnerbrand');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/partnerbrand');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $partnerbrand_id) {
				$this->model_catalog_partnerbrand->deletepartnerbrand($partnerbrand_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['partner_brand'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/partnerbrand', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	public function refresh() {
		$this->load->language('catalog/partnerbrand');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/partnerbrand');

		if ($this->validateRefresh()) {
			$this->model_catalog_partnerbrand->refresh(true);

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

			//$this->response->redirect($this->url->link('localisation/currency', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
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
			'href' => $this->url->link('catalog/partnerbrand', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('catalog/partnerbrand/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('catalog/partnerbrand/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['refresh'] = $this->url->link('catalog/partnerbrand/refresh', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['partnerbrands'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$partnerbrand_total = $this->model_catalog_partnerbrand->getTotalpartnerbrands();

		$results = $this->model_catalog_partnerbrand->getpartnerbrands($filter_data);

		foreach ($results as $result) {
			$data['partnerbrands'][] = array(
				'partnerbrand_id'   => $result['partnerbrand_id'],
				'partner_brand'     => $result['partner_brand'],
				'location'          => $result['location'],
				'manufacturer'      => $result['manufacturer'],
				'edit'              => $this->url->link('catalog/partnerbrand/edit', 'user_token=' . $this->session->data['user_token'] . '&partnerbrand_id=' . $result['partnerbrand_id'] . $url, true)
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

		$data['sort_location'] = $this->url->link('catalog/partnerbrand', 'user_token=' . $this->session->data['user_token'] . '&sort=location' . $url, true);
		$data['sort_manufacturer'] = $this->url->link('catalog/partnerbrand', 'user_token=' . $this->session->data['user_token'] . '&sort=manufacturer' . $url, true);
		$data['sort_partner_brand'] = $this->url->link('catalog/partnerbrand', 'user_token=' . $this->session->data['user_token'] . '&sort=partner_brand' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $partnerbrand_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/partnerbrand', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($partnerbrand_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($partnerbrand_total - $this->config->get('config_limit_admin'))) ? $partnerbrand_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $partnerbrand_total, ceil($partnerbrand_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/partnerbrand_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['partnerbrand_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['location'])) {
			$data['error_location'] = $this->error['location'];
		} else {
			$data['error_location'] = '';
		}

		if (isset($this->error['manufacturer'])) {
			$data['error_manufacturer'] = $this->error['manufacturer'];
		} else {
			$data['error_manufacturer'] = '';
		}

		if (isset($this->error['partner_brand'])) {
			$data['error_partner_brand'] = $this->error['partner_brand'];
		} else {
			$data['error_partner_brand'] = '';
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
			'href' => $this->url->link('catalog/partnerbrand', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['partnerbrand_id'])) {
			$data['action'] = $this->url->link('catalog/partnerbrand/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('catalog/partnerbrand/edit', 'user_token=' . $this->session->data['user_token'] . '&partnerbrand_id=' . $this->request->get['partnerbrand_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('catalog/partnerbrand', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['partnerbrand_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$partnerbrand_info = $this->model_catalog_partnerbrand->getpartnerbrand($this->request->get['partnerbrand_id']);
		}

		if (isset($this->request->post['location_id'])) {
			$data['location_id'] = $this->request->post['location_id'];
		} elseif (!empty($partnerbrand_info)) {
			$data['location_id'] = $partnerbrand_info['location_id'];
		} else {
			$data['location_id'] = '';
		}

		if (isset($this->request->post['manufacturer_id'])) {
			$data['manufacturer_id'] = $this->request->post['manufacturer_id'];
		} elseif (!empty($partnerbrand_info)) {
			$data['manufacturer_id'] = $partnerbrand_info['manufacturer_id'];
		} else {
			$data['manufacturer_id'] = '';
		}

		if (isset($this->request->post['partner_brand'])) {
			$data['partner_brand'] = $this->request->post['partner_brand'];
		} elseif (!empty($partnerbrand_info)) {
			$data['partner_brand'] = $partnerbrand_info['partner_brand'];
		} else {
			$data['partner_brand'] = '';
		}

		if (isset($this->request->post['partner_brand_clear'])) {
			$data['partner_brand_clear'] = $this->request->post['partner_brand_clear'];
		} elseif (!empty($partnerbrand_info)) {
			$data['partner_brand_clear'] = $partnerbrand_info['partner_brand_clear'];
		} else {
			$data['partner_brand_clear'] = '';
		}
    
 		$this->load->model('localisation/location');
    $filter_data = array('is_partner'=>'1');
    $data['locations'] = $this->model_localisation_location->getLocations($filter_data);
 		$this->load->model('catalog/manufacturer');
    $data['manufacturers'] = $this->model_catalog_manufacturer->getManufacturers();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/partnerbrand_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/partnerbrand')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['partner_brand']) < 3) || (utf8_strlen($this->request->post['partner_brand']) > 32)) {
			$this->error['partner_brand'] = $this->language->get('error_partner_brand');
		}

		if (empty($this->request->post['location_id'])) {
			$this->error['location'] = sprintf($this->language->get('error_empty'),$this->language->get('entry_location'));
		}

		if (empty($this->request->post['manufacturer_id'])) {
			$this->error['manufacturer'] = sprintf($this->language->get('error_empty'),$this->language->get('entry_manufacturer'));
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/partnerbrand')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateRefresh() {
		if (!$this->user->hasPermission('modify', 'catalog/partnerbrand')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
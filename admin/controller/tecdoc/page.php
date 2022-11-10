<?php
// *	@source		See SOURCE.txt for source and other copyright.
// *	@license	GNU General Public License version 3; see LICENSE.txt

class ControllerTecdocPage extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('tecdoc/page');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('tecdoc/page');
        
//        $this->connectToTecdocDb();

		$this->getList();
	}
    
    public function add() {
		$this->load->language('tecdoc/page');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('tecdoc/page');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_tecdoc_page->addPage($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_url'])) {
				$url .= '&filter_url=' . urlencode(html_entity_decode($this->request->get['filter_url'], ENT_QUOTES, 'UTF-8'));
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

			$this->response->redirect($this->url->link('tecdoc/page', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}
    
    public function edit() {
		$this->load->language('tecdoc/page');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('tecdoc/page');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_tecdoc_page->editPage($this->request->get['page_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_url'])) {
				$url .= '&filter_url=' . urlencode(html_entity_decode($this->request->get['filter_url'], ENT_QUOTES, 'UTF-8'));
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

			$this->response->redirect($this->url->link('tecdoc/page', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}
    
    public function delete() {
		$this->load->language('tecdoc/page');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('tecdoc/page');

		if (isset($this->request->post['selected'])) { // && $this->validateDelete()
			foreach ($this->request->post['selected'] as $page_id) {
				$this->model_tecdoc_page->deletePage($page_id);
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

//			$this->response->redirect($this->url->link('tecdoc/page', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
        $filter_url = (isset($this->request->get['filter_url']) ? $this->request->get['filter_url'] : '');
        
        $sort = (isset($this->request->get['sort']) ? $this->request->get['sort'] : 'url');
        $order = (isset($this->request->get['order']) ? $this->request->get['order'] : 'ASC');
        $page = (isset($this->request->get['page']) ? $this->request->get['page'] : 1);

        // собираем url для хлебных крошек и кнопок групповых операций
		$url = '';

		if (isset($this->request->get['filter_url'])) {
			$url .= '&filter_url=' . urlencode(html_entity_decode($this->request->get['filter_url'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

        // Хлебные крошки
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('tecdoc/page', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

        // ссылки для кнопок групповых операций Edit, Delete
		$data['add'] = $this->url->link('tecdoc/page/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('tecdoc/page/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

        // получаем данные - список сущностей и их общее кол-во
		$filter_data = array(
			'filter_url'	  => $filter_url,
			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'           => $this->config->get('config_limit_admin')
		);

		$total = $this->model_tecdoc_page->getTotalPages($filter_data);
		$results = $this->model_tecdoc_page->getPages($filter_data);
        
        $data['items'] = array();

		foreach ($results as $result) {
			$data['items'][] = array(
				'id'                => $result['id'],
				'datatype'          => $result['datatype'],
				'url'               => $result['url'],
				'meta_title'        => $result['meta_title'],
				'meta_description'  => (!empty($result['meta_description']) ? true : false),
                'description'       => (!empty($result['description']) ? true : false),
				'edit'              => $this->url->link('tecdoc/page/edit', 'user_token=' . $this->session->data['user_token'] . '&page_id=' . $result['id'] . $url, true)
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

        // собираем url для сортировки
		$url = '';

		if (isset($this->request->get['filter_url'])) {
			$url .= '&filter_url=' . urlencode(html_entity_decode($this->request->get['filter_url'], ENT_QUOTES, 'UTF-8'));
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_datatype'] = $this->url->link('tecdoc/page', 'user_token=' . $this->session->data['user_token'] . '&sort=datatype' . $url, true);
		$data['sort_url'] = $this->url->link('tecdoc/page', 'user_token=' . $this->session->data['user_token'] . '&sort=url' . $url, true);
		$data['sort_meta_title'] = $this->url->link('tecdoc/page', 'user_token=' . $this->session->data['user_token'] . '&sort=meta_title' . $url, true);

        // собираем паджинацию
		$url = '';

		if (isset($this->request->get['filter_url'])) {
			$url .= '&filter_url=' . urlencode(html_entity_decode($this->request->get['filter_url'], ENT_QUOTES, 'UTF-8'));
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
		$pagination->url = $this->url->link('tecdoc/page', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($total - $this->config->get('config_limit_admin'))) ? $total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $total, ceil($total / $this->config->get('config_limit_admin')));

		$data['filter_url'] = $filter_url;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('tecdoc/page_list', $data));
	}
    
    protected function getForm() {
		$data['text_form'] = !isset($this->request->get['page_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

        if (isset($this->error['page_url'])) {
			$data['error_page_url'] = $this->error['page_url'];
		} else {
			$data['error_page_url'] = '';
		}
		if (isset($this->error['meta_title'])) {
			$data['error_meta_title'] = $this->error['meta_title'];
		} else {
			$data['error_meta_title'] = array();
		}
        if (isset($this->error['meta_description'])) {
			$data['error_meta_description'] = $this->error['meta_description'];
		} else {
			$data['error_meta_description'] = array();
		}

		$url = '';

		if (isset($this->request->get['filter_url'])) {
			$url .= '&filter_url=' . urlencode(html_entity_decode($this->request->get['filter_url'], ENT_QUOTES, 'UTF-8'));
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
			'href' => $this->url->link('tecdoc/page', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['page_id'])) {
			$data['action'] = $this->url->link('tecdoc/page/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('tecdoc/page/edit', 'user_token=' . $this->session->data['user_token'] . '&page_id=' . $this->request->get['page_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('tecdoc/page', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['page_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$page_info = $this->model_tecdoc_page->getPage($this->request->get['page_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];
        
        // языковые данные
        $this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['page_description'])) {
			$data['page_description'] = $this->request->post['page_description'];
		} elseif (isset($this->request->get['page_id'])) {
			$data['page_description'] = $this->model_tecdoc_page->getPageLangData($this->request->get['page_id']);
		} else {
			$data['page_description'] = array();
		}

        // не языковые поля
		if (isset($this->request->post['datatype'])) {
			$data['datatype'] = $this->request->post['datatype'];
		} elseif (!empty($page_info)) {
			$data['datatype'] = $page_info['datatype'];
		} else {
			$data['datatype'] = '';
		}

		if (isset($this->request->post['url'])) {
			$data['url'] = $this->request->post['url'];
		} elseif (!empty($page_info)) {
			$data['url'] = $page_info['url'];
		} else {
			$data['url'] = '';
		}

		$this->load->model('design/layout');

		$data['layouts'] = $this->model_design_layout->getLayouts();
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('tecdoc/page_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'tecdoc/page')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['page_description'] as $language_id => $value) {
			if (utf8_strlen($value['meta_description']) > 254) {
				$this->error['meta_description'][$language_id] = $this->language->get('error_meta_description');
			}

			if (empty($value['meta_title']) || (utf8_strlen($value['meta_title']) > 254)) {
				$this->error['meta_title'][$language_id] = $this->language->get('error_meta_title');
			}
		}

		if ((utf8_strlen($this->request->post['url']) < 3) || (utf8_strlen($this->request->post['url']) > 254)) {
			$this->error['page_url'] = $this->language->get('error_page_url');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}
        
//echo '<pre>';
////var_dump($this->request->post);
//var_dump($this->error); die;

		return !$this->error;
	}

//	public function autocomplete() {
//        $this->connectToTecdocDb();
//        
//		$json = array();
//
//		if (isset($this->request->get['filter_url'])) {
//			$this->load->model('tecdoc/page');
//
//			if (isset($this->request->get['filter_url'])) {
//				$filter_url = $this->request->get['filter_url'];
//			} else {
//				$filter_url = '';
//			}
//
//			if (isset($this->request->get['limit'])) {
//				$limit = $this->request->get['limit'];
//			} else {
//				$limit = 5;
//			}
//
//			$filter_data = array(
//				'filter_name'  => $filter_url,
//				'start'        => 0,
//				'limit'        => $limit
//			);
//
//			$results = $this->model_tecdoc_page->getPages($filter_data);
//
//			foreach ($results as $result) {
//				$json[] = array(
//					'id' => $result['id'],
//					'name' => strip_tags(html_entity_decode($result['url'], ENT_QUOTES, 'UTF-8')),
//				);
//			}
//		}
//
//		$this->response->addHeader('Content-Type: application/json');
//		$this->response->setOutput(json_encode($json));
//	}
    
//    private function connectToTecdocDb() {
//        // модель tecdoc работает с объектом подключения tecdoc
//        $tecdocDb = new DB(DB_DRIVER_TECDOC, DB_HOSTNAME_TECDOC, DB_USERNAME_TECDOC, DB_PASSWORD_TECDOC, DB_DATABASE_TECDOC);
//        $this->registry->set('tecdoc', $tecdocDb);
//    }
}

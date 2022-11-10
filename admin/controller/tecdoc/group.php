<?php
// *	@source		See SOURCE.txt for source and other copyright.
// *	@license	GNU General Public License version 3; see LICENSE.txt

class ControllerTecdocGroup extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('tecdoc/group');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('tecdoc/group');
        
        $this->connectToTecdocDb();

		$this->getList();
	}

//    public function edit() {
//		$this->load->language('tecdoc/group');
//
//		$this->document->setTitle($this->language->get('heading_title'));
//
//		$this->load->model('tecdoc/index');
//
//		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
//			$this->model_tecdoc_index->editGroup($this->request->get['group_id'], $this->request->post);
//
//			$this->session->data['success'] = $this->language->get('text_success');
//
//			$url = '';
//
//			if (isset($this->request->get['filter_name'])) {
//				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
//			}
//            
//			if (isset($this->request->get['filter_status'])) {
//				$url .= '&filter_status=' . $this->request->get['filter_status'];
//			}
//			
//			if (isset($this->request->get['sort'])) {
//				$url .= '&sort=' . $this->request->get['sort'];
//			}
//
//			if (isset($this->request->get['order'])) {
//				$url .= '&order=' . $this->request->get['order'];
//			}
//
//			if (isset($this->request->get['page'])) {
//				$url .= '&page=' . $this->request->get['page'];
//			}
//
//			$this->response->redirect($this->url->link('tecdoc/group', 'user_token=' . $this->session->data['user_token'] . $url, true));
//		}
//
//		$this->getForm();
//	}

	protected function getList() {
		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = '';
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = '';
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
			'href' => $this->url->link('tecdoc/group', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['enabled'] = $this->url->link('tecdoc/group/enable', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['disabled'] = $this->url->link('tecdoc/group/disable', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['groups'] = array();

		$filter_data = array(
			'filter_name'	  => $filter_name,
			'filter_status'	  => $filter_status,
			'sort'            		=> $sort,
			'order'           		=> $order,
			'start'           		=> ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'           		=> $this->config->get('config_limit_admin')
		);

		$total = $this->model_tecdoc_group->getTotalGroups($filter_data);
		$results = $this->model_tecdoc_group->getGroups($filter_data);

		foreach ($results as $result) {
			$data['groups'][] = array(
				'id' => $result['id'],
				'assemblygroupdescription' => $result['assemblygroupdescription'],
				'description'  => $result['description'],
                'status'  => $result['status'],
//				'edit'       => $this->url->link('catalog/product/edit', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $result['product_id'] . $url, true)
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

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

//		$data['sort_default'] = $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . '&sort=default' . $url, true);
		$data['sort_assemblygroupdescription'] = $this->url->link('tecdoc/group', 'user_token=' . $this->session->data['user_token'] . '&sort=assemblygroupdescription' . $url, true);
		$data['sort_description'] = $this->url->link('tecdoc/group', 'user_token=' . $this->session->data['user_token'] . '&sort=description' . $url, true);
		$data['sort_status'] = $this->url->link('tecdoc/group', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
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
		$pagination->url = $this->url->link('tecdoc/group', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($total - $this->config->get('config_limit_admin'))) ? $total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $total, ceil($total / $this->config->get('config_limit_admin')));

		$data['filter_name'] = $filter_name;
        $data['filter_status'] = $filter_status;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('tecdoc/group_list', $data));
	}

	public function enable() {
        $this->connectToTecdocDb();
        
        $this->load->language('tecdoc/group');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('tecdoc/group');
        
        if (isset($this->request->post['selected']) && $this->validateEnable()) {
            foreach ($this->request->post['selected'] as $group_id) {
                $this->model_tecdoc_group->editStatus($group_id, 1);
            }
            $this->session->data['success'] = $this->language->get('text_success');
            $url = '';
            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }
            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }
            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }
            if (isset($this->request->get['filter_name'])) {
                $url .= '&filter_name=' . $this->request->get['filter_name'];
            }
            if (isset($this->request->get['filter_status'])) {
                $url .= '&filter_status=' . $this->request->get['filter_status'];
            }
            $this->response->redirect($this->url->link('tecdoc/group', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }
        $this->getList();
    }
    public function disable() {
        $this->connectToTecdocDb();
        
        $this->load->language('tecdoc/group');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('tecdoc/group');
        
        if (isset($this->request->post['selected']) && $this->validateDisable()) {
            foreach ($this->request->post['selected'] as $group_id) {
                $this->model_tecdoc_group->editStatus($group_id, 0);
            }
            $this->session->data['success'] = $this->language->get('text_success');
            $url = '';
            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }
            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }
            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }
            if (isset($this->request->get['filter_name'])) {
                $url .= '&filter_name=' . $this->request->get['filter_name'];
            }
            if (isset($this->request->get['filter_status'])) {
                $url .= '&filter_status=' . $this->request->get['filter_status'];
            }
            $this->response->redirect($this->url->link('tecdoc/group', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }
        $this->getList();
    }
	
	protected function validateEnable() {
		if (!$this->user->hasPermission('modify', 'tecdoc/group')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
	
	protected function validateDisable() {
		if (!$this->user->hasPermission('modify', 'tecdoc/group')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function autocomplete() {
        $this->connectToTecdocDb();
        
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('tecdoc/group');

			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$filter_data = array(
				'filter_name'  => $filter_name,
				'start'        => 0,
				'limit'        => $limit
			);

			$results = $this->model_tecdoc_group->getGroups($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'id' => $result['id'],
					'name' => strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')),
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

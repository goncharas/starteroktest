<?php
class ControllerExtensionModuleJetimpexParallax extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/jetimpex_parallax');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/module');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (!isset($this->request->get['module_id'])) {
				$this->model_setting_module->addModule('jetimpex_parallax', $this->request->post);
			} else {
				$this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

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
		
		if (isset($this->error['width'])) {
			$data['error_width'] = $this->error['width'];
		} else {
			$data['error_width'] = '';
		}
		
		if (isset($this->error['height'])) {
			$data['error_height'] = $this->error['height'];
		} else {
			$data['error_height'] = '';
		}

		if (isset($this->error['speed'])) {
			$data['error_speed'] = $this->error['speed'];
		} else {
			$data['error_speed'] = '';
		}

		if (isset($this->error['layer_speed'])) {
			$data['error_layer_speed'] = $this->error['layer_speed'];
		} else {
			$data['error_layer_speed'] = '';
		}

		if (isset($this->error['layer_image_width'])) {
			$data['error_layer_image_width'] = $this->error['layer_image_width'];
		} else {
			$data['error_layer_image_width'] = '';
		}

		if (isset($this->error['layer_image_height'])) {
			$data['error_layer_image_height'] = $this->error['layer_image_height'];
		} else {
			$data['error_layer_image_height'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
			);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
			);

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/jetimpex_parallax', 'user_token=' . $this->session->data['user_token'], true)
				);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/jetimpex_parallax', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
				);
		}
		
		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('extension/module/jetimpex_parallax', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['action'] = $this->url->link('extension/module/jetimpex_parallax', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
		}
		
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
		
		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
		}

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($module_info)) {
			$data['name'] = $module_info['name'];
		} else {
			$data['name'] = '';
		}

		$this->load->model('localisation/language');
		$this->load->model('tool/image');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} elseif (!empty($module_info)) {
			$data['image'] = $module_info['image'];
		} else {
			$data['image'] = '';
		}

		if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
			$data['image_thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
		} elseif (!empty($module_info) && isset($module_info['image']) && is_file(DIR_IMAGE . $module_info['image'])) {
			$data['image_thumb'] = $this->model_tool_image->resize($module_info['image'], 100, 100);
		} else {
			$data['image_thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		if (isset($this->request->post['width'])) {
			$data['width'] = $this->request->post['width'];
		} elseif (!empty($module_info)) {
			$data['width'] = $module_info['width'];
		} else {
			$data['width'] = '';
		}

		if (isset($this->request->post['height'])) {
			$data['height'] = $this->request->post['height'];
		} elseif (!empty($module_info)) {
			$data['height'] = $module_info['height'];
		} else {
			$data['height'] = '';
		}
		
		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($module_info)) {
			$data['status'] = $module_info['status'];
		} else {
			$data['status'] = '';
		}

		if (isset($this->request->post['blur'])) {
			$data['blur'] = $this->request->post['blur'];
		} elseif (!empty($module_info)) {
			$data['blur'] = $module_info['blur'];
		} else {
			$data['blur'] = '';
		}

		if (isset($this->request->post['direction'])) {
			$data['direction'] = $this->request->post['direction'];
		} elseif (!empty($module_info)) {
			$data['direction'] = $module_info['direction'];
		} else {
			$data['direction'] = '';
		}

		if (isset($this->request->post['speed'])) {
			$data['speed'] = $this->request->post['speed'];
		} elseif (!empty($module_info)) {
			$data['speed'] = $module_info['speed'];
		} else {
			$data['speed'] = '0.2';
		}

		if (isset($this->request->post['module_id'])) {
			$data['module_id'] = $this->request->post['module_id'];
		} elseif (!empty($module_info['module_id'])) {
			$data['module_id'] = $module_info['module_id'];
		} else {
			$data['module_id'] = '';
		}

		if (isset($this->request->post['layers'])) {
			$data['layers'] = $this->request->post['layers'];
		} elseif (!empty($module_info['layers'])) {
			$data['layers'] = $module_info['layers'];
		} else {
			$data['layers'] = '';
		}

		if (!empty($module_info) && isset($module_info['layers'])) {
			foreach ($module_info['layers'] as $layer) {
				if (is_file(DIR_IMAGE . $layer['image'])) {
					$layer_thumb = $this->model_tool_image->resize($layer['image'], 100, 100);
				} else {
					$layer_thumb = $this->model_tool_image->resize('no_image.png', 100, 100);
				}
				$data['layer_thumb'][] = $layer_thumb;
			}
		}

		$data['modules']  = $this->model_setting_module->getModules();
		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/jetimpex_parallax', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/jetimpex_parallax')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if (!$this->request->post['width']) {
			$this->error['width'] = $this->language->get('error_width');
		}
		
		if (!$this->request->post['height']) {
			$this->error['height'] = $this->language->get('error_height');
		}

		if (!is_numeric($this->request->post['speed']) || ($this->request->post['speed'] < 0 || $this->request->post['speed'] > 2)) {
			$this->error['speed'] = $this->language->get('error_speed');
		}

		if (isset($this->request->post['layers'])) {
			$i=0;
			foreach ($this->request->post['layers'] as $layer) {
				if (!is_numeric($layer['speed']) || ($layer['speed'] < 0 || $layer['speed'] > 2)) {
					$this->error['layer_speed'][$i] = $this->language->get('error_layer_speed');
				}
				if ($layer['image']) {
					if (!is_numeric($layer['width']) || $layer['width'] <= 0) {
						$this->error['layer_image_width'][$i] = $this->language->get('error_layer_image_width');
					}
					if (!is_numeric($layer['height']) || $layer['height'] <= 0) {
						$this->error['layer_image_height'][$i] = $this->language->get('error_layer_image_height');
					}
				}
				$i++;
			}
		}

		return !$this->error;
	}
}
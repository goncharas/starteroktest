<?php
class ControllerExtensionModuleJetimpexCategory extends Controller {
	public function index() {
		$this->load->language('extension/module/jetimpex_category');

		$data['heading_title'] = $this->language->get('heading_title');

		if (isset($this->request->get['path'])) {
			$parts = explode('_', (string)$this->request->get['path']);
		} else {
			$parts = array();
		}

		if (isset($parts[0])) {
			$data['category_id'] = $parts[0];
		} else {
			$data['category_id'] = 0;
		}

		if (isset($parts[1])) {
			$data['child_id'] = $parts[1];
		} else {
			$data['child_id'] = 0;
		}

		$this->load->model('catalog/category');
		$this->load->model('catalog/product');
		$this->load->model('tool/image');

		$data['categories'] = array();

		$categories = $this->model_catalog_category->getCategories(0);

		foreach ($categories as $category) {
			// Level 2
			$children_data = array();
				//$subchildren_data = array();
			$children = $this->model_catalog_category->getCategories($category['category_id']);

			foreach ($children as $child) {
				$filter_data = array(
					'filter_category_id'  => $child['category_id'],
					'filter_sub_category' => true
                );

				// Level 3
				$subchildren_data = array();
				$subchildren = $this->model_catalog_category->getCategories($child['category_id']);

				foreach ($subchildren as $subchild) {
					$filter_subdata = array(
						'filter_category_id'  => $subchild['category_id'],
						'filter_sub_category' => true
                    );
					$subchildren_data[] = array(
						'category_id' => $subchild['category_id'],
						'name'  => $subchild['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_subdata) . ')' : ''),
						'href'  => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'] . '_' . $subchild['category_id']),
                        'thumb' => $this->getImage($subchild['image']),
                    );
				}
                
				$children_data[] = array(
					'name'  => $child['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
					'href'  => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id']),
					'thumb' => $this->getImage($child['image']),
					'subchildren' => $subchildren_data
                );
			}

			// Level 1
			$data['categories'][] = array(
				'name'     => $category['name'],
				'children' => $children_data,
				'column'   => $category['column'] ? $category['column'] : 1,
                'thumb' => $this->getImage($category['image']),
				'href'     => $this->url->link('product/category', 'path=' . $category['category_id'])
            );
		}

		return $this->load->view('extension/module/jetimpex_category', $data);
	}
    
    protected function getImage($imageName) {
        if (!empty($imageName)) {
            $image = $this->model_tool_image->resize($imageName, 32, 32);//$this->config->get('config_image_product_width'), $this->config->get('config_image_product_height')
        } else {
            $image = $this->model_tool_image->resize('placeholder.png', 32, 32); //$this->config->get('config_image_product_width'), $this->config->get('config_image_product_height')
        }
        return $image;
    }
}


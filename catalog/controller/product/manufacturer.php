<?php
// *	@source		See SOURCE.txt for source and other copyright.
// *	@license	GNU General Public License version 3; see LICENSE.txt

class ControllerProductManufacturer extends Controller {
	public function index() {
if(catalog_controller_product_manufacturer__index){
$log = fopen(DIR_LOGS . 'catalog_controller_product_manufacturer__index.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,' $this->request->get )' . print_r($this->request->get, true) . '==' . chr(10) . chr(13));
fwrite($log,' $this->session->data )' . print_r($this->session->data, true) . '==' . chr(10) . chr(13));
fclose($log);}
		$this->load->language('product/manufacturer');

		$this->load->model('catalog/manufacturer');

		$this->load->model('tool/image');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_brand'),
			'href' => $this->url->link('product/manufacturer')
		);

		$data['categories'] = array();

		$results = $this->model_catalog_manufacturer->getManufacturers();
if(catalog_controller_product_manufacturer__index){
$log = fopen(DIR_LOGS . 'catalog_controller_product_manufacturer__index.log', 'a');
fwrite($log,' $results = $this->model_catalog_manufacturer->getManufacturers(); ' . '==' . chr(10) . chr(13));
fwrite($log,' $results )' . print_r($results, true) . '==' . chr(10) . chr(13));
fclose($log);}

		foreach ($results as $result) {
			if (is_numeric(utf8_substr($result['name'], 0, 1))) {
				$key = '0 - 9';
			} else {
				$key = utf8_substr(utf8_strtoupper($result['name']), 0, 1);
			}
if(catalog_controller_product_manufacturer__index){
$log = fopen(DIR_LOGS . 'catalog_controller_product_manufacturer__index.log', 'a');
fwrite($log,' ************************ foreach ($results as $result) {  ' . '==' . chr(10) . chr(13));
fwrite($log,' $result ) ' . print_r($result, true) . '==' . chr(10) . chr(13));
fwrite($log,' if (is_numeric(utf8_substr($result[name], 0, 1)))  ' . '==' . chr(10) . chr(13));
fwrite($log,' $key )' . print_r($key, true) . '==' . chr(10) . chr(13));
fclose($log);}

			if (!isset($data['categories'][$key])) {
				$data['categories'][$key]['name'] = $key;
if(catalog_controller_product_manufacturer__index){
$log = fopen(DIR_LOGS . 'catalog_controller_product_manufacturer__index.log', 'a');
fwrite($log,' ************************ foreach ($results as $result) {  ' . '==' . chr(10) . chr(13));
fwrite($log,' if (!isset($data[categories][$key])) {  ' . '==' . chr(10) . chr(13));
fwrite($log,' $data[categories][$key][name] = $key;  )' . print_r($key, true) . '==' . chr(10) . chr(13));
fclose($log);}
			}

			$data['categories'][$key]['manufacturer'][] = array(
				'name' => $result['name'],
				'href' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $result['manufacturer_id'])
			);
if(catalog_controller_product_manufacturer__index){
$log = fopen(DIR_LOGS . 'catalog_controller_product_manufacturer__index.log', 'a');
fwrite($log,' ************************ foreach ($results as $result) {  ' . '==' . chr(10) . chr(13));
fwrite($log,' $data[categories]  )' . print_r($data['categories'], true) . '==' . chr(10) . chr(13));
fclose($log);}
		}

		$data['continue'] = $this->url->link('common/home');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

if(catalog_controller_product_manufacturer__index){
$log = fopen(DIR_LOGS . 'catalog_controller_product_manufacturer__index.log', 'a');
fwrite($log,' ************************  $this->response->setOutput($this->load->view(product/manufacturer_list, $data)); ' . '==' . chr(10) . chr(13));
fwrite($log,' $data  )' . print_r($data, true) . '==' . chr(10) . chr(13));
fclose($log);}
		$this->response->setOutput($this->load->view('product/manufacturer_list', $data));
	}

	public function info() {
if(catalog_controller_product_manufacturer__info){
$log = fopen(DIR_LOGS . 'catalog_controller_product_manufacturer__info.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,' $this->request->get )' . print_r($this->request->get, true) . '==' . chr(10) . chr(13));
fwrite($log,' $this->session->data )' . print_r($this->session->data, true) . '==' . chr(10) . chr(13));
fclose($log);}
		$this->load->language('product/manufacturer');

		$this->load->model('catalog/manufacturer');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		if (isset($this->request->get['manufacturer_id'])) {
			$manufacturer_id = (int)$this->request->get['manufacturer_id'];
		} else {
			$manufacturer_id = 0;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
			$this->document->setRobots('noindex,follow');
		} else {
			$sort = 'p.sort_order';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
			$this->document->setRobots('noindex,follow');
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
			$this->document->setRobots('noindex,follow');
		} else {
			$page = 1;
		}

		if (isset($this->request->get['limit'])) {
			$limit = (int)$this->request->get['limit'];
			$this->document->setRobots('noindex,follow');
		} else {
			$limit = (int)$this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit');
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_brand'),
			'href' => $this->url->link('product/manufacturer')
		);

		$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($manufacturer_id);
if(catalog_controller_product_manufacturer__info){
$log = fopen(DIR_LOGS . 'catalog_controller_product_manufacturer__info.log', 'a');
fwrite($log,' $manufacturer_info = $this->model_catalog_manufacturer->getManufacturer('.$manufacturer_id.'); ' . '==' . chr(10) . chr(13));
fwrite($log,' $manufacturer_info )' . print_r($manufacturer_info, true) . '==' . chr(10) . chr(13));
fclose($log);}

		if ($manufacturer_info) {
			
			if ($manufacturer_info['meta_title']) {
				$this->document->setTitle($manufacturer_info['meta_title']);
			} else {
				$this->document->setTitle($manufacturer_info['name']);
			}
			
			if ($manufacturer_info['noindex'] <= 0) {
				$this->document->setRobots('noindex,follow');
			}
			
			if ($manufacturer_info['meta_h1']) {
				$data['heading_title'] = $manufacturer_info['meta_h1'];
			} else {
				$data['heading_title'] = $manufacturer_info['name'];
			}
			
			$this->document->setDescription($manufacturer_info['meta_description']);
			$this->document->setKeywords($manufacturer_info['meta_keyword']);
			$data['description'] = html_entity_decode($manufacturer_info['description'], ENT_QUOTES, 'UTF-8');
if(catalog_controller_product_manufacturer__info){
$log = fopen(DIR_LOGS . 'catalog_controller_product_manufacturer__info.log', 'a');
fwrite($log,' $data )' . print_r($data, true) . '==' . chr(10) . chr(13));
fclose($log);}
			
			
			if ($manufacturer_info['image']) {
if(catalog_controller_product_manufacturer__info){
$log = fopen(DIR_LOGS . 'catalog_controller_product_manufacturer__info.log', 'a');
fwrite($log,' if ($manufacturer_info[image]) {'.$manufacturer_info['image'].'); ' . '==' . chr(10) . chr(13));
fwrite($log,' 1) )' . print_r(('theme_' . $this->config->get('config_theme') . '_image_manufacturer_width'), true) . '==' . chr(10) . chr(13));
fwrite($log,' 2) )' . print_r(('theme_' . $this->config->get('config_theme') . '_image_manufacturer_height'), true) . '==' . chr(10) . chr(13));
fclose($log);}
				$data['thumb'] = $this->model_tool_image->resize($manufacturer_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_manufacturer_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_manufacturer_height'));
			} else {
				$data['thumb'] = '';
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

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}
if(catalog_controller_product_manufacturer__info){
$log = fopen(DIR_LOGS . 'catalog_controller_product_manufacturer__info.log', 'a');
fwrite($log,' $url )' . print_r($url, true) . '==' . chr(10) . chr(13));
fclose($log);}

			$data['breadcrumbs'][] = array(
				'text' => $manufacturer_info['name'],
				'href' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . $url)
			);

			$data['text_compare'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));

			$data['compare'] = $this->url->link('product/compare');

			$data['products'] = array();

			$filter_data = array(
				'filter_manufacturer_id' => $manufacturer_id,
				'sort'                   => $sort,
				'order'                  => $order,
				'start'                  => ($page - 1) * $limit,
				'limit'                  => $limit
			);
if(catalog_controller_product_manufacturer__info){
$log = fopen(DIR_LOGS . 'catalog_controller_product_manufacturer__info.log', 'a');
fwrite($log,' $filter_data )' . print_r($filter_data, true) . '==' . chr(10) . chr(13));
fclose($log);}

			$product_total = $this->model_catalog_product->getTotalProducts($filter_data);
if(catalog_controller_product_manufacturer__info){
$log = fopen(DIR_LOGS . 'catalog_controller_product_manufacturer__info.log', 'a');
fwrite($log,' $product_total = $this->model_catalog_product->getTotalProducts($filter_data); )' . print_r($product_total, true) . '==' . chr(10) . chr(13));
fclose($log);}

			$results = $this->model_catalog_product->getProducts($filter_data);
if(catalog_controller_product_manufacturer__info){
$log = fopen(DIR_LOGS . 'catalog_controller_product_manufacturer__info.log', 'a');
fwrite($log,' $results = $this->model_catalog_product->getProducts($filter_data); )' . print_r($results, true) . '==' . chr(10) . chr(13));
fclose($log);}

			foreach ($results as $result) {
				if ($result['image']) {
if(catalog_controller_product_manufacturer__info){
$log = fopen(DIR_LOGS . 'catalog_controller_product_manufacturer__info.log', 'a');
fwrite($log,' if ($result[image]) {'.$result['image'].'); ' . '==' . chr(10) . chr(13));
fwrite($log,' 1) )' . print_r('theme_' . $this->config->get('config_theme') . '_image_product_width', true) . '==' . chr(10) . chr(13));
fwrite($log,' 2) )' . print_r('theme_' . $this->config->get('config_theme') . '_image_product_height', true) . '==' . chr(10) . chr(13));
fclose($log);}
					$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if ((float)$result['special']) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$special = false;
				}

				if ($result['quantity'] <= 0  || empty($result['quantity']) ) {
				    $stock = $result['stock_status'];
			    } elseif ($this->config->get('config_stock_display')) {
				    $stock = $result['quantity'];
			    } else {
				    $stock = $this->language->get('text_instock');
			    }

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = (int)$result['rating'];
				} else {
					$rating = false;
				}

				$data['products'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
					'stock'       => $stock,
					'tax'         => $tax,
					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'rating'      => $result['rating'],
					'href'        => $this->url->link('product/product', 'manufacturer_id=' . $result['manufacturer_id'] . '&product_id=' . $result['product_id'] . $url)
				);
			}
if(catalog_controller_product_manufacturer__info){
$log = fopen(DIR_LOGS . 'catalog_controller_product_manufacturer__info.log', 'a');
fwrite($log,' $data[products] )' . print_r($data['products'], true) . '==' . chr(10) . chr(13));
fclose($log);}

			$url = '';

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['sorts'] = array();

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_default'),
				'value' => 'p.sort_order-ASC',
				'href'  => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . '&sort=p.sort_order&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_name_asc'),
				'value' => 'pd.name-ASC',
				'href'  => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . '&sort=pd.name&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_name_desc'),
				'value' => 'pd.name-DESC',
				'href'  => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . '&sort=pd.name&order=DESC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_price_asc'),
				'value' => 'p.price-ASC',
				'href'  => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . '&sort=p.price&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_price_desc'),
				'value' => 'p.price-DESC',
				'href'  => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . '&sort=p.price&order=DESC' . $url)
			);

			if ($this->config->get('config_review_status')) {
				$data['sorts'][] = array(
					'text'  => $this->language->get('text_rating_desc'),
					'value' => 'rating-DESC',
					'href'  => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . '&sort=rating&order=DESC' . $url)
				);

				$data['sorts'][] = array(
					'text'  => $this->language->get('text_rating_asc'),
					'value' => 'rating-ASC',
					'href'  => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . '&sort=rating&order=ASC' . $url)
				);
			}

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_model_asc'),
				'value' => 'p.model-ASC',
				'href'  => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . '&sort=p.model&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_model_desc'),
				'value' => 'p.model-DESC',
				'href'  => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . '&sort=p.model&order=DESC' . $url)
			);

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			$data['limits'] = array();

			$limits = array_unique(array($this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit'), 25, 50, 75, 100));

			sort($limits);

			foreach($limits as $value) {
				$data['limits'][] = array(
					'text'  => $value,
					'value' => $value,
					'href'  => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . $url . '&limit=' . $value)
				);
			}

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}
if(catalog_controller_product_manufacturer__info){
$log = fopen(DIR_LOGS . 'catalog_controller_product_manufacturer__info.log', 'a');
fwrite($log,' $data )' . print_r($data['products'], true) . '==' . chr(10) . chr(13));
fclose($log);}

			$pagination = new Pagination();
			$pagination->total = $product_total;
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->url = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id'] .  $url . '&page={page}');

			$data['pagination'] = $pagination->render();

			$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($product_total - $limit)) ? $product_total : ((($page - 1) * $limit) + $limit), $product_total, ceil($product_total / $limit));

			// http://googlewebmastercentral.blogspot.com/2011/09/pagination-with-relnext-and-relprev.html
			// if ($page == 1) {
			    // $this->document->addLink($this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id'], true), 'canonical');
			// } else {
				// $this->document->addLink($this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . $url . '&page='. $page, true), 'canonical');
			// }
			$this->document->addLink($this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id']), 'canonical');

			if ($page > 1) {
			    $this->document->addLink($this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . $url . (($page - 2) ? '&page='. ($page - 1) : '')), 'prev');
			}

			if ($limit && ceil($product_total / $limit) > $page) {
			    $this->document->addLink($this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . $url . '&page='. ($page + 1)), 'next');
			}

            
// reviews
      $this->load->model('catalog/review');
      $reviews = $this->model_catalog_review->getLastReviewsByManufacturer($this->request->get['manufacturer_id'], 'r.rating>=3');
            
      foreach($reviews as &$review) {
        if (!empty($review['image'])) {
  				$review['image'] = $this->model_tool_image->resize($review['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
        } else {
					$review['image'] = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
				}
                
        $review['href'] = $this->url->link('product/product', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . $url . '&product_id=' . $review['product_id']);
                //$review['date_added'] = date('j M Y', strtotime($review['date_added']));
                
                //$formatter = new IntlDateFormatter('ru_RU', IntlDateFormatter::FULL, IntlDateFormatter::FULL);
                //$formatter->setPattern('d MMM YYYY');
                //$review['date_added'] = $formatter->format(new DateTime($review['date_added']));
        $review['date_added'] = date('Y-m-d', strtotime($review['date_added']));
      }
			$data['reviews'] = $reviews;            
      $this->registry->set('ete_reviews', $reviews);
if(catalog_controller_product_manufacturer__info){
$log = fopen(DIR_LOGS . 'catalog_controller_product_manufacturer__info.log', 'a');
fwrite($log,' $data[reviews] )' . print_r($data['reviews'], true) . '==' . chr(10) . chr(13));
fclose($log);}            


			$data['sort'] = $sort;
			$data['order'] = $order;
			$data['limit'] = $limit;

			$data['continue'] = $this->url->link('common/home');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('product/manufacturer_info', $data));
		} else {
			$url = '';

			if (isset($this->request->get['manufacturer_id'])) {
				$url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
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

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('product/manufacturer/info', $url)
			);

			$this->document->setTitle($this->language->get('text_error'));

			$data['heading_title'] = $this->language->get('text_error');

			$data['text_error'] = $this->language->get('text_error');

			$data['continue'] = $this->url->link('common/home');

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

			$data['header'] = $this->load->controller('common/header');
			$data['footer'] = $this->load->controller('common/footer');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');

			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}
}

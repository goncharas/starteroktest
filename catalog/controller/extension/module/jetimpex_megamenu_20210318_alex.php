<?php

class ControllerExtensionModuleJetimpexMegaMenu extends Controller
{

	public function index($setting)
	{
$log = fopen(DIR_LOGS . 'catalog_controler_extension_module_jetimpex_megamenu__index.log', 'w');
fwrite($log,'**********************************' . chr(13) . chr(13));
$t = microtime(true);
$micro = sprintf("%06d",($t - floor($t)) * 1000000);
$d = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );
fwrite($log, $d->format("Y-m-d H:i:s.u") . '==' . chr(10) . chr(13));
fwrite($log,'$setting) ' . print_r($setting,true) . chr(13) . chr(13));
fclose($log);
		// $this->document->addScript('catalog/view/theme/' . $this->config->get('theme_' . $this->config->get('config_theme') . '_directory') . '/js/jetimpex_megamenu/superfish.min.js');
		// $this->document->addScript('catalog/view/theme/' . $this->config->get('theme_' . $this->config->get('config_theme') . '_directory') . '/js/jetimpex_megamenu/jquery.rd-navbar.min.js');

		$this->load->model('catalog/category');
		$this->load->model('catalog/product');
		$this->load->model('tool/image');
		$this->load->model('extension/module/jetimpex_megamenu');

		$categories = $this->model_catalog_category->getCategories(0);
$log = fopen(DIR_LOGS . 'catalog_controler_extension_module_jetimpex_megamenu__index.log', 'a');
fwrite($log,'++++++++++++++++++++++++++++++++++' . chr(13) . chr(13));
fwrite($log,'$categories) ' . print_r($categories,true) . chr(13) . chr(13));
fclose($log);
		foreach ($categories as $categorie_id => $categorie) {
			if (!$categorie['top']) {
				unset($categories[$categorie_id]);
			}
		}

		// $categories = array_values($categories);
$log = fopen(DIR_LOGS . 'catalog_controler_extension_module_jetimpex_megamenu__index.log', 'a');
fwrite($log,'++++++++++++++++++++++++++++++++++' . chr(13) . chr(13));
fwrite($log,'$categories array_values) ' . print_r($categories,true) . chr(13) . chr(13));
fclose($log);

		$data['menu_items'] = [];
		if (isset($setting['menu_item'])) {
$log = fopen(DIR_LOGS . 'catalog_controler_extension_module_jetimpex_megamenu__index.log', 'a');
fwrite($log,'++++++++++++++++++++++++++++++++++' . chr(13) . chr(13));
fwrite($log,'if (isset($setting[menu_item])) ' . chr(13) . chr(13));
fclose($log);
			$top_category_count = 0;

			foreach ($setting['menu_item'] as $menu_item_id => $menu_item) {
				$columns          = [];
				$products_count   = [];
				$categories_count = [];
				if ($menu_item['type']) {

					$name       = $menu_item[$this->config->get('config_language_id')]['title'];
					$href       = $menu_item['link'];
					$show_mobile = (isset($menu_item['show_mobile']) ? 'show_mobile' : '');
					$show_computer = (isset($menu_item['show_computer']) ? 'show_desktop' : '');
					$multilevel = '';
$log = fopen(DIR_LOGS . 'catalog_controler_extension_module_jetimpex_megamenu__index.log', 'a');
fwrite($log,'++++++++++++++++++++++++++++++++++' . chr(13) . chr(13));
fwrite($log,'foreach ($setting[menu_item]) ' . print_r($name,true) . chr(13) . chr(13));
fwrite($log,'foreach ($menu_item_id) ' . print_r($menu_item_id,true) . chr(13) . chr(13));
fwrite($log,'foreach ($menu_item) ' . print_r($menu_item,true) . chr(13) . chr(13));
fclose($log);

					if (isset($menu_item['column'])) {
$log = fopen(DIR_LOGS . 'catalog_controler_extension_module_jetimpex_megamenu__index.log', 'a');
fwrite($log,'++++++++++++++++++++++++++++++++++' . chr(13) . chr(13));
fwrite($log,'if (isset($menu_item[column])) ' . print_r($menu_item['column'],true) . chr(13) . chr(13));
fclose($log);
						$column_categories = [];
						foreach ($menu_item['column'] as $column_id => $column) {
$log = fopen(DIR_LOGS . 'catalog_controler_extension_module_jetimpex_megamenu__index.log', 'a');
fwrite($log,'++++++++++++++++++++++++++++++++++' . chr(13) . chr(13));
fwrite($log,'foreach ($menu_item[column] ' . print_r($column,true) . chr(13) . chr(13));
fclose($log);
							$list   = '';
							$module = '';

							if ($column['category_show']) {
$log = fopen(DIR_LOGS . 'catalog_controler_extension_module_jetimpex_megamenu__index.log', 'a');
fwrite($log,'++++++++++++++++++++++++++++++++++' . chr(13) . chr(13));
fwrite($log,'if ($column[category_show]) ' . print_r($column['category_id'],true) . chr(13) . chr(13));
fclose($log);
								$category_lv_2      = $this->model_catalog_category->getCategory($column['category_id']);
$log = fopen(DIR_LOGS . 'catalog_controler_extension_module_jetimpex_megamenu__index.log', 'a');
fwrite($log,'++++++++++++++++++++++++++++++++++' . chr(13) . chr(13));
fwrite($log,'$category_lv_2 ) ' . print_r($category_lv_2,true) . chr(13) . chr(13));
fclose($log);
								$category_lv_2_href = $this->url->link('product/category', 'path=' . $column['category_id'], true);
							} else {
$log = fopen(DIR_LOGS . 'catalog_controler_extension_module_jetimpex_megamenu__index.log', 'a');
fwrite($log,'++++++++++++++++++++++++++++++++++' . chr(13) . chr(13));
fwrite($log,'if else ($column[category_show]) ' . print_r($column['category_id'],true) . chr(13) . chr(13));
fclose($log);
								$category_lv_2      = '';
								$category_lv_2_href = '';
							}

$log = fopen(DIR_LOGS . 'catalog_controler_extension_module_jetimpex_megamenu__index.log', 'a');
fwrite($log,'++++++++++++++++++++++++++++++++++' . chr(13) . chr(13));
fwrite($log,'switch ($column[content]) ) ' . print_r($column['content'],true) . chr(13) . chr(13));
fclose($log);
							switch ($column['content']) {
								case 4:
								$filter_data = array(
									'filter_category_id'  => $column['category_id'],
									'filter_sub_category' => true,
									'sort'                => 'p.date_added',
									'order'               => 'DESC',
									'start'               => isset($products_count[$column['category_id']]) ? $products_count[$column['category_id']] + 1 : 0,
									'limit'               => $column['prod_limit']
									);

								$results = $this->model_catalog_product->getProducts($filter_data);
								isset($products_count[$column['category_id']]) ? $products_count[$column['category_id']] += $column['prod_limit'] : $products_count[$column['category_id']] = (int)$column['prod_limit'];

								if ($results) {
									foreach ($results as $product_info) {
										$list .= "<li>\n<a href=\"" . $this->url->link('product/product', '&product_id=' . $product_info['product_id'], true) . "\">" . $product_info['name'] . "</a>\n</li>\n";
									}
								}
								break;
								case 3:
								isset($column_categories[$column['category_id']]) ? $column_categories[$column['category_id']]++ : $column_categories[$column['category_id']] = 0;

								$cats_2 = $this->model_catalog_category->getCategories($column['category_id']);

								if (isset($cats_2[$column_categories[$column['category_id']]])) {
									$list .= "<li class=\"submenu_title\">\n<a href=\"" . $this->url->link('product/category', 'path=' . $cats_2[$column_categories[$column['category_id']]]['category_id'], true) . "\">" . $cats_2[$column_categories[$column['category_id']]]['name'] . "</a>\n</li>\n";

									$cats_3 = $this->model_catalog_category->getCategories($cats_2[$column_categories[$column['category_id']]]['category_id']);

									foreach ($cats_3 as $cats_3_key => $cats_3_value) {
										if ($column['limit'] <= $cats_3_key) {
											break;
										}
										$list .= "<li>\n<a href=\"" . $this->url->link('product/category', 'path=' . $cats_3_value['category_id'], true) . "\">" . $cats_3_value['name'] . "</a>\n</li>\n";
									}
								}
								break;
								case 2:
								isset($column_categories[$column['category_id']]) ? $column_categories[$column['category_id']]++ : $column_categories[$column['category_id']] = 0;

								$cats_2 = $this->model_catalog_category->getCategories($column['category_id']);

								if (isset($cats_2[$column_categories[$column['category_id']]])) {
									$list .= "<li>\n<a href=\"" . $this->url->link('product/category', 'path=' . $cats_2[$column_categories[$column['category_id']]]['category_id'], true) . "\">" . $cats_2[$column_categories[$column['category_id']]]['name'] . "</a>\n</li>\n";

									$filter_data = array(
										'filter_category_id'  => $cats_2[$column_categories[$column['category_id']]]['category_id'],
										'filter_sub_category' => true,
										'sort'                => 'p.date_added',
										'order'               => 'DESC',
										'start'               => 0,
										'limit'               => $column['prod_limit']
										);

									$results = $this->model_catalog_product->getProducts($filter_data);
									if ($results) {
										foreach ($results as $product_info) {
											$list .= "<li>\n<a href=\"" . $this->url->link('product/product', '&product_id=' . $product_info['product_id'], true) . "\">" . $product_info['name'] . "</a>\n</li>\n";
										}
									}
								}
								break;
								case 1:
								isset($categories_count[$column['category_id']]) ? $categories_count[$column['category_id']] += $column['limit'] : $categories_count[$column['category_id']] = 0;
								$cats_2 = array_slice($this->model_catalog_category->getCategories($column['category_id']), $categories_count[$column['category_id']], $column['limit']);

								foreach ($cats_2 as $cat) {
									$list .= "<li>\n<a href=\"" . $this->url->link('product/category', 'path=' . $cat['category_id'], true) . "\">" . $cat['name'] . "</a>\n</li>\n";
								}
								break;
								case 0:
								$code = $this->model_extension_module_jetimpex_megamenu->getModuleCode($column['module_id']);
								$setting_info = $this->model_setting_module->getModule($column['module_id']);
								$module .= $this->load->controller('extension/module/' . $code, $setting_info);
								break;
							}
							$columns[] = array(
								'width'                => $column['width'],
								'custom_category'      => $category_lv_2,
								'custom_category_href' => $category_lv_2_href,
								'module'               => $module,
								'list'                 => $list
								);
						}
					}
					$e = 0;
				} elseif (isset($categories[$top_category_count])) {
					$column['category_show'] = 0;
					$name = $categories[$top_category_count]['name'];
					$href = $this->url->link('product/category', 'path=' . $categories[$top_category_count]['category_id'], true);

					$category_id = $categories[$top_category_count]['category_id'];
$log = fopen(DIR_LOGS . 'catalog_controler_extension_module_jetimpex_megamenu__index.log', 'a');
fwrite($log,'++++++++++++++++++++++++++++++++++' . chr(13) . chr(13));
fwrite($log,'if (isset($categories[$top_category_count])) name =  ' . print_r($name,true) . chr(13) . chr(13));
fwrite($log,'if (isset($categories[$top_category_count])) top_category_count = ' . print_r($top_category_count,true) . chr(13) . chr(13));
fwrite($log,'if (isset($categories[$top_category_count])) $categories[$top_category_count] =  ' . print_r($categories[$top_category_count],true) . chr(13) . chr(13));
fwrite($log,'if (isset($categories[$top_category_count])) $category_id =  ' . print_r($category_id,true) . chr(13) . chr(13));
fclose($log);

					$menu_item['submenu_type'] ? $multilevel = '' : $multilevel = $this->getCatTree($categories[$top_category_count]['category_id']) . "\n";
$log = fopen(DIR_LOGS . 'catalog_controler_extension_module_jetimpex_megamenu__index.log', 'a');
fwrite($log,'++++++++++++++++++++++++++++++++++' . chr(13) . chr(13));
fwrite($log,'if (isset($categories[$top_category_count])) submenu_type =  ' . print_r($menu_item['submenu_type'],true) . chr(13) . chr(13));
fwrite($log,'if (isset($categories[$top_category_count])) $multilevel =  ' . print_r($multilevel,true) . chr(13) . chr(13));
fclose($log);

					if (isset($menu_item['column'])) {
						$products_count    = [];
						$column_categories = [];
						foreach ($menu_item['column'] as $column_id => $column) {
$log = fopen(DIR_LOGS . 'catalog_controler_extension_module_jetimpex_megamenu__index.log', 'a');
fwrite($log,'++++++++++++++++++++++++++++++++++' . chr(13) . chr(13));
fwrite($log,'if (isset($menu_item[column])) $column_id =  ' . print_r($column_id,true) . chr(13) . chr(13));
fwrite($log,'if (isset($menu_item[column])) $column =  ' . print_r($column,true) . chr(13) . chr(13));
fclose($log);
							$list   = '';
							$module = '';

							if ($column['category_show']) {
								$category_lv_2      = $this->model_catalog_category->getCategory($column['category_id']);
								$category_lv_2_href = $this->url->link('product/category', 'path=' . $column['category_id'], true);
$log = fopen(DIR_LOGS . 'catalog_controler_extension_module_jetimpex_megamenu__index.log', 'a');
fwrite($log,'++++++++++++++++++++++++++++++++++' . chr(13) . chr(13));
fwrite($log,'if (isset($column[category_show])) $category_lv_2 =  ' . print_r($category_lv_2,true) . chr(13) . chr(13));
fwrite($log,'if (isset($column[category_show])) $category_lv_2_href =  ' . print_r($category_lv_2_href,true) . chr(13) . chr(13));
fclose($log);
							} else {
								$category_lv_2      = '';
								$category_lv_2_href = '';
							}

$log = fopen(DIR_LOGS . 'catalog_controler_extension_module_jetimpex_megamenu__index.log', 'a');
fwrite($log,'++++++++++++++++++++++++++++++++++' . chr(13) . chr(13));
fwrite($log,'switch $column[content] =  ' . print_r($column['content'],true) . chr(13) . chr(13));
fclose($log); 
							switch ($column['content']) {
								case 4:
								$filter_data = array(
									'filter_category_id'  => $category_id,
									'filter_sub_category' => true,
									'sort'                => 'p.date_added',
									'order'               => 'DESC',
									'start'               => isset($products_count[$category_id]) ? $products_count[$category_id] + 1 : 0,
									'limit'               => $column['prod_limit']
									);

								$results = $this->model_catalog_product->getProducts($filter_data);
								isset($products_count[$category_id]) ? $products_count[$category_id] += $column['prod_limit'] : $products_count[$category_id] = (int)$column['prod_limit'];

								if ($results) {
									foreach ($results as $product_info) {
										$list .= "<li>\n<a href=\"" . $this->url->link('product/product', '&product_id=' . $product_info['product_id'], true) . "\">" . $product_info['name'] . "</a>\n</li>\n";
									}
								}
								break;
								case 3:
								isset($column_categories[$category_id]) ? $column_categories[$category_id]++ : $column_categories[$category_id] = 0;

								$cats_2 = $this->model_catalog_category->getCategories($category_id);

								if (isset($cats_2[$column_categories[$category_id]])) {
									$list .= "<li class=\"submenu_title\">\n<a href=\"" . $this->url->link('product/category', 'path=' . $cats_2[$column_categories[$category_id]]['category_id'], true) . "\">" . $cats_2[$column_categories[$category_id]]['name'] . "</a></li>\n";

									$cats_3 = $this->model_catalog_category->getCategories($cats_2[$column_categories[$category_id]]['category_id']);


									foreach ($cats_3 as $cats_3_key => $cats_3_value) {
										if ($column['limit'] <= $cats_3_key) {
											break;
										}
										$list .= "<li>\n<a href=\"" . $this->url->link('product/category', 'path=' . $cats_3_value['category_id'], true) . "\">" . $cats_3_value['name'] . "</a>\n</li>\n";
									}

								}
								break;
								case 2:
								isset($column_categories[$category_id]) ? $column_categories[$category_id]++ : $column_categories[$category_id] = 0;
								$cats_2 = $this->model_catalog_category->getCategories($category_id);

								if (isset($cats_2[$column_categories[$category_id]])) {
									$list .= "<li>\n<a href=\"" . $this->url->link('product/category', 'path=' . $cats_2[$column_categories[$category_id]]['category_id'], true) . "\">" . $cats_2[$column_categories[$category_id]]['name'] . "</a>\n</li>\n";

									$filter_data = array(
										'filter_category_id'  => $cats_2[$column_categories[$category_id]]['category_id'],
										'filter_sub_category' => true,
										'sort'                => 'p.date_added',
										'order'               => 'DESC',
										'start'               => 0,
										'limit'               => $column['prod_limit']
										);

									$results = $this->model_catalog_product->getProducts($filter_data);
									if ($results) {
										foreach ($results as $product_info) {
											$list .= "<li>\n<a href=\"" . $this->url->link('product/product', '&product_id=' . $product_info['product_id'], true) . "\">" . $product_info['name'] . "</a>\n</li>\n";
										}
									}
								}
								break;
								case 1:
								isset($categories_count[$category_id]) ? $categories_count[$category_id] += $column['limit'] : $categories_count[$category_id] = 0;
								$cats_2 = array_slice($this->model_catalog_category->getCategories($category_id), $categories_count[$category_id], $column['limit']);

								foreach ($cats_2 as $cat) {
									$list .= "<li>\n<a href=\"" . $this->url->link('product/category', 'path=' . $cat['category_id'], true) . "\">" . $cat['name'] . "</a>\n</li>\n";
								}
								break;
								case 0:
								$code = $this->model_extension_module_jetimpex_megamenu->getModuleCode($column['module_id']);
								$setting_info = $this->model_setting_module->getModule($column['module_id']);
								$module .= $this->load->controller('extension/module/' . $code, $setting_info);
								break;
							}
							$columns[] = array(
								'width'                => $column['width'],
								'custom_category'      => $category_lv_2,
								'custom_category_href' => $category_lv_2_href,
								'module'               => $module,
								'list'                 => $list
								);
						}
					}

					$top_category_count++;
				} else {
					continue;
				}

				$liClass = ((!$menu_item['type'] && $menu_item['submenu_type']) || ($menu_item['type'] && $menu_item['submenu_show'])) ? 'sf-with-mega' : '';

				$data['menu_items'][] = array(
					'href'    => $href,
					'show_mobile' => isset($show_mobile) ? $show_mobile : '',
					'show_computer' => isset($show_computer) ? $show_computer : '',
					'name'    => $name,
					'mega'    => $liClass,
					'multi'   => $multilevel,
					'per-row' => $menu_item['columns-per-row'],
					'column'  => $columns
					);
			}
		}
$log = fopen(DIR_LOGS . 'catalog_controler_extension_module_jetimpex_megamenu__index.log', 'a');
fwrite($log,'++++++++++++++++++++++++++++++++++' . chr(13) . chr(13));
fwrite($log,'data ) ' . print_r($data,true) . chr(13) . chr(13));
fclose($log);

		return $this->load->view('extension/module/jetimpex_megamenu', $data);
	}

	function getCatTree($category_id = 0)
	{
		if (isset($this->request->get['path'])) {
			$parts = explode('_', (string)$this->request->get['path']);
		} else {
			$parts = array();
		}

		$this->load->model('catalog/category');
		$category_data = "";

		$categories = $this->model_catalog_category->getCategories((int)$category_id);

		foreach ($categories as $category) {
			$name = $category['name'];
			$href = $this->url->link('product/category', 'path=' . $category['category_id']);
			$class = in_array($category['category_id'], $parts) ? " class=\"active\"" : "";
			$parent = $this->getCatTree($category['category_id']);
			if ($parent) {
				$class = $class ? " class=\"active\"" : " class=\"parent\"";
			}
			$category_data .= "<li>\n<a href=\"" . $href . "\"" . $class . ">" . $name . "</a>" . $parent . "\n</li>\n";

		}

		return strlen($category_data) ? "<ul class=\"simple_menu\">\n" . $category_data . "\n</ul>\n" : "";
	}
}
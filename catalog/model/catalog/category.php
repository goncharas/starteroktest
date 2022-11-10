<?php
class ModelCatalogCategory extends Model {
	
//	private $url;
	private $cat_tree = [];
	private $keywords = [];
	private $queries = [];
//	private $product_categories = [];
//	private $valide_get_param;

	private function getPath($categories, $category_id, $current_path = []) {
	
		if(!$current_path)
			$current_path = [(int)$category_id];
			
		$path = $current_path;
		
		$parent_id = 0;
		
		if(isset($categories[$category_id]['parent_id'])) 
			$parent_id = (int)$categories[$category_id]['parent_id'];
					
		if($parent_id > 0) {
			$new_path =  array_merge ([$parent_id] , $current_path);
			$path =  $this->getPath($categories, $parent_id, $new_path);
		}

		return $path;
	}
	
	
	
	public function initHelpers() {
		// start category_tree
		if($this->config->get('config_seo_url_cache')){		
			$this->cat_tree = $this->cache->get('seopro.cat_tree');
		}
		
		if(!$this->cat_tree || empty($this->cat_tree)) {
		
			$this->cat_tree = [];
			
			$all_cat_query = $this->db->query("SELECT category_id, parent_id FROM " . DB_PREFIX . "category ORDER BY parent_id");
				
			$allcats = [];
			$categories = [];
			
			if($all_cat_query->num_rows) {
				$allcats = $all_cat_query->rows;
			};
			
			foreach ($allcats as $category) {
				$categories[$category['category_id']]['parent_id'] = $category['parent_id'];
			};
			unset ($allcats);
			
			foreach ($categories as $category_id => $category) {
				$path = $this->getPath($categories, $category_id);
				$this->cat_tree[$category_id]['path'] = $path;
					
			};
			
		}
		//end_category_tree
		
		//keyword_data
		if ($this->config->get('config_seo_url_cache')) {		

			$this->keywords = $this->cache->get('seopro.keywords');
			$this->queries = $this->cache->get('seopro.queries');

			if ((!$this->keywords || empty($this->keywords) || !$this->queries || empty($this->queries))) {
				
				$sql_keyword = 'keyword';
				if ($this->config->get('config_seopro_lowercase')) 
					$sql_keyword = 'LCASE(keyword) as '. $sql_keyword;		

				$sql = "SELECT " . $sql_keyword . ", query, store_id, language_id FROM " . DB_PREFIX . "seo_url WHERE 1";	
			
				$query = $this->db->query($sql);
				if($query->num_rows) {
					foreach($query->rows as $row) {
						$this->keywords[$row['query']][$row['store_id']][$row['language_id']] = $row['keyword'];
						$this->queries[$row['keyword']][$row['store_id']][$row['language_id']] = $row['query'];
					}
				}
			}
		}
		//end_keyword_data		
	}
	
	public function getPathByCategory($category_id) {
		
		$path = '';

		if ((int)$category_id < 1 && !isset($this->cat_tree[$category_id])) 
			return false;
		
		if (!empty($this->cat_tree[$category_id]['path']) && is_array($this->cat_tree[$category_id]['path'])) {
			$path = implode('_', $this->cat_tree[$category_id]['path']);
		}

		return $path;
			
	}
	
	
	public function getCategory($category_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.category_id = '" . (int)$category_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND c.status = '1'");

		return $query->row;
	}

	public function getCategories($parent_id = 0, $customWhere = '') {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'  AND c.status = '1' ". (!empty($customWhere)?" AND (".$customWhere.")":'') ." ORDER BY c.sort_order, LCASE(cd.name)");

		return $query->rows;
	}

	public function getCategoryFilters($category_id) {
		$implode = array();

		$query = $this->db->query("SELECT filter_id FROM " . DB_PREFIX . "category_filter WHERE category_id = '" . (int)$category_id . "'");

		foreach ($query->rows as $result) {
			$implode[] = (int)$result['filter_id'];
		}

		$filter_group_data = array();

		if ($implode) {
			$filter_group_query = $this->db->query("SELECT DISTINCT f.filter_group_id, fgd.name, fg.sort_order FROM " . DB_PREFIX . "filter f LEFT JOIN " . DB_PREFIX . "filter_group fg ON (f.filter_group_id = fg.filter_group_id) LEFT JOIN " . DB_PREFIX . "filter_group_description fgd ON (fg.filter_group_id = fgd.filter_group_id) WHERE f.filter_id IN (" . implode(',', $implode) . ") AND fgd.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY f.filter_group_id ORDER BY fg.sort_order, LCASE(fgd.name)");

			foreach ($filter_group_query->rows as $filter_group) {
				$filter_data = array();

				$filter_query = $this->db->query("SELECT DISTINCT f.filter_id, fd.name FROM " . DB_PREFIX . "filter f LEFT JOIN " . DB_PREFIX . "filter_description fd ON (f.filter_id = fd.filter_id) WHERE f.filter_id IN (" . implode(',', $implode) . ") AND f.filter_group_id = '" . (int)$filter_group['filter_group_id'] . "' AND fd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY f.sort_order, LCASE(fd.name)");

				foreach ($filter_query->rows as $filter) {
					$filter_data[] = array(
						'filter_id' => $filter['filter_id'],
						'name'      => $filter['name']
					);
				}

				if ($filter_data) {
					$filter_group_data[] = array(
						'filter_group_id' => $filter_group['filter_group_id'],
						'name'            => $filter_group['name'],
						'filter'          => $filter_data
					);
				}
			}
		}

		return $filter_group_data;
	}

	public function getCategoryLayoutId($category_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_to_layout WHERE category_id = '" . (int)$category_id . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if ($query->num_rows) {
			return (int)$query->row['layout_id'];
		} else {
			return 0;
		}
	}

	public function getTotalCategoriesByCategoryId($parent_id = 0) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND c.status = '1'");

		return $query->row['total'];
	}
}
<?php
// *	@source		See SOURCE.txt for source and other copyright.
// *	@license	GNU General Public License version 3; see LICENSE.txt

class ModelCatalogProduct extends Model {
	public function updateViewed($product_id) {
		$this->db->query("UPDATE " . DB_PREFIX . "product SET viewed = (viewed + 1) WHERE product_id = '" . (int)$product_id . "'");
	}

	public function getProduct($product_id) {
if(catalog_model_catalog_product__getProduct){
$log = fopen(DIR_LOGS . 'catalog_model_catalog_product__getProduct.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,' $product_id )' . print_r($product_id, true) . '==' . chr(10) . chr(13));
fclose($log);}
	$ret = array();
    $this->load->language('product/product');
		$pcur_value = $this->currency->getValue($this->config->get('config_product_currency'));
		
    $sql = "SELECT DISTINCT p.product_id, p.model, p.sku, p.upc, p.ean, p.jan, p.isbn, p.mpn, p.stock_status_id, p.image, p.manufacturer_id, p.shipping, p.points, p.tax_class_id, p.date_available, p.weight, p.weight_class_id, p.length, p.width, p.height, p.length_class_id, p.subtract, p.minimum, p.sort_order, p.status, p.viewed, p.date_added, p.date_modified, p.noindex, p.analog_group, p.subst_group, p.state, p.location,   m.name AS manufacturer, pp.price  AS price, pp.code curr_code,  pd.name, pd.description, pd.meta_title, pd.meta_description, pd.meta_keyword, pd.meta_h1, pd.tag, ll.location_id, ll.is_partner, ' ' as location_name, (SELECT SUM(IFNULL(ps.quantity,0)) FROM " . DB_PREFIX . "product_stock ps WHERE ps.product_id = p.product_id AND ps.location_id = ll.location_id ) AS quantity, (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special, (SELECT points FROM " . DB_PREFIX . "product_reward pr WHERE pr.product_id = p.product_id AND pr.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "') AS reward, IF(ll.is_partner = '1', CONCAT(ll.shipping_term, '" . ((int)$this->config->get('config_language_id') == '1' ? " дн." : " дн.") . "'), (SELECT ss.name FROM " . DB_PREFIX . "stock_status ss WHERE ss.stock_status_id = p.stock_status_id AND ss.language_id = '" . (int)$this->config->get('config_language_id') . "')) AS stock_status, (SELECT wcd.unit FROM " . DB_PREFIX . "weight_class_description wcd WHERE p.weight_class_id = wcd.weight_class_id AND wcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS weight_class, (SELECT lcd.unit FROM " . DB_PREFIX . "length_class_description lcd WHERE p.length_class_id = lcd.length_class_id AND lcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS length_class, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating, (SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review r2 WHERE r2.product_id = p.product_id AND r2.status = '1' GROUP BY r2.product_id) AS reviews ";
    $sql .= " FROM " . DB_PREFIX . "product p INNER JOIN  " . DB_PREFIX . "product_price pp ON( pp.product_id = p.product_id AND pp.price > 0  AND pp.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "')  LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "location ll ON (ll.location_id = pp.location_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id  AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' ) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'  AND p.status = '1' AND p.date_available <= NOW() and pp.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' ";

    $sql = "SELECT p.* FROM (" . $sql . ") p ";
		$sql .= " ORDER BY IF(p.location_id = '1' and p.quantity > 0, 10, IF(p.location_id = '1' and p.quantity > 0, 20, IF(p.location_id ='1', 30, 100)))";
    
    
if(catalog_model_catalog_product__getProduct){
$log = fopen(DIR_LOGS . 'catalog_model_catalog_product__getProduct.log', 'a');
fwrite($log,' $sql )' . print_r($sql, true) . '==' . chr(10) . chr(13));
fclose($log);}
		$query = $this->db->query($sql);

		if ($query->num_rows) {
if(catalog_model_catalog_product__getProduct){
$log = fopen(DIR_LOGS . 'catalog_model_catalog_product__getProduct.log', 'a');
fwrite($log,' $query->num_rows )' . print_r($query->num_rows, true) . '==' . chr(10) . chr(13));
fclose($log);}
			if (empty($query->row['quantity'])) {
				$stock = $query->row['stock_status'];
			} elseif ($query->row['quantity'] <= 0) {
				$stock = $query->row['stock_status'];
			} elseif ($this->config->get('config_stock_display')) {
				$stock = $query->row['quantity'];
			} else {
			  $stock = $query->row['is_partner'] == '1' ? $query->row['stock_status'] . ' ' : $this->language->get('text_instock');
//				$stock = $this->language->get('text_instock');
			}
	  
      if(!empty($query->row['curr_code']))
        $pcur_value = $this->currency->getValue($query->row['curr_code']);
      else
        $pcur_value = $this->currency->getValue($this->config->get('config_product_currency'));
 
if(catalog_model_catalog_product__getProduct){
$log = fopen(DIR_LOGS . 'catalog_model_catalog_product__getProduct.log', 'a');
fwrite($log,' $pcur_value )' . print_r($pcur_value, true) . '==' . chr(10) . chr(13));
fclose($log);}
		  
			$ret = array(
				'product_id'       => $query->row['product_id'],
				'name'             => $query->row['name'],
				'description'      => $query->row['description'],
				'meta_title'       => $query->row['meta_title'],
				'noindex'          => $query->row['noindex'],
				'meta_h1'	         => $query->row['meta_h1'],
				'meta_description' => $query->row['meta_description'],
				'meta_keyword'     => $query->row['meta_keyword'],
				'tag'              => $query->row['tag'],
				'model'            => $query->row['model'],
				'sku'              => $query->row['sku'],
				'upc'              => $query->row['upc'],
				'ean'              => $query->row['ean'],
				'jan'              => $query->row['jan'],
				'isbn'             => $query->row['isbn'],
				'mpn'              => $query->row['mpn'],
				'location'         => $query->row['location'],
				'quantity'         => $query->row['quantity'],
//				'stock_status'     => $query->row['stock_status'],
				'stock_status'     => $stock,
				'image'            => $query->row['image'],
				'manufacturer_id'  => $query->row['manufacturer_id'],
				'manufacturer'     => $query->row['manufacturer'],
				'price'            => ($query->row['discount'] ? $query->row['discount'] : (empty($query->row['price']) ? 0 : $query->row['price'])) / $pcur_value,
				'discount'         => $query->row['discount'] / $pcur_value,
				'special'          => $query->row['special'] / $pcur_value,
				'reward'           => $query->row['reward'],
				'points'           => $query->row['points'],
				'tax_class_id'     => $query->row['tax_class_id'],
				'date_available'   => $query->row['date_available'],
				'weight'           => $query->row['weight'],
				'weight_class_id'  => $query->row['weight_class_id'],
				'length'           => $query->row['length'],
				'width'            => $query->row['width'],
				'height'           => $query->row['height'],
				'length_class_id'  => $query->row['length_class_id'],
				'subtract'         => $query->row['subtract'],
				'rating'           => round($query->row['rating']),
				'reviews'          => $query->row['reviews'] ? $query->row['reviews'] : 0,
				'minimum'          => $query->row['minimum'],
				'sort_order'       => $query->row['sort_order'],
				'status'           => $query->row['status'],
				'date_added'       => $query->row['date_added'],
				'date_modified'    => $query->row['date_modified'],
				'viewed'           => $query->row['viewed'],
				'location_id'      => $query->row['location_id'],
				'location_name'    => $query->row['location_name'],
        'is_partner'       => $query->row['is_partner'],
        'hasstock'         => ($query->row['stock_status'] == $stock ? '0' : '1' ) 
			);
// SAVIN 20220730 beg      
if(catalog_model_catalog_product__getProduct){
$log = fopen(DIR_LOGS . 'catalog_model_catalog_product__getProduct.log', 'a');
fwrite($log,' $ret )' . print_r($ret, true) . '==' . chr(10) . chr(13));
fclose($log);}
// SAVIN 20220730 end      

			if($ret['price'] == '1' && $ret['location_id'] == '1'){
				foreach($query->rows as $row){
					if(!empty($row['curr_code']))
						$pcur_value = $this->currency->getValue($row['curr_code']);
					else
						$pcur_value = $this->currency->getValue($this->config->get('config_product_currency'));
					$prc = ($row['discount'] ? $row['discount'] : (empty($row['price']) ? 0 : $row['price'])) / $pcur_value;
					if((float)$ret['price'] > $prc || $ret['price'] == '1'){
						$ret['price'] = $prc;
						if (empty($row['quantity'])) {
							$stock = $row['stock_status'];
						} elseif ($row['quantity'] <= 0) {
							$stock = $row['stock_status'];
						} elseif ($this->config->get('config_stock_display')) {
							$stock = $row['quantity'];
						} else {
							$stock = $row['is_partner'] == '1' ? $row['stock_status'] . ' ' : $this->language->get('text_instock');
						}
						$ret['stock_status'] = $stock;
		        $ret['location_id']  = $row['location_id'];
		        $ret['location_name']= $row['location_name'];
						$ret['is_partner']   = $row['is_partner'];
						$ret['hasstock']     = ($row['stock_status'] == $stock ? '0' : '1' ); 
					}
				}
			} 
			return $ret;
		} else {
			return false;
		}
	}

	public function getProducts($data = array()) {
if(catalog_model_catalog_product__getProducts){
$log = fopen(DIR_LOGS . 'catalog_model_catalog_product__getProducts.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,' $data )' . print_r($data, true) . '==' . chr(10) . chr(13));
fclose($log);}
		$sql = "SELECT p.product_id, pp.price  AS price, " . (isset($data['quantity']) ? "(SELECT SUM(ps.quantity) FROM " . DB_PREFIX . "product_stock ps WHERE ps.product_id = p.product_id )" : "0") . " AS quantity, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating, '' AS discount, '' AS special";

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
			} else {
				$sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
			}

			if (!empty($data['filter_filter'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p2c.product_id = pf.product_id) LEFT JOIN " . DB_PREFIX . "product p ON (pf.product_id = p.product_id)";
			} else {
				$sql .= " INNER JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id " . (!empty($data['filter_category_id']) ? " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'" : "") . ")";
			}
		} else {
			$sql .= " FROM " . DB_PREFIX . "product p";
		}
//     $sql .= " INNER JOIN " . DB_PREFIX . "product_price pp ON( pp.product_id = p.product_id  AND pp.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "') ";

    
    $forbidden = '\/:*?"<>|+\-%!@.';
    if (!empty($data['filter_name'])) {
      $string = $data['filter_name'];
      $stringWithoutForbiddenCharacters = preg_replace("/[${forbidden}]/", '', $string); 
      $data['filter_name'] = $stringWithoutForbiddenCharacters;    
if(catalog_model_catalog_product__getProducts){
$log = fopen(DIR_LOGS . 'catalog_model_catalog_product__getProducts.log', 'a');
fwrite($log,' $data[filter_name] )' . print_r($data['filter_name'], true) . '==' . chr(10) . chr(13));
fclose($log);}
    }

    if (!empty($data['filter_tag'])) {
      $string = $data['filter_tag'];
      $stringWithoutForbiddenCharacters = preg_replace("/[${forbidden}]/", '', $string); 
      $data['filter_tag'] = $stringWithoutForbiddenCharacters;    
if(catalog_model_catalog_product__getProducts){
$log = fopen(DIR_LOGS . 'catalog_model_catalog_product__getProducts.log', 'a');
fwrite($log,' $data[filter_tag] )' . print_r($data['filter_tag'], true) . '==' . chr(10) . chr(13));
fclose($log);}
    }
	if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
     $sql .= " INNER JOIN " . DB_PREFIX . "product_price pp ON( pp.product_id = p.product_id  AND pp.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pp.price > 0) ";
	} else {
		$sql .= " INNER JOIN " . DB_PREFIX . "product_price pp ON( pp.product_id = p.product_id  AND pp.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pp.price > 1) ";
	}
		
		
        if (!empty($data['filter_tecdoc'])) {
            $sql .= " LEFT JOIN oc_manufacturer ocm ON (p.manufacturer_id = ocm.manufacturer_id)";
        }
//		$sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";
		
		$pd = false;
		if (!empty($data['filter_name']) || !empty($data['filter_tag']) || (isset($data['sort']) && $data['sort'] == 'pd.name') ) {
			$sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' ";
		    $pd = true;
		} else{
			$sql .= " WHERE 1 = 1 ";
		}
//		$sql .= " AND p.status = '1' AND p.date_available <= NOW() ";
		$sql .= " AND p.status = '1' ";
		
//		$sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() ";
    
//    $sql .= " AND pp.price > 0 ";

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
			} else {
				$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
			}

			if (!empty($data['filter_filter'])) {
				$implode = array();

				$filters = explode(',', $data['filter_filter']);

				foreach ($filters as $filter_id) {
					$implode[] = (int)$filter_id;
				}

				$sql .= " AND pf.filter_id IN (" . implode(',', $implode) . ")";
			}
		}

	if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
 //      $sql .= " AND pp.price > 0 ";
			$sql .= " AND (";

			if (!empty($data['filter_name'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

				foreach ($words as $word) {
//					$implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
                    $implode[] = "+" . $this->db->escape($word) . " ";
				}

				if ($implode) {
//					$sql .= " " . implode(" AND ", $implode) . "";
 				    $sql .= " MATCH(pd.name)  AGAINST('" . implode("", $implode) . "' IN BOOLEAN MODE)";
				}

				if (!empty($data['filter_description'])) {
					$sql .= " OR pd.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
				}
			}

			if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
				$sql .= " OR ";
			}

			if (!empty($data['filter_tag'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_tag'])));

				foreach ($words as $word) {
				  // WHERE MATCH (title,body) AGAINST ('database');
					//$implode[] = "pd.tag LIKE '%" . $this->db->escape($word) . "%'";
                   $implode[] = "+" . $this->db->escape($word) . " ";
				}

				if ($implode) {
				  $sql .= " MATCH(pd.tag)  AGAINST('" . implode("", $implode) . "' IN BOOLEAN MODE)";
					// $sql .= " " . implode(" AND ", $implode) . "";
				}
			}

			if (false && !empty($data['filter_name'])) {
				$sql .= " OR LCASE(p.model) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.sku) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
			//$sql .= " OR LCASE(p.upc) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				//$sql .= " OR LCASE(p.ean) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				//$sql .= " OR LCASE(p.jan) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				//$sql .= " OR LCASE(p.isbn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				//$sql .= " OR LCASE(p.mpn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
			}

			$sql .= ")";
//		} else {
//			    $sql .= " AND pp.price > 1 ";
		}

		if (!empty($data['filter_manufacturer_id'])) {
			$sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
		}

        if (!empty($data['filter_tecdoc'])) {
            $sql .= " AND p.sku IN (
                SELECT part_number 
                FROM session_search ss
                WHERE ss.session_id = '". $this->session->getId() ."' AND ss.supplier_name = ocm.name
             )";            
//                INNER JOIN oc_manufacturer m ON (ss.supplier_name = m.name)
        }
        
		$sql .= " GROUP BY p.product_id";

		$sort_data = array(
			'pd.name',
			'p.model',
			'quantity',
			'price',
		    'p.price',
			'rating',
			'p.sort_order',
			'p.date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
				$sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
			} elseif ($data['sort'] == 'p.price') {
				// $sql .= " ORDER BY (CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE p.price END)";
				$sql .= " ORDER BY price";
			} else {
				$sql .= " ORDER BY " . $data['sort'];
			}
		} else {
			$sql .= " ORDER BY p.sort_order";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			// $sql .= " DESC, LCASE(pd.name) DESC";
			$sql .= " DESC" . ($pd ? ", LCASE(pd.name) DESC" : "");
		} else {
			// $sql .= " ASC, LCASE(pd.name) ASC";
			$sql .= " ASC" . ($pd ? ", LCASE(pd.name)  ASC" : "");
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if (!isset($data['start']) || $data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$product_data = array();
if(catalog_model_catalog_product__getProducts){
$log = fopen(DIR_LOGS . 'catalog_model_catalog_product__getProducts.log', 'a');
fwrite($log,' $sql )' . print_r($sql, true) . '==' . chr(10) . chr(13));
fclose($log);}
 
		$query = $this->db->query($sql, false);

		foreach ($query->rows as $result) {
			$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
/* $log = fopen(DIR_LOGS . 'catalog_model_catalog_product__getProducts.log', 'a');
fwrite($log,' $product_data[' . $result['product_id'] . '] )' . print_r($product_data[$result['product_id']], true) . '==' . chr(10) . chr(13));
fclose($log);*/
		}
if(catalog_model_catalog_product__getProducts){
$log = fopen(DIR_LOGS . 'catalog_model_catalog_product__getProducts.log', 'a');
fwrite($log,' $product_data )' . print_r($product_data, true) . '==' . chr(10) . chr(13));
fclose($log);}

		return $product_data;
	}

	public function getProductSpecials($data = array()) {
		$sql = "SELECT DISTINCT ps.product_id, (SELECT AVG(rating) FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = ps.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating FROM " . DB_PREFIX . "product_special ps LEFT JOIN " . DB_PREFIX . "product p ON (ps.product_id = p.product_id) INNER JOIN " . DB_PREFIX . "product_price pp ON( pp.product_id = p.product_id AND pp.price > 0  AND pp.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "') LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW()))  ";

		$sort_data = array(
			'pd.name',
			'p.model',
			'ps.price',
			'rating',
			'p.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
				$sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
			} else {
				$sql .= " ORDER BY " . $data['sort'];
			}
		} else {
			$sql .= " ORDER BY p.sort_order";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC, LCASE(pd.name) DESC";
		} else {
			$sql .= " ASC, LCASE(pd.name) ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$product_data = array();

		$query = $this->db->query($sql);

		foreach ($query->rows as $result) {
			$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
		}

		return $product_data;
	}

	public function getProductOptcustomersPrice($data = array()) {
if(catalog_model_catalog_product__getProductOptcustomersPrice){
$log = fopen(DIR_LOGS . 'catalog_model_catalog_product__getProductOptcustomersPrice.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,' $data )' . print_r($data, true) . '==' . chr(10) . chr(13));
fclose($log);}
		$sql = "SELECT DISTINCT ps.product_id, (SELECT AVG(rating) FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = ps.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating FROM " . DB_PREFIX . "product_special ps LEFT JOIN " . DB_PREFIX . "product p ON (ps.product_id = p.product_id) INNER JOIN " . DB_PREFIX . "product_price pp ON( pp.product_id = p.product_id AND pp.price > 0  AND pp.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "') LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW()))    GROUP BY ps.product_id";

		$sort_data = array(
			'pd.name',
			'p.model',
			'ps.price',
			'rating',
			'p.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
				$sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
			} else {
				$sql .= " ORDER BY " . $data['sort'];
			}
		} else {
			$sql .= " ORDER BY p.sort_order";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC, LCASE(pd.name) DESC";
		} else {
			$sql .= " ASC, LCASE(pd.name) ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$product_data = array();
if(catalog_model_catalog_product__getProductOptcustomersPrice){
$log = fopen(DIR_LOGS . 'catalog_model_catalog_product__getProductOptcustomersPrice.log', 'a');
fwrite($log,' $sql )' . print_r($sql, true) . '==' . chr(10) . chr(13));
fclose($log);}

		$query = $this->db->query($sql);

		foreach ($query->rows as $result) {
			$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
		}

		return $product_data;
	}

	public function getLatestProducts($limit) {
		$product_data = $this->cache->get('product.latest.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit);

		if (!$product_data) {
			$query = $this->db->query("SELECT p.product_id FROM " . DB_PREFIX . "product p INNER JOIN  " . DB_PREFIX . "product_price pp ON( pp.product_id = p.product_id AND pp.price > 0  AND pp.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "') LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'  ORDER BY p.date_added DESC LIMIT " . (int)$limit);

			foreach ($query->rows as $result) {
				$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
			}

			$this->cache->set('product.latest.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit, $product_data);
		}

		return $product_data;
	}

	public function getPopularProducts($limit) {
		$product_data = $this->cache->get('product.popular.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit);
	
		if (!$product_data) {
			$query = $this->db->query("SELECT p.product_id FROM " . DB_PREFIX . "product p INNER JOIN  " . DB_PREFIX . "product_price pp ON( pp.product_id = p.product_id AND pp.price > 0  AND pp.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "')  LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'   ORDER BY p.viewed DESC, p.date_added DESC LIMIT " . (int)$limit);
	
			foreach ($query->rows as $result) {
				$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
			}
			
			$this->cache->set('product.popular.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit, $product_data);
		}
		
		return $product_data;
	}

	public function getBestSellerProducts($limit) {
		$product_data = $this->cache->get('product.bestseller.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit);

		if (!$product_data) {
			$product_data = array();

			$query = $this->db->query("SELECT op.product_id, SUM(op.quantity) AS total FROM " . DB_PREFIX . "order_product op LEFT JOIN `" . DB_PREFIX . "order` o ON (op.order_id = o.order_id) LEFT JOIN `" . DB_PREFIX . "product` p ON (op.product_id = p.product_id) INNER JOIN  " . DB_PREFIX . "product_price pp ON( pp.product_id = p.product_id AND pp.price > 0  AND pp.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "')  LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE o.order_status_id > '0' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'    GROUP BY op.product_id ORDER BY total DESC LIMIT " . (int)$limit);

			foreach ($query->rows as $result) {
				$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
			}

			$this->cache->set('product.bestseller.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit, $product_data);
		}

		return $product_data;
	}

	public function getProductAttributes($product_id) {
		$product_attribute_group_data = array();

		$product_attribute_group_query = $this->db->query("SELECT ag.attribute_group_id, agd.name FROM " . DB_PREFIX . "product_attribute pa LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id) LEFT JOIN " . DB_PREFIX . "attribute_group ag ON (a.attribute_group_id = ag.attribute_group_id) LEFT JOIN " . DB_PREFIX . "attribute_group_description agd ON (ag.attribute_group_id = agd.attribute_group_id) WHERE pa.product_id = '" . (int)$product_id . "' AND agd.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY ag.attribute_group_id ORDER BY ag.sort_order, agd.name");

		foreach ($product_attribute_group_query->rows as $product_attribute_group) {
			$product_attribute_data = array();

			$product_attribute_query = $this->db->query("SELECT a.attribute_id, ad.name, pa.text FROM " . DB_PREFIX . "product_attribute pa LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id) LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (a.attribute_id = ad.attribute_id) WHERE pa.product_id = '" . (int)$product_id . "' AND a.attribute_group_id = '" . (int)$product_attribute_group['attribute_group_id'] . "' AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "' AND pa.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY a.sort_order, ad.name");

			foreach ($product_attribute_query->rows as $product_attribute) {
				$product_attribute_data[] = array(
					'attribute_id' => $product_attribute['attribute_id'],
					'name'         => $product_attribute['name'],
					'text'         => $product_attribute['text']
				);
			}

			$product_attribute_group_data[] = array(
				'attribute_group_id' => $product_attribute_group['attribute_group_id'],
				'name'               => $product_attribute_group['name'],
				'attribute'          => $product_attribute_data
			);
		}

		return $product_attribute_group_data;
	}

	public function getProductOptions($product_id) {
		$pcur_value = $this->currency->getValue($this->config->get('config_product_currency'));
    $query = $this->db->query("select pp.code as curr_code from " . DB_PREFIX . "product_price pp where pp.product_id = '" . (int)$product_id . "' AND pp.price > 0  AND pp.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "'"); 
    if($query->num_rows)
        $pcur_value = $this->currency->getValue($query->row['curr_code']);
    else
        $pcur_value = $this->currency->getValue($this->config->get('config_product_currency'));

		$product_option_data = array();
		$product_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY o.sort_order");

		foreach ($product_option_query->rows as $product_option) {
			$product_option_value_data = array();

			$product_option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_id = '" . (int)$product_id . "' AND pov.product_option_id = '" . (int)$product_option['product_option_id'] . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY ov.sort_order");

			foreach ($product_option_value_query->rows as $product_option_value) {
				$product_option_value_data[] = array(
					'product_option_value_id' => $product_option_value['product_option_value_id'],
					'option_value_id'         => $product_option_value['option_value_id'],
					'name'                    => $product_option_value['name'],
					'image'                   => $product_option_value['image'],
					'quantity'                => $product_option_value['quantity'],
					'subtract'                => $product_option_value['subtract'],
					'price'                   => $product_option_value['price'] / $pcur_value,
					'price_prefix'            => $product_option_value['price_prefix'],
					'weight'                  => $product_option_value['weight'],
					'weight_prefix'           => $product_option_value['weight_prefix']
				);
			}

			$product_option_data[] = array(
				'product_option_id'    => $product_option['product_option_id'],
				'product_option_value' => $product_option_value_data,
				'option_id'            => $product_option['option_id'],
				'name'                 => $product_option['name'],
				'type'                 => $product_option['type'],
				'value'                => $product_option['value'],
				'required'             => $product_option['required']
			);
		}

		return $product_option_data;
	}

	public function getProductDiscounts($product_id) {
		$pcur_value = $this->currency->getValue($this->config->get('config_product_currency'));
    $query = $this->db->query("select pp.code as curr_code from " . DB_PREFIX . "product_price pp where pp.product_id = '" . (int)$product_id . "' AND pp.price > 0  AND pp.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "'"); 
    if($query->num_rows)
        $pcur_value = $this->currency->getValue($query->row['curr_code']);
    else
        $pcur_value = $this->currency->getValue($this->config->get('config_product_currency'));
    
		$query = $this->db->query("SELECT pd.*, pd.price / " . (float)$pcur_value . " as price FROM " . DB_PREFIX . "product_discount pd WHERE pd.product_id = '" . (int)$product_id . "' AND pd.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pd.quantity > 1 AND ((pd.date_start = '0000-00-00' OR pd.date_start < NOW()) AND (pd.date_end = '0000-00-00' OR pd.date_end > NOW()))  ORDER BY pd.quantity ASC, pd.priority ASC, pd.price ASC");

		return $query->rows;
	}

	public function getProductImages($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "' ORDER BY sort_order ASC");

		return $query->rows;
	}

	public function getProductRelated($product_id) {
		$product_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_related pr LEFT JOIN " . DB_PREFIX . "product p ON (pr.related_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pr.product_id = '" . (int)$product_id . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

		foreach ($query->rows as $result) {
			$product_data[$result['related_id']] = $this->getProduct($result['related_id']);
		}

		return $product_data;
	}

	public function getProductLayoutId($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if ($query->num_rows) {
			return (int)$query->row['layout_id'];
		} else {
			return 0;
		}
	}

	public function getCategories($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");

		return $query->rows;
	}

	public function getTotalProducts($data = array()) {
if(catalog_model_catalog_product__getTotalProducts){
$log = fopen(DIR_LOGS . 'catalog_model_catalog_product__getTotalProducts.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,' $data )' . print_r($data, true) . '==' . chr(10) . chr(13));
fclose($log);}
		$sql = "SELECT COUNT(DISTINCT p.product_id) AS total";

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
			} else {
				$sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
			}

			if (!empty($data['filter_filter'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p2c.product_id = pf.product_id) LEFT JOIN " . DB_PREFIX . "product p ON (pf.product_id = p.product_id)";
			} else {
//				$sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
				$sql .= " INNER JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id " . (!empty($data['filter_category_id']) ? " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'" : "") . ")";
			}
		} else {
			$sql .= " FROM " . DB_PREFIX . "product p";
		}
//     $sql .= " INNER JOIN " . DB_PREFIX . "product_price pp ON( pp.product_id = p.product_id  AND pp.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "') ";

    
    $forbidden = '\/:*?"<>|+\-%!@.';
    if (!empty($data['filter_name'])) {
      $string = $data['filter_name'];
      $stringWithoutForbiddenCharacters = preg_replace("/[${forbidden}]/", '', $string); 
      $data['filter_name'] = $stringWithoutForbiddenCharacters;    
if(catalog_model_catalog_product__getProducts){
$log = fopen(DIR_LOGS . 'catalog_model_catalog_product__getProducts.log', 'a');
fwrite($log,' $data[filter_name] )' . print_r($data['filter_name'], true) . '==' . chr(10) . chr(13));
fclose($log);}
    }

    if (!empty($data['filter_tag'])) {
      $string = $data['filter_tag'];
      $stringWithoutForbiddenCharacters = preg_replace("/[${forbidden}]/", '', $string); 
      $data['filter_tag'] = $stringWithoutForbiddenCharacters;    
if(catalog_model_catalog_product__getProducts){
$log = fopen(DIR_LOGS . 'catalog_model_catalog_product__getProducts.log', 'a');
fwrite($log,' $data[filter_tag] )' . print_r($data['filter_tag'], true) . '==' . chr(10) . chr(13));
fclose($log);}
    }
	if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
     $sql .= " INNER JOIN " . DB_PREFIX . "product_price pp ON( pp.product_id = p.product_id  AND pp.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pp.price > 0) ";
	} else {
		$sql .= " INNER JOIN " . DB_PREFIX . "product_price pp ON( pp.product_id = p.product_id  AND pp.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pp.price > 1) ";
	}
		
		
        if (!empty($data['filter_tecdoc'])) {
            $sql .= " LEFT JOIN oc_manufacturer ocm ON (p.manufacturer_id = ocm.manufacturer_id)";
        }
//		$sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";
		
		$pd = false;
		if (!empty($data['filter_name']) || !empty($data['filter_tag']) || (isset($data['sort']) && $data['sort'] == 'pd.name') ) {
			$sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' ";
		    $pd = true;
		} else{
			$sql .= " WHERE 1 = 1 ";
		}
		$sql .= " AND p.status = '1' AND p.date_available <= NOW() ";
		
//		$sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() ";
    
//    $sql .= " AND pp.price > 0 ";

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
			} else {
				$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
			}

			if (!empty($data['filter_filter'])) {
				$implode = array();

				$filters = explode(',', $data['filter_filter']);

				foreach ($filters as $filter_id) {
					$implode[] = (int)$filter_id;
				}

				$sql .= " AND pf.filter_id IN (" . implode(',', $implode) . ")";
			}
		}

	if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
 //      $sql .= " AND pp.price > 0 ";
			$sql .= " AND (";

			if (!empty($data['filter_name'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

				foreach ($words as $word) {
//					$implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
                    $implode[] = "+" . $this->db->escape($word) . " ";
				}

				if ($implode) {
//					$sql .= " " . implode(" AND ", $implode) . "";
 				    $sql .= " MATCH(pd.name)  AGAINST('" . implode("", $implode) . "' IN BOOLEAN MODE)";
				}

				if (!empty($data['filter_description'])) {
					$sql .= " OR pd.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
				}
			}

			if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
				$sql .= " OR ";
			}

			if (!empty($data['filter_tag'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_tag'])));

				foreach ($words as $word) {
				  // WHERE MATCH (title,body) AGAINST ('database');
					//$implode[] = "pd.tag LIKE '%" . $this->db->escape($word) . "%'";
                   $implode[] = "+" . $this->db->escape($word) . " ";
				}

				if ($implode) {
				  $sql .= " MATCH(pd.tag)  AGAINST('" . implode("", $implode) . "' IN BOOLEAN MODE)";
					// $sql .= " " . implode(" AND ", $implode) . "";
				}
			}

			if (false && !empty($data['filter_name'])) {
				$sql .= " OR LCASE(p.model) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.sku) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
			//$sql .= " OR LCASE(p.upc) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				//$sql .= " OR LCASE(p.ean) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				//$sql .= " OR LCASE(p.jan) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				//$sql .= " OR LCASE(p.isbn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				//$sql .= " OR LCASE(p.mpn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
			}

			$sql .= ")";
//		} else {
//			    $sql .= " AND pp.price > 1 ";
		}

		if (!empty($data['filter_manufacturer_id'])) {
			$sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
		}
//        if (!empty($data['filter_tecdoc'])) {
//            $sql .= " AND p.sku IN (
//                SELECT part_number 
//                FROM session_search ss
//                INNER JOIN oc_manufacturer m ON (ss.supplier_name = m.name)
//                WHERE ss.session_id = '". $this->session->getId() ."'
//             )";
//        }
if(catalog_model_catalog_product__getTotalProducts){
$log = fopen(DIR_LOGS . 'catalog_model_catalog_product__getTotalProducts.log', 'a');
fwrite($log,' $sql )' . print_r($sql, true) . '==' . chr(10) . chr(13));
fclose($log);}

		$query = $this->db->query($sql, false);
if(catalog_model_catalog_product__getTotalProducts){
$log = fopen(DIR_LOGS . 'catalog_model_catalog_product__getTotalProducts.log', 'a');
fwrite($log,' $query->row[total] )' . print_r($query->row['total'], true) . '==' . chr(10) . chr(13));
fclose($log);}

		return $query->row['total'];
	}
    
    public function getTotalProducts_ForSessionSearch($data = array()) {
        $sql = "SELECT COUNT(DISTINCT p.product_id) AS total "
        . "FROM session_search ss "
        . "INNER JOIN oc_product p on  p.sku = ss.part_number "
        . "INNER JOIN oc_manufacturer m ON (ss.supplier_name = m.name) "
        . "LEFT JOIN oc_product_description pd ON (p.product_id = pd.product_id) "
        . "LEFT JOIN oc_product_to_store p2s ON (p.product_id = p2s.product_id) "
        . "WHERE pd.language_id = " . (int)$this->config->get('config_language_id') . " AND p.status = '1' "
            . "AND ss.session_id = 'ca8e0f6e532b7f2cd0072b56b1' "
            . "AND p.date_available <= NOW() AND p2s.store_id = '0' ";
//            . "AND ( "
//                . "EXISTS (SELECT 1 FROM  oc_product_price pp WHERE pp.product_id = p.product_id AND pp.price > 0)  OR  "
//                . "EXISTS (SELECT 1 FROM  oc_product_stock ps WHERE ps.product_id = p.product_id AND ps.quantity > 0) OR "
//                . "(p.image IS NOT NULL AND p.image <> '') "
//            . ") ";

		$query = $this->db->query($sql, false);

		return $query->row['total'];
	}

	public function getProfile($product_id, $recurring_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "recurring r JOIN " . DB_PREFIX . "product_recurring pr ON (pr.recurring_id = r.recurring_id AND pr.product_id = '" . (int)$product_id . "') WHERE pr.recurring_id = '" . (int)$recurring_id . "' AND status = '1' AND pr.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "'");

		return $query->row;
	}

	public function getProfiles($product_id) {
		$query = $this->db->query("SELECT rd.* FROM " . DB_PREFIX . "product_recurring pr JOIN " . DB_PREFIX . "recurring_description rd ON (rd.language_id = " . (int)$this->config->get('config_language_id') . " AND rd.recurring_id = pr.recurring_id) JOIN " . DB_PREFIX . "recurring r ON r.recurring_id = rd.recurring_id WHERE pr.product_id = " . (int)$product_id . " AND status = '1' AND pr.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' ORDER BY sort_order ASC");

		return $query->rows;
	}

	public function getTotalProductSpecials() {
		$query = $this->db->query("SELECT COUNT(DISTINCT ps.product_id) AS total FROM " . DB_PREFIX . "product_special ps LEFT JOIN " . DB_PREFIX . "product p ON (ps.product_id = p.product_id) INNER JOIN  " . DB_PREFIX . "product_price pp ON( pp.product_id = p.product_id AND pp.price > 0  AND pp.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "')  LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ");

		if (isset($query->row['total'])) {
			return $query->row['total'];
		} else {
			return 0;
		}
	}
	
	public function getTotalProductOptcustomersPrice() {
		$query = $this->db->query("SELECT COUNT(DISTINCT ps.product_id) AS total FROM " . DB_PREFIX . "product_special ps LEFT JOIN " . DB_PREFIX . "product p ON (ps.product_id = p.product_id) INNER JOIN  " . DB_PREFIX . "product_price pp ON( pp.product_id = p.product_id AND pp.price > 0  AND pp.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "')  LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ");

		if (isset($query->row['total'])) {
			return $query->row['total'];
		} else {
			return 0;
		}
	}
	
	public function getProductQuantities($product_id) {
		$this->load->language('product/product');
if(catalog_model_catalog_product__getProductQuantities){
$log = fopen(DIR_LOGS . 'catalog_model_catalog_product__getProductQuantities.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,' $product_id )' . print_r($product_id, true) . '==' . chr(10) . chr(13));
fclose($log);}
		$pcur_value = $this->currency->getValue($this->config->get('config_product_currency'));
	 	
		$sql = "SELECT p.product_id, ops.location_id, pd.name, pd.description, pd.meta_title, p.noindex, pd.meta_h1, pd.meta_description, pd.meta_keyword, pd.tag, p.model, p.sku, p.upc, p.ean, p.jan, p.isbn, p.mpn, p.location, p.image, p.manufacturer_id, m.name AS manufacturer, p.points, p.tax_class_id, p.date_available, p.weight, p.weight_class_id, p.length, p.width, p.height, p.length_class_id, p.subtract, p.minimum, p.sort_order, p.status, p.date_added, p.date_modified, p.viewed, p.state, ll.is_partner, TRIM(LEADING ',' FROM (IF(ld.city IS NOT NULL AND ld.name IS NOT NULL, CONCAT(ld.city,', ',ld.name),IF(ld.city IS NOT NULL, ld.city, ld.name))))  as location_name, pp.code as curr_code,  pp.price  AS price, (SELECT SUM(ps.quantity) FROM " . DB_PREFIX . "product_stock ps WHERE ps.product_id = ops.product_id AND ps.location_id = ops.location_id ) AS quantity, (SELECT date_format(MIN(ps.dateupdate),'%d.%m.%Y') FROM " . DB_PREFIX . "product_stock ps WHERE ps.product_id = ops.product_id AND ps.location_id = ops.location_id ) AS dateupdate, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating, (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special, (SELECT points FROM " . DB_PREFIX . "product_reward pr WHERE pr.product_id = p.product_id AND pr.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "') AS reward, (SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review r2 WHERE r2.product_id = p.product_id AND r2.status = '1' GROUP BY r2.product_id) AS reviews, IF((ll.is_partner = 1 AND ll.shipping_term IS NOT NULL), CONCAT(ll.shipping_term,  '" . ((int)$this->config->get('config_language_id') == '1' ? " дн." : " дн.") . "'),  (SELECT ss.name FROM " . DB_PREFIX . "stock_status ss WHERE ss.stock_status_id = p.stock_status_id AND ss.language_id = '" . (int)$this->config->get('config_language_id') . "')) AS stock_status  ";
		$sql .= " FROM (SELECT p2.* FROM " . DB_PREFIX . "product p1 INNER JOIN  " . DB_PREFIX . "product p2 ON (p1.analog_group = p2.analog_group AND p1.product_id = '" . (int)$product_id . "') WHERE p2.product_id = '" . (int)$product_id . "' OR  p1.analog_group <> 0 ) p  ";
    $sql .= " INNER JOIN  " . DB_PREFIX . "product_price pp ON( pp.product_id = p.product_id AND pp.price > 0  AND pp.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "') ";
		$sql .= " LEFT JOIN  " . DB_PREFIX . "product_stock ops ON ops.product_id = p.product_id AND ops.location_id = pp.location_id LEFT JOIN " . DB_PREFIX . "location ll ON (ll.location_id = ops.location_id) LEFT JOIN " . DB_PREFIX . "location_description ld ON ld.location_id = ll.location_id  LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id  AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' ) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) ";
		$sql .= " WHERE p.status = '1'  AND IFNULL(ld.language_id,'" . (int)$this->config->get('config_language_id') . "') = '" . (int)$this->config->get('config_language_id') . "' AND IFNULL(pd.language_id,'" . (int)$this->config->get('config_language_id') . "') = '" . (int)$this->config->get('config_language_id') . "' AND p.date_available <= NOW() ";
    $sql = "SELECT p.* FROM (" . $sql . ") p ";
		$sql .= " ORDER BY IF(p.product_id = '" . (int)$product_id . "' and p.quantity > 0 and is_partner=0, 10, IF(p.product_id = '" . (int)$product_id . "' and p.quantity > 0 and is_partner=1, 20, IF(p.product_id != '" . (int)$product_id . "' and p.quantity > 0 and is_partner=0, 30, IF(p.product_id != '" . (int)$product_id . "' and p.quantity > 0 and is_partner=1, 40, 100))))";
if(catalog_model_catalog_product__getProductQuantities){
$log = fopen(DIR_LOGS . 'catalog_model_catalog_product__getProductQuantities.log', 'a');
fwrite($log,' $sql )' . print_r($sql, true) . '==' . chr(10) . chr(13));
fclose($log);}
		
		// $query = $this->db->query($sql);
		// return $query->rows;
		$product_data = array();

		$query = $this->db->query($sql);
if(catalog_model_catalog_product__getProductQuantities){
$log = fopen(DIR_LOGS . 'catalog_model_catalog_product__getProductQuantities.log', 'a');
fwrite($log,' $query->rows )' . print_r($query->rows, true) . '==' . chr(10) . chr(13));
fclose($log);}

		foreach ($query->rows as $result) {
			if (empty($result['quantity'])) {
				$stock = $result['stock_status'];
			} elseif ($result['quantity'] <= 0) {
				$stock = $result['stock_status'];
			} elseif ($this->config->get('config_stock_display')) {
				$stock = $result['quantity'];
			} else {
				$stock = $result['is_partner'] == '1' ? $result['stock_status'] . ' ' : $this->language->get('text_instock');
			}
      if(!empty($result['curr_code']))
        $pcur_value = $this->currency->getValue($result['curr_code']);
      else
        $pcur_value = $this->currency->getValue($this->config->get('config_product_currency'));
			
			$product_data[] =  array(
				'product_id'       => $result['product_id'],
				'name'             => $result['name'],
				'description'      => $result['description'],
				'meta_title'       => $result['meta_title'],
				'noindex'          => $result['noindex'],
				'meta_h1'	         => $result['meta_h1'],
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword'],
				'tag'              => $result['tag'],
				'model'            => $result['model'],
				'sku'              => $result['sku'],
				'upc'              => $result['upc'],
				'ean'              => $result['ean'],
				'jan'              => $result['jan'],
				'isbn'             => $result['isbn'],
				'mpn'              => $result['mpn'],
				'location'         => $result['location'],
				'quantity'         => $result['quantity'],
				'stock_status'     => $stock,
				'image'            => $result['image'],
				'manufacturer_id'  => $result['manufacturer_id'],
				'manufacturer'     => $result['manufacturer'],
				'price'            => ($result['discount'] ? $result['discount'] : (empty($result['price']) ? 0 : $result['price'])) / $pcur_value,
				'special'          => $result['special'] / $pcur_value,
				'reward'           => $result['reward'],
				'points'           => $result['points'],
				'tax_class_id'     => $result['tax_class_id'],
				'date_available'   => $result['date_available'],
				'weight'           => $result['weight'],
				'weight_class_id'  => $result['weight_class_id'],
				'length'           => $result['length'],
				'width'            => $result['width'],
				'height'           => $result['height'],
				'length_class_id'  => $result['length_class_id'],
				'subtract'         => $result['subtract'],
				'rating'           => round($result['rating']),
				'reviews'          => $result['reviews'] ? $result['reviews'] : 0,
				'minimum'          => $result['minimum'],
				'sort_order'       => $result['sort_order'],
				'status'           => $result['status'],
				'date_added'       => $result['date_added'],
				'date_modified'    => $result['date_modified'],
				'dateupdate'       => $result['dateupdate'],
				'location_id'      => $result['location_id'],
				'location_name'    => $result['location_name'],
				'viewed'           => $result['viewed'],
				'state'            => $result['state'],
        'is_partner'       => $result['is_partner'],
        'hasstock'         => ($result['stock_status'] == $stock ? '0' : '1' )
			); 
		} 

if(catalog_model_catalog_product__getProductQuantities){
$log = fopen(DIR_LOGS . 'catalog_model_catalog_product__getProductQuantities.log', 'a');
fwrite($log,' RETURN $product_data )' . print_r($product_data, true) . '==' . chr(10) . chr(13));
fclose($log);}
		return $product_data;
		
	}
}

<?php
class ModelCatalogPartnerprice extends Model {
  private $stock_lot = 0;
  private $location_id = 0;

  public function getCurrencies(){
    $sql = "SELECT l.location_id as id, l.currency_code as code, l.name as title, l.partner_module, ROUND(1/IF(IFNULL(c.value,1) = 0, 0, IFNULL(c.value,1)),2) as curr_change, l.category_id, cd.name as ctgrname, (SELECT COUNT(*) FROM " . DB_PREFIX . "partner_price pp WHERE pp.location_id = l.location_id ) as import_count, (SELECT COUNT(*) FROM " . DB_PREFIX . "partner_price pp WHERE pp.location_id = l.location_id AND pp.product_id > 0 ) as partner_compare_product_count, (SELECT COUNT(*) FROM " . DB_PREFIX . "partner_price pp WHERE pp.location_id = l.location_id AND pp.applied > 0 ) as partner_set_extra_count  FROM " . DB_PREFIX . "currency c INNER JOIN " . DB_PREFIX . "location l ON l.currency_code = c.code LEFT JOIN " . DB_PREFIX . "category_description cd ON cd.category_id = l.category_id AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
if(admin_model_catalog_partnerprice__getCurrencies){
$log = fopen(DIR_LOGS . 'admin_model_catalog_partnerprice__getCurrencies.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,' $sql )' . print_r($sql, true) . '==' . chr(10) . chr(13));
fclose($log);}
    $query = $this->db->query($sql);
		return $query->rows;
  }

  public function getCurrency2Location($currency_code){
    $res = '';
    $sql = "SELECT l.location_id as id, l.currency_code as code, l.name as title FROM " . DB_PREFIX . "currency c INNER JOIN " . DB_PREFIX . "location l ON l.currency_code = c.code WHERE l.currency_code = '" . $currency_code . "'";
if(admin_model_catalog_partnerprice__getCurrency2Local){
$log = fopen(DIR_LOGS . 'admin_model_catalog_partnerprice__getCurrency2Local', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,' $sql )' . print_r($sql, true) . '==' . chr(10) . chr(13));
fclose($log);}
    $query = $this->db->query($sql);
		if($query->num_rows){ 
		  return $query->row['id'];
    }
  }

  public function getLocation2Currency($location_id){
    $res = 0;
    $sql = "SELECT l.location_id as id, l.currency_code as code, l.name as title FROM " . DB_PREFIX . "currency c INNER JOIN " . DB_PREFIX . "location l ON l.currency_code = c.code WHERE l.location_id = '" . (int)$location_id . "'";
if(admin_model_catalog_partnerprice__getLocation2Currency){
$log = fopen(DIR_LOGS . 'admin_model_catalog_partnerprice__getLocation2Currency', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,' $sql )' . print_r($sql, true) . '==' . chr(10) . chr(13));
fclose($log);}
    $query = $this->db->query($sql);
		if($query->num_rows){ 
		  return $query->row['code'];
    } else return false;
  }

  public function getstock_lot($location_id){
    $res = 0;
    $sql = "SELECT l.stock_lot FROM " . DB_PREFIX . "currency c INNER JOIN " . DB_PREFIX . "location l ON l.currency_code = c.code WHERE l.location_id = '" . (int)$location_id . "'";
    $query = $this->db->query($sql);
		if($query->num_rows){ 
		  return $query->row['stock_lot'];
    } else return 0;
  }


  public function getpartner_module($location_id){
    $sql = "SELECT l.partner_module FROM " . DB_PREFIX . "location l WHERE l.location_id = '" . (int)$location_id . "'";
    $query = $this->db->query($sql);
		if($query->num_rows){ 
		  return $query->row['partner_module'];
    } else return false;
  }

  public function getpartnerprice_list($location_id, $data = array()){
    $sql = "SELECT pp.partnerprice_id, pp.product_name, pp.vendor_code_raw, pp.product_brand, pp.quantity, pp.price, IFNULL(pp.product_id,1) ctgr_count  FROM " . DB_PREFIX . "location l INNER JOIN " . DB_PREFIX . "partner_price pp ON pp.location_id = l.location_id WHERE l.location_id = '" . (int)$location_id . "' AND (pp.product_id IS null OR pp.product_id = -1) ";

		$sort_data = array(
      'partnerprice_id',
      'product_name',
			'vendor_code_raw',
			'product_brand',
      'quantity', 'price'
		);
    

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY partnerprice_id";
		}


		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
    } else { $sql .= " LIMIT 1,25 "; }
    $query = $this->db->query($sql);
		if($query->num_rows){ 
		  return $query->rows;
    } else return false;
  }

  public function getpartnerprice_total($location_id, $data = array()){
    $sql = "SELECT COUNT(*) cnt FROM " . DB_PREFIX . "location l INNER JOIN " . DB_PREFIX . "partner_price pp ON pp.location_id = l.location_id WHERE l.location_id = '" . (int)$location_id . "' AND (pp.product_id IS null OR pp.product_id = -1) ";

    $query = $this->db->query($sql);
		return $query->row['cnt']; 
  }



	public function mystorePumppriceIntoDatabase( $pumpprice_array ) {
if(admin_model_extension_export_import__uploadpumpprice){
$log = fopen(DIR_LOGS . 'admin_model_extension_export_import__uploadpumpprice.log', 'a');
fwrite($log,'>> ================================== ' . '==' . chr(10) . chr(13));
fwrite($log,'>> $pumpprice_array) ' . print_r($pumpprice_array,true) . '==' . chr(10) . chr(13));
fclose($log);}

	 // $currency, $model, $price, $name, $quantity, $pump_id 
		$currency_code = $pumpprice_array['currency'];
	  $vendor_code_raw = $this->db->escape($pumpprice_array['model']);
    //“/”; “\”; “-”; “_”; “:”;“ ”; “.”; “,”
    $vendor_code = addslashes(str_replace(' ','', str_replace(',','', str_replace('.','', str_replace(';','', str_replace(':','', str_replace('_','', str_replace('-','',str_replace('\\','',(str_replace('/','',$vendor_code_raw)))))))))));
		$manufacture = addslashes((empty($pumpprice_array['manufacture']) ? '' : $pumpprice_array['manufacture']));
    $manufacture = addslashes(str_replace(' ','', str_replace(',','', str_replace('.','', str_replace(';','', str_replace(':','', str_replace('_','', str_replace('-','',str_replace('\\','',(str_replace('/','',$manufacture)))))))))));
		$automanufacture = addslashes((empty($pumpprice_array['automanufacture']) ? '' : $pumpprice_array['automanufacture']));
		$automanufacture_number = (empty($pumpprice_array['automanufacture_number']) ? '' : $pumpprice_array['automanufacture_number']);
    
		$price = str_replace(',','.',$pumpprice_array['price']);
		$quantities = (empty($pumpprice_array['quantity']) ? '' : $pumpprice_array['quantity']);
if(admin_model_extension_export_import__uploadpumpprice){
$log = fopen(DIR_LOGS . 'admin_model_extension_export_import__uploadpumpprice.log', 'a');
fwrite($log,'>> ================================== ' . '==' . chr(10) . chr(13));
fwrite($log,'>>1 $quantities) ' . print_r($quantities,true) . '==' . chr(10) . chr(13));
fclose($log);}
    $quantities = explode('+',$quantities);
if(admin_model_extension_export_import__uploadpumpprice){
$log = fopen(DIR_LOGS . 'admin_model_extension_export_import__uploadpumpprice.log', 'a');
fwrite($log,'>> ================================== ' . '==' . chr(10) . chr(13));
fwrite($log,'>>2 $quantities) ' . print_r($quantities,true) . '==' . chr(10) . chr(13));
fclose($log);}
    $quant = '';
    foreach($quantities as $quantity){
      if(!empty($quantity) && strpos($quantity, '>') !== false){
        if($this->location_id != $pumpprice_array['location_id'] || $this->stock_lot == 0){
          $this->stock_lot = $this->getstock_lot($pumpprice_array['location_id']);
          $this->location_id = $pumpprice_array['location_id']; 
if(admin_model_extension_export_import__uploadpumpprice){
$log = fopen(DIR_LOGS . 'admin_model_extension_export_import__uploadpumpprice.log', 'a');
fwrite($log,'>> ================================== ' . '==' . chr(10) . chr(13));
fwrite($log,'>>$this->stock_lot) ' . print_r($this->stock_lot,true) . '==' . chr(10) . chr(13));
fwrite($log,'>>$this->location_id) ' . print_r($this->location_id,true) . '==' . chr(10) . chr(13));
fclose($log);}
        }
        $quant = empty($quant) ? (int)$this->stock_lot : (int)$quant+(int)$this->stock_lot ;
      } else {$quant = empty($quant) ? (int)$quantity : (int)$quant+(int)$quantity; }
if(admin_model_extension_export_import__uploadpumpprice){
$log = fopen(DIR_LOGS . 'admin_model_extension_export_import__uploadpumpprice.log', 'a');
fwrite($log,'>> ================================== ' . '==' . chr(10) . chr(13));
fwrite($log,'>> $quant) ' . print_r($quant,true) . '==' . chr(10) . chr(13));
fclose($log);}
    }
    if(!isset($quant)) 
      $quant = '';
		$name = $pumpprice_array['name'];
		if (!isset($name)) $name = '';
		$name = $name == '' ? '' : $this->db->escape($name);
    $name = addslashes($name);
//		$quantity = $this->IsInteger(($quantity == '' ? 0 : $quantity)) ? $quantity : 0;
		if (!isset($pump_id) || ($pump_id == 0)) {
  		  $sql  = "INSERT INTO `".DB_PREFIX."partner_price` (`location_id`, `product_name`, `vendor_code`, `vendor_code_raw`, `product_brand`,  `oem_code`, `oem_brand`, `quantity`, `price`, `currency_code`, `product_id`, `dateadd`, `applied`, `dateupdate`, `user_id_add`) VALUES ";
		  $sql .= "(" . (empty($pumpprice_array['location_id']) ? " NULL, " : " '" . (int)$pumpprice_array['location_id'] . "', ") . (empty($name) ? " NULL, " : "'" . $name . "', ") . (empty($vendor_code) ? " NULL, " : "'" . $vendor_code . "', ") . (empty($vendor_code_raw) ? " NULL, " : "'" . $vendor_code_raw . "', ") . (empty($manufacture) ? " NULL, " : "'" . $manufacture . "', ") . (empty($automanufacture_number) ? " NULL, " : "'" . $automanufacture_number . "', ") . (empty($automanufacture) ? " NULL, " : "'" . $automanufacture . "', ") . (empty($quant) ? " NULL, " : "'" . (int)$quant . "', ") . $price . ", '" . $currency_code . "', NULL, NOW(), 0, NULL, '" . $this->user->getid() . "' );";
if(admin_model_extension_export_import__uploadpumpprice){
$log = fopen(DIR_LOGS . 'admin_model_extension_export_import__uploadpumpprice.log', 'a');
fwrite($log,'>> $sql) ' . print_r($sql,true) . '==' . chr(10) . chr(13));
fclose($log);}
		  $this->db->query( $sql );
		  $lastId = $this->db->getLastId();
		} else {
		  $lastId = $pump_id;
		}
   return  $lastId;
	}

  public function updateStatus4Double($location_id){
    return true;
    $sql = "SELECT opp.partnerprice_id FROM `".DB_PREFIX."partner_price` opp WHERE (location_id, vendor_code, product_brand, oem_code, oem_brand) IN ( SELECT opp.location_id, opp.vendor_code, opp.product_brand, opp.oem_code, opp.oem_brand FROM `".DB_PREFIX."partner_price` opp WHERE opp.location_id = '" . (int)$location_id . "' GROUP BY opp.location_id, opp.vendor_code, opp.product_brand, opp.oem_code, opp.oem_brand HAVING COUNT(*) > 1)";
if(admin_model_catalog_partnerprice__updateStatus4Double){
$log = fopen(DIR_LOGS . 'admin_model_catalog_partnerprice__updateStatus4Double.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,' $sql )' . print_r($sql, true) . '==' . chr(10) . chr(13));
fclose($log);}
    $query = $this->db->query($sql);
		if($query->num_rows){
		  foreach($query->rows as $list_id){
		    $sql = "UPDATE `".DB_PREFIX."partner_price` SET applied = 1 WHERE partnerprice_id = '" . (int)$list_id['partnerprice_id'] . "'";
        $this->db->query($sql);
		  }
		}
  }

  public function deletedoubleprices($location_id){
    $sql = "UPDATE `".DB_PREFIX."partner_price` opp INNER JOIN (SELECT opp.location_id, opp.product_brand, opp.vendor_code, COUNT(*), MIN(price) price, MIN(opp.quantity) quantity, MIN(opp.partnerprice_id) partn_id FROM `".DB_PREFIX."partner_price` opp WHERE opp.location_id = '".(int)$location_id."' GROUP BY opp.location_id, opp.product_brand, opp.vendor_code HAVING COUNT(*) = 1) dbl ON dbl.location_id = opp.location_id AND dbl.product_brand = opp.product_brand AND dbl.vendor_code = opp.vendor_code SET opp.price = dbl.price, opp.quantity = dbl.quantity ";
if(admin_model_catalog_partnerprice__deletedoubleprices){
$log = fopen(DIR_LOGS . 'admin_model_catalog_partnerprice__deletedoubleprices.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '====== deletedoubleprices == ' . chr(10) . chr(13));
fwrite($log,' $sql )' . print_r($sql, true) . '==' . chr(10) . chr(13));
fclose($log);}
    
    $query = $this->db->query($sql);

    $sql = "DELETE opp FROM `".DB_PREFIX."partner_price` opp INNER JOIN (SELECT opp.location_id, opp.product_brand, opp.vendor_code, COUNT(*), MIN(price) price, MIN(opp.quantity) quantity, MIN(opp.partnerprice_id) partn_id FROM `".DB_PREFIX."partner_price` opp WHERE opp.location_id = '".(int)$location_id."' GROUP BY opp.location_id, opp.product_brand, opp.vendor_code HAVING COUNT(*) > 1) dbl ON dbl.location_id = opp.location_id AND dbl.product_brand = opp.product_brand AND dbl.vendor_code = opp.vendor_code WHERE opp.partnerprice_id <> dbl.partn_id";
if(admin_model_catalog_partnerprice__deletedoubleprices){
$log = fopen(DIR_LOGS . 'admin_model_catalog_partnerprice__deletedoubleprices.log', 'a');
fwrite($log,' ' . '====== deletedoubleprices == ' . chr(10) . chr(13));
fwrite($log,' $sql )' . print_r($sql, true) . '==' . chr(10) . chr(13));
fclose($log);}
    $this->db->query($sql);
    $res = $this->db->countAffected();
if(admin_model_catalog_partnerprice__deletedoubleprices){
$log = fopen(DIR_LOGS . 'admin_model_catalog_partnerprice__deletedoubleprices.log', 'a');
fwrite($log,' ' . '====== deletedoubleprices == ' . chr(10) . chr(13));
fwrite($log,' $res = $this->db->countAffected(); )' . print_r($res, true) . '==' . chr(10) . chr(13));
fclose($log);}
    return $res;
  }



  public function partner_compare_product($location_id){
    $sql = "CALL partner_compare_product('" . (int)$location_id . "')";
if(admin_model_catalog_partnerprice__partner_compare_product){
$log = fopen(DIR_LOGS . 'admin_model_catalog_partnerprice__partner_compare_product.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,' $sql )' . print_r($sql, true) . '==' . chr(10) . chr(13));
fclose($log);}
    $query = $this->db->query($sql);
//		return $query->rows;
    return true;
  }



  public function partner_set_extra($location_id){
    $sql = "CALL partner_set_extra('" . (int)$location_id . "')";
if(admin_model_catalog_partnerprice__partner_set_extra){
$log = fopen(DIR_LOGS . 'admin_model_catalog_partnerprice__partner_set_extra.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,' $sql )' . print_r($sql, true) . '==' . chr(10) . chr(13));
fclose($log);}
    $query = $this->db->query($sql);
//		return $query->rows;
    return true; 
  }

	
}

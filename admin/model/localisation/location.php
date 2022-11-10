<?php
class ModelLocalisationLocation extends Model {
	public function addLocation($data) {
if(admin_model_localisation_location__editLocation){
$log = fopen(DIR_LOGS . 'admin_model_localisation_location__addLocation.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,' $data )' . print_r($data, true) . '==' . chr(10) . chr(13));
fclose($log);}

    $sql = "INSERT INTO " . DB_PREFIX . "location SET name = '" . $this->db->escape($data['name']) . "', address = '" . $this->db->escape($data['address']) . "', geocode = '" . $this->db->escape($data['geocode']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', image = '" . $this->db->escape($data['image']) . "', open = '" . $this->db->escape($data['open']) . "', comment = '" . $this->db->escape($data['comment']) . "', parent_id = '" . (int)$data['parent_id'] . "', `order` = '" . (int)$data['order'] . "', google_map_iframe_src = '" . $this->db->escape($data['google_map_iframe_src']) . "', viber = '" . $this->db->escape($data['viber']) . "', telegram = '" . $this->db->escape($data['telegram']) . "', whatsapp = '" . $this->db->escape($data['whatsapp']) . "', skype = '" . $this->db->escape($data['skype']) . "', youtube_id = '" . $this->db->escape($data['youtube_id']) . "', email = '" . $this->db->escape($data['email']) . "', currency_code = '" . $this->db->escape($data['currency_code']) . "', shipping_term = '" . $this->db->escape($data['shipping_term']) . "', is_partner = '" . (int)$data['is_partner'] . "', category_id = '" . (int)$data['category_id'] . "', stock_lot = '" . (int)$data['stock_lot'] . "', status = '" . (int)$data['status'] . "'";
    
if(admin_model_localisation_location__editLocation){
$log = fopen(DIR_LOGS . 'admin_model_localisation_location__addLocation.log', 'a');
fwrite($log,' $sql )' . print_r($sql, true) . '==' . chr(10) . chr(13));
fclose($log);}


		$this->db->query($sql);
	
		$location_id = $this->db->getLastId();
if(admin_model_localisation_location__editLocation){
$log = fopen(DIR_LOGS . 'admin_model_localisation_location__addLocation.log', 'a');
fwrite($log,' $location_id )' . print_r($location_id, true) . '==' . chr(10) . chr(13));
fclose($log);}

		foreach ($data['location_description'] as $language_id => $value) {
		  $sql = "INSERT INTO " . DB_PREFIX . "location_description SET location_id = '" . (int)$location_id . "', language_id = '" . (int)$language_id . "', address = '" . $this->db->escape($value['address']) . "', city = '" . $this->db->escape($value['city']) . "', comment = '" . $this->db->escape($value['comment']) . "', schedule = '" . $this->db->escape($value['schedule']) . "', name = '" . $this->db->escape($value['name']) . "', location_type = '" . $this->db->escape($value['location_type']) . "', email = '" . $this->db->escape($value['email']) . "'";
    
if(admin_model_localisation_location__editLocation){
$log = fopen(DIR_LOGS . 'admin_model_localisation_location__addLocation.log', 'a');
fwrite($log,' $sql )' . print_r($sql, true) . '==' . chr(10) . chr(13));
fclose($log);}

			$this->db->query($sql);
		}
		
		return $location_id;
	}

	public function editLocation($location_id, $data) {
if(admin_model_localisation_location__editLocation){
$log = fopen(DIR_LOGS . 'admin_model_localisation_location__editLocation.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,' $location_id )' . print_r($location_id, true) . '==' . chr(10) . chr(13));
fwrite($log,' $data )' . print_r($data, true) . '==' . chr(10) . chr(13));
fclose($log);}
		$sql = "UPDATE " . DB_PREFIX . "location SET name = '" . $this->db->escape($data['name']) . "', address = '" . $this->db->escape($data['address']) . "', geocode = '" . $this->db->escape($data['geocode']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', image = '" . $this->db->escape($data['image']) . "', open = '" . $this->db->escape($data['open']) . "', comment = '" . $this->db->escape($data['comment']) . "', parent_id = '" . (int)$data['parent_id'] . "', `order` = '" . (int)$data['order'] . "', google_map_iframe_src = '" . $this->db->escape($data['google_map_iframe_src']) . "', viber = '" . $this->db->escape($data['viber']) . "', telegram = '" . $this->db->escape($data['telegram']) . "', whatsapp = '" . $this->db->escape($data['whatsapp']) . "', skype = '" . $this->db->escape($data['skype']) . "', youtube_id = '" . $this->db->escape($data['youtube_id']) . "', email = '" . $this->db->escape($data['email']) . "', currency_code = '" . $this->db->escape($data['currency_code']) . "', shipping_term = '" . $this->db->escape($data['shipping_term']) . "', is_partner = '" . (int)$data['is_partner'] . "', category_id = '" . (int)$data['category_id'] . "', stock_lot = '" . (int)$data['stock_lot'] . "', status = '" . (int)$data['status'] . "' WHERE location_id = '" . (int)$location_id . "'";

if(admin_model_localisation_location__editLocation){
$log = fopen(DIR_LOGS . 'admin_model_localisation_location__editLocation.log', 'a');
fwrite($log,' $sql )' . print_r($sql, true) . '==' . chr(10) . chr(13));
fclose($log);}

		$this->db->query($sql);

    $sql = "DELETE FROM " . DB_PREFIX . "location_description WHERE location_id = '" . (int)$location_id . "'";
if(admin_model_localisation_location__editLocation){
$log = fopen(DIR_LOGS . 'admin_model_localisation_location__editLocation.log', 'a');
fwrite($log,' $sql )' . print_r($sql, true) . '==' . chr(10) . chr(13));
fclose($log);}

		$this->db->query($sql);

		foreach ($data['location_description'] as $language_id => $value) {
		  $sql = "INSERT INTO " . DB_PREFIX . "location_description SET location_id = '" . (int)$location_id . "', language_id = '" . (int)$language_id . "', address = '" . $this->db->escape($value['address']) . "', city = '" . $this->db->escape($value['city']) . "', comment = '" . $this->db->escape($value['comment']) . "', schedule = '" . $this->db->escape($value['schedule']) . "', name = '" . $this->db->escape($value['name']) . "', location_type = '" . $this->db->escape($value['location_type']) . "', email = '" . $this->db->escape($value['email']) . "'";
if(admin_model_localisation_location__editLocation){
$log = fopen(DIR_LOGS . 'admin_model_localisation_location__editLocation.log', 'a');
fwrite($log,' $sql )' . print_r($sql, true) . '==' . chr(10) . chr(13));
fclose($log);}
			$this->db->query($sql);
		}
	}

	public function deleteLocation($location_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "location_description WHERE location_id = '" . (int)$location_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "location WHERE location_id = " . (int)$location_id);
	}

	public function getLocation($location_id) {
	   $sql = "SELECT DISTINCT l.*, ( SELECT CONCAT_WS('&nbsp;&nbsp;&gt;&nbsp;&nbsp;', ocd3.name, ocd2.name, ocd1.name, ocd.NAME) FROM " . DB_PREFIX . "location oc3 LEFT JOIN  " . DB_PREFIX . "location_description ocd3 ON oc3.location_id = ocd3.location_id LEFT JOIN  " . DB_PREFIX . "location oc2 ON oc2.location_id = oc3.parent_id LEFT JOIN  " . DB_PREFIX . "location_description ocd2 ON oc2.location_id = ocd2.location_id LEFT JOIN  " . DB_PREFIX . "location oc1 ON oc1.location_id = oc2.parent_id LEFT JOIN  " . DB_PREFIX . "location_description ocd1 ON oc1.location_id = ocd1.location_id LEFT JOIN  " . DB_PREFIX . "location oc ON oc.location_id = oc1.parent_id LEFT JOIN  " . DB_PREFIX . "location_description ocd ON oc.location_id = ocd.location_id WHERE IFNULL(ocd.language_id,'" . (int)$this->config->get('config_language_id') . "') = '" . (int)$this->config->get('config_language_id') . "' AND IFNULL(ocd1.language_id,'" . (int)$this->config->get('config_language_id') . "') = '" . (int)$this->config->get('config_language_id') . "' AND IFNULL(ocd2.language_id,'" . (int)$this->config->get('config_language_id') . "') = '" . (int)$this->config->get('config_language_id') . "'  AND IFNULL(ocd3.language_id,'" . (int)$this->config->get('config_language_id') . "') = '" . (int)$this->config->get('config_language_id') . "'  AND oc3.location_id = '" . (int)$location_id . "') AS path, (SELECT cd.name FROM " . DB_PREFIX . "category_description cd WHERE cd.category_id = l.category_id AND IFNULL(cd.language_id,'" . (int)$this->config->get('config_language_id') . "') = '" . (int)$this->config->get('config_language_id') . "' ) AS category FROM " . DB_PREFIX . "location l WHERE l.location_id = '" . (int)$location_id . "'";

if(admin_model_localisation_location__getLocation){     
$log = fopen(DIR_LOGS . 'admin_model_localisation_location__getLocation.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,' $location_id )' . print_r($location_id, true) . '==' . chr(10) . chr(13));
fwrite($log,' $sql  )' . print_r($sql, true)  . '==' . chr(10) . chr(13));
fclose($log);}
	 
		$query = $this->db->query($sql);

		return $query->row;
	}

	public function getLocations($data = array()) {
		$sql = "SELECT l.location_id, IFNULL(ld.name,l.name) name, IFNULL(ld.address,l.address) address FROM " . DB_PREFIX . "location l LEFT JOIN " . DB_PREFIX . "location_description ld on l.location_id = ld.location_id WHERE IFNULL(ld.language_id,'" . (int)$this->config->get('config_language_id') . "') = '" . (int)$this->config->get('config_language_id') . "' ";

		if (!empty($data['filter_name'])) {
			$sql .= " AND ld.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}
		if (isset($data['is_partner']) && !empty((int)$data['is_partner'])) {
			$sql .= " AND l.is_partner = '" . (int)$data['is_partner'] . "'";
		}


		$sort_data = array(
			'name',
			'address',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . (($sort_data == 'name') ? "IFNULL(ld.name,l.name)" : "ld." . $data['sort']);
		} else {
			$sql .= " ORDER BY IFNULL(ld.name,l.name)";
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
		}
if(admin_model_localisation_location__getLocations){
$log = fopen(DIR_LOGS . 'admin_model_localisation_location__getLocations.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,' $data )' . print_r($data, true) . '==' . chr(10) . chr(13));
fwrite($log,' $sql  )' . print_r($sql, true)  . '==' . chr(10) . chr(13));
fclose($log);}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalLocations() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "location");

		return $query->row['total'];
	}

	public function getCurrencies() {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "currency ");
		return $query->rows;
	}



	public function getLocationDescriptions($location_id) {
		$location_description_data = array();
// SELECT old.location_id, old.language_id, old.address, old.city, old.comment, old.schedule, old.name, old.location_type, old.email FROM oc_location_description old
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "location_description WHERE location_id = '" . (int)$location_id . "'");

		foreach ($query->rows as $result) {
			$location_description_data[$result['language_id']] = array(
				'address'       => htmlspecialchars(html_entity_decode ($result['address'],ENT_COMPAT)),
				'city'       	=> htmlspecialchars(html_entity_decode ($result['city'],ENT_COMPAT)),
				'comment'      	=> htmlspecialchars(html_entity_decode ($result['comment'],ENT_HTML5)),
				'schedule' 		=> htmlspecialchars(html_entity_decode ($result['schedule'],ENT_HTML5)),
				'name'     		=> htmlspecialchars(html_entity_decode ($result['name'],ENT_QUOTES)),
				'location_type' => $result['location_type'],
				'email'      	=> htmlspecialchars(html_entity_decode ($result['email'],ENT_HTML5))
			);
		}

		return $location_description_data;
	}
	
}

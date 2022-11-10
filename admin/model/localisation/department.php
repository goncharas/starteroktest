<?php
class ModelLocalisationDepartment extends Model {
	public function addDepartment($data) {
$log = fopen(DIR_LOGS . 'admin_model_localisation_department__addDepartment.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,' $data )' . print_r($data, true) . '==' . chr(10) . chr(13));
fclose($log);
		$sql = "INSERT INTO " . DB_PREFIX . "department SET location_id = '" . (int)$data['parent_id'] . "', `order` = '" . (int)$data['order'] . "'";
$log = fopen(DIR_LOGS . 'admin_model_localisation_department__addDepartment.log', 'a');
fwrite($log,'1. $sql )' . print_r($sql, true) . '==' . chr(10) . chr(13));
fclose($log);
		$this->db->query($sql);
	
		$department_id = $this->db->getLastId();

		foreach ($data['department_description'] as $language_id => $value) {
			$sql = "INSERT INTO " . DB_PREFIX . "department_description SET department_id = '" . (int)$department_id . "', language_id = '" . (int)$language_id . "', schedule = '" . $this->db->escape($value['schedule']) . "', name = '" . $this->db->escape($value['name']) . "'";
$log = fopen(DIR_LOGS . 'admin_model_localisation_department__addDepartment.log', 'a');
fwrite($log,'2. $sql )' . print_r($sql, true) . '==' . chr(10) . chr(13));
fclose($log);
			$this->db->query($sql);
		}
$log = fopen(DIR_LOGS . 'admin_model_localisation_department__addDepartment.log', 'a');
fwrite($log,'RETURN $department_id )' . print_r($department_id, true) . '==' . chr(10) . chr(13));
fclose($log);

		return $department_id;
	}

	public function editDepartment($department_id, $data) {
$log = fopen(DIR_LOGS . 'admin_model_localisation_department__editDepartment.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,' $department_id )' . print_r($department_id, true) . '==' . chr(10) . chr(13));
fwrite($log,' $data )' . print_r($data, true) . '==' . chr(10) . chr(13));
fclose($log);
		
		$this->db->query("UPDATE " . DB_PREFIX . "department SET location_id = '" . (int)$data['parent_id'] . "', `order` = '" . (int)$data['order'] . "' WHERE department_id = '" . (int)$department_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "department_description WHERE department_id = '" . (int)$department_id . "'");

		foreach ($data['department_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "department_description SET department_id = '" . (int)$department_id . "', language_id = '" . (int)$language_id . "', schedule = '" . $this->db->escape($value['schedule']) . "', name = '" . $this->db->escape($value['name']) . "'");
		}
	}

	public function deleteDepartment($department_id) {
		$sql = "DELETE FROM " . DB_PREFIX . "department_description WHERE department_id = '" . (int)$department_id . "'";
$log = fopen(DIR_LOGS . 'admin_model_localisation_department__deleteDepartment.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,' $department_id )' . print_r($department_id, true) . '==' . chr(10) . chr(13));
fwrite($log,' 1. $sql )' . print_r($data, true) . '==' . chr(10) . chr(13));
fclose($log);
		$this->db->query($sql);
		$sql = "DELETE FROM " . DB_PREFIX . "department WHERE department_id = " . (int)$department_id;
$log = fopen(DIR_LOGS . 'admin_model_localisation_department__deleteDepartment.log', 'a');
fwrite($log,' 2. $sql )' . print_r($data, true) . '==' . chr(10) . chr(13));
fclose($log);
		$this->db->query($sql);
	}

	public function getDepartment($department_id) {
		$query = $this->db->query("SELECT DISTINCT d.*, ( SELECT ocd.NAME FROM " . DB_PREFIX . "location oc LEFT JOIN  " . DB_PREFIX . "location_description ocd ON oc.location_id = ocd.location_id WHERE IFNULL(ocd.language_id,'" . (int)$this->config->get('config_language_id') . "') = '" . (int)$this->config->get('config_language_id') . "' AND oc.location_id = d.location_id) AS path FROM " . DB_PREFIX . "department d WHERE d.department_id = '" . (int)$department_id . "'");

		return $query->row;
	}

	public function getDepartments($data = array()) {
		$sql = "SELECT d.department_id, dd.name, dd.schedule, d.location_id, ( SELECT ocd.NAME FROM " . DB_PREFIX . "location oc LEFT JOIN  " . DB_PREFIX . "location_description ocd ON oc.location_id = ocd.location_id WHERE IFNULL(ocd.language_id,'" . (int)$this->config->get('config_language_id') . "') = '" . (int)$this->config->get('config_language_id') . "' AND oc.location_id = d.location_id) AS path FROM " . DB_PREFIX . "department d INNER JOIN " . DB_PREFIX . "department_description dd on d.department_id = dd.department_id WHERE dd.language_id = '" . (int)$this->config->get('config_language_id') . "' ";
$log = fopen(DIR_LOGS . 'admin_model_localisation_department__getDepartments.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,' $data )' . print_r($data, true) . '==' . chr(10) . chr(13));
fwrite($log,' $sql  )' . print_r($sql, true)  . '==' . chr(10) . chr(13));
fclose($log);

		if (!empty($data['filter_name'])) {
			$sql .= " AND dd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}
$log = fopen(DIR_LOGS . 'admin_model_localisation_department__getDepartments.log', 'a');
fwrite($log,'filter_name) == $sql  )' . print_r($sql, true)  . '==' . chr(10) . chr(13));
fclose($log);

		if (!empty($data['filter_path'])) { 
			$sql = "SELECT tbl.* FROM (" . $sql . ") as tbl WHERE tbl.path LIKE '%" . $this->db->escape($data['filter_path']) . "%'";
$log = fopen(DIR_LOGS . 'admin_model_localisation_department__getDepartments.log', 'a');
fwrite($log,'filter_path) == $sql  )' . print_r($sql, true)  . '==' . chr(10) . chr(13));
fclose($log);
		} else if (!empty($data['filter_path_or_name'])){
			$sql = "SELECT tbl.* FROM (" . $sql . ") as tbl WHERE (lower(tbl.path) LIKE '%" . mb_strtolower($this->db->escape($data['filter_path_or_name'])) . "%' OR lower(tbl.name) LIKE '%" . mb_strtolower($this->db->escape($data['filter_path_or_name'])) . "%')";
$log = fopen(DIR_LOGS . 'admin_model_localisation_department__getDepartments.log', 'a');
fwrite($log,'filter_path_or_name) == $sql  )' . print_r($sql, true)  . '==' . chr(10) . chr(13));
fclose($log);
		}

		
		$sort_data = array(
			'name',
			'path',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}
$log = fopen(DIR_LOGS . 'admin_model_localisation_department__getDepartments.log', 'a');
fwrite($log,'ORDER) == $sql  )' . print_r($sql, true)  . '==' . chr(10) . chr(13));
fclose($log);

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
$log = fopen(DIR_LOGS . 'admin_model_localisation_department__getDepartments.log', 'a');
fwrite($log,'LIMIT) == $sql  )' . print_r($sql, true)  . '==' . chr(10) . chr(13));
fclose($log);

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalDepartments() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "department");

		return $query->row['total'];
	}

	public function getDepartmentDescriptions($department_id) {
		$department_description_data = array();
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "department_description WHERE department_id = '" . (int)$department_id . "'");

		foreach ($query->rows as $result) {
			$department_description_data[$result['language_id']] = array(
				'schedule' 		=> htmlspecialchars(html_entity_decode ($result['schedule'],ENT_HTML5)),
				'name'     		=> htmlspecialchars(html_entity_decode ($result['name'],ENT_QUOTES))
			);
		}

		return $department_description_data;
	}
	
}

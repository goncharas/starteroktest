<?php
class ModelLocalisationEmployee extends Model {
	public function addEmployee($data) {
$log = fopen(DIR_LOGS . 'admin_model_localisation_employee__addEmployee.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,' $employee_id )' . print_r($employee_id, true) . '==' . chr(10) . chr(13));
fwrite($log,' $data )' . print_r($data, true) . '==' . chr(10) . chr(13));
fclose($log);
		$this->db->query("INSERT INTO " . DB_PREFIX . "employee SET department_id = '" . (int)$data['department_id'] . "', nickname = '" . $this->db->escape($data['nickname']) . "', image = '" . $this->db->escape($data['image']) . "', skype = '" . $this->db->escape($data['skype']) . "', email = '" . $this->db->escape($data['email']) . "', status = '" . (int)$data['status'] . "', `order` = '" . (int)$data['order'] . "'");
	
		$employee_id = $this->db->getLastId();

		foreach ($data['employee_contact'] as $value) {
			if(!empty($value['telephone']))
				$this->db->query("INSERT INTO " . DB_PREFIX . "employee_contact SET employee_id = '" . (int)$employee_id . "', telephone = '" . $this->db->escape($value['telephone']) . "', viber = '" . $this->db->escape($value['viber']) . "', telegram = '" . $this->db->escape($value['telegram']) . "', whatsapp = '" . $this->db->escape($value['whatsapp']) . "'");
		}

		foreach ($data['employee_details'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "employee_details SET employee_id = '" . (int)$employee_id . "', language_id = '" . (int)$language_id . "', position = '" . $this->db->escape($value['position']) . "', name = '" . $this->db->escape($value['name']) . "'");
		}

		return $employee_id;
	}

	public function editEmployee($employee_id, $data) {
$log = fopen(DIR_LOGS . 'admin_model_localisation_employee__editEmployee.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,' $employee_id )' . print_r($employee_id, true) . '==' . chr(10) . chr(13));
fwrite($log,' $data )' . print_r($data, true) . '==' . chr(10) . chr(13));
fclose($log);
		
		$this->db->query("UPDATE " . DB_PREFIX . "employee SET department_id = '" . (int)$data['department_id'] . "', nickname = '" . $this->db->escape($data['nickname']) . "', image = '" . $this->db->escape($data['image']) . "', skype = '" . $this->db->escape($data['skype']) . "', email = '" . $this->db->escape($data['email']) . "', status = '" . (int)$data['status'] . "', `order` = '" . (int)$data['order'] . "' WHERE employee_id = '" . (int)$employee_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "employee_contact WHERE employee_id = '" . (int)$employee_id . "'");

		foreach ($data['employee_contact'] as $value) {
			if(!empty($value['telephone']))
				$this->db->query("INSERT INTO " . DB_PREFIX . "employee_contact SET employee_id = '" . (int)$employee_id . "', telephone = '" . $this->db->escape($value['telephone']) . "', viber = '" . $this->db->escape($value['viber']) . "', telegram = '" . $this->db->escape($value['telegram']) . "', whatsapp = '" . $this->db->escape($value['whatsapp']) . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "employee_details WHERE employee_id = '" . (int)$employee_id . "'");

		foreach ($data['employee_details'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "employee_details SET employee_id = '" . (int)$employee_id . "', language_id = '" . (int)$language_id . "', position = '" . $this->db->escape($value['position']) . "', name = '" . $this->db->escape($value['name']) . "'");
		}
		
	}

	public function deleteEmployee($employee_id) { 
		$this->db->query("DELETE FROM " . DB_PREFIX . "employee_contact WHERE employee_id = '" . (int)$employee_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "employee_details WHERE employee_id = '" . (int)$employee_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "employee WHERE employee_id = " . (int)$employee_id);
	}

	public function getEmployee($employee_id) { 
		$query = $this->db->query("SELECT DISTINCT e.*, ( SELECT CONCAT(ocd.name,'&nbsp;&nbsp;&gt;&nbsp;&nbsp;', odd.NAME) FROM " . DB_PREFIX . "department od LEFT JOIN " . DB_PREFIX . "department_description odd ON odd.department_id = od.department_id LEFT JOIN " . DB_PREFIX . "location_description ocd ON od.location_id = ocd.location_id WHERE IFNULL(odd.language_id,'" . (int)$this->config->get('config_language_id') . "') = '" . (int)$this->config->get('config_language_id') . "'  AND odd.department_id = e.department_id AND  IFNULL(ocd.language_id,'" . (int)$this->config->get('config_language_id') . "') = '" . (int)$this->config->get('config_language_id') . "'  ) AS path FROM " . DB_PREFIX . "employee e WHERE e.employee_id = '" . (int)$employee_id . "'");

		return $query->row;
	}

	public function getEmployees($data = array()) { 
		$sql = "SELECT e.employee_id, e.department_id, e.nickname, e.image, e.skype, e.email, e.order, e.status, ( SELECT CONCAT(ocd.name,'&nbsp;&nbsp;&gt;&nbsp;&nbsp;', odd.NAME) FROM " . DB_PREFIX . "department od LEFT JOIN " . DB_PREFIX . "department_description odd ON odd.department_id = od.department_id LEFT JOIN " . DB_PREFIX . "location_description ocd ON od.location_id = ocd.location_id WHERE IFNULL(odd.language_id,'" . (int)$this->config->get('config_language_id') . "') = '" . (int)$this->config->get('config_language_id') . "'  AND odd.department_id = e.department_id AND  IFNULL(ocd.language_id,'" . (int)$this->config->get('config_language_id') . "') = '" . (int)$this->config->get('config_language_id') . "'  ) AS path, ( SELECT oed.NAME FROM " . DB_PREFIX . "employee_details oed WHERE IFNULL(oed.language_id,'" . (int)$this->config->get('config_language_id') . "') = '" . (int)$this->config->get('config_language_id') . "'  AND oed.employee_id = e.employee_id) as name FROM " . DB_PREFIX . "employee e WHERE 1 = 1 ";

		if (!empty($data['filter_path'])) {
			$sql .= "SELECT tbl.* FROM (" . $sql . ") as tbl WHERE tbl.path LIKE '%" . $this->db->escape($data['filter_path']) . "%'";
		}
		

		if (!empty($data['filter_name'])) {
			$sql .= " AND (tbl.nickname LIKE '%" . $this->db->escape($data['filter_name']) . "%' OR tbl.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		$sort_data = array(
			'nickname',
			'path',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . ($data['sort'] == 'nickname' ? 'nickname, name' : $data['sort']);
		} else {
			$sql .= " ORDER BY nickname, name";
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
$log = fopen(DIR_LOGS . 'admin_model_localisation_employee__getEmployees.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,' $data )' . print_r($data, true) . '==' . chr(10) . chr(13));
fwrite($log,' $sql  )' . print_r($sql, true)  . '==' . chr(10) . chr(13));
fclose($log);

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalEmployees() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "employee");

		return $query->row['total'];
	}

	public function getEmployeeContact($employee_id) {
		$employee_contact_data = array();
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "employee_contact WHERE employee_id = '" . (int)$employee_id . "'");

		foreach ($query->rows as $result) {
			$employee_contact_data[] = array(
				'telephone' 		=> htmlspecialchars(html_entity_decode ($result['telephone'],ENT_HTML5)),
				'viber'     		=> htmlspecialchars(html_entity_decode ($result['viber'],ENT_QUOTES)),
				'telegram'     		=> htmlspecialchars(html_entity_decode ($result['telegram'],ENT_QUOTES)),
				'whatsapp'     		=> htmlspecialchars(html_entity_decode ($result['whatsapp'],ENT_QUOTES))
			);
		}

		return $employee_contact_data;
	}

	public function getEmployeeDetails($employee_id) {
		$employee_details_data = array();
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "employee_details WHERE employee_id = '" . (int)$employee_id . "'");

		foreach ($query->rows as $result) {
			$employee_details_data[$result['language_id']] = array(
				'name' 		=> htmlspecialchars(html_entity_decode ($result['name'],ENT_HTML5)),
				'position'	=> htmlspecialchars(html_entity_decode ($result['position'],ENT_QUOTES))
			);
		}

		return $employee_details_data;
	}	
}

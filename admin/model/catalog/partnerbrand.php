<?php
class ModelCatalogPartnerbrand extends Model {
	public function addpartnerbrand($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "partner_brand SET partner_brand = '" . $this->db->escape($data['partner_brand']) . "', partner_brand_clear = '" . $this->db->escape($data['partner_brand_clear']) . "', location_id = '" . (int)$data['location_id'] . "', manufacturer_id = '" . (int)$data['manufacturer_id'] . "'");

		$partnerbrand_id = $this->db->getLastId();

		return $partnerbrand_id;
	}

	public function editpartnerbrand($partnerbrand_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "partner_brand SET partner_brand = '" . $this->db->escape($data['partner_brand']) . "', partner_brand_clear = '" . $this->db->escape($data['partner_brand_clear']) . "', location_id = '" . (int)$data['location_id'] . "', manufacturer_id = '" . (int)$data['manufacturer_id'] . "' WHERE partnerbrand_id = '" . (int)$partnerbrand_id . "'");
	}

	public function deletepartnerbrand($partnerbrand_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "partner_brand WHERE partnerbrand_id = '" . (int)$partnerbrand_id . "'");
	}

	public function getpartnerbrand($partnerbrand_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "partner_brand WHERE partnerbrand_id = '" . (int)$partnerbrand_id . "'");

		return $query->row;
	}

	public function getpartnerbrands($data = array()) {
			$sql = "SELECT pb.*, (SELECT CONCAT(IFNULL(IFNULL(ld.name,l.name),''),' - ', IFNULL(IFNULL(ld.address,l.address),'')) FROM " . DB_PREFIX . "location l LEFT JOIN " . DB_PREFIX . "location_description ld on l.location_id = ld.location_id WHERE IFNULL(ld.language_id,'" . (int)$this->config->get('config_language_id') . "') = '" . (int)$this->config->get('config_language_id') . "'  AND l.location_id = pb.location_id) location, (SELECT m.name FROM " . DB_PREFIX . "manufacturer m WHERE m.manufacturer_id = pb.manufacturer_id) manufacturer FROM " . DB_PREFIX . "partner_brand pb ";

			$sort_data = array(
				'partner_brand',
				'location',
				'manufacturer'
			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY partner_brand";
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

			$query = $this->db->query($sql);

			return $query->rows;
	}

	public function getTotalpartnerbrands() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "partner_brand");

		return $query->row['total'];
	}
}
<?php
class ModelLocalisationZonenp extends Model {
	public function getZone($zone_id) {
		$query = $this->db->query("SELECT ref as zone_id, area as country_id, descriptionRu as name, '' as code, status  FROM " . DB_PREFIX . "np_cities WHERE ref = '" . $zone_id . "' AND status = '1'");
 
		return $query->row;
	}

	public function getZonesByCountryId($country_id) {
		$zone_data = $this->cache->get('zone.' . (int)$country_id);

		if (!$zone_data) {
			$query = $this->db->query("SELECT ref as zone_id, area as country_id, descriptionRu as name, '' as code, status FROM " . DB_PREFIX . "np_cities WHERE area = '" . $country_id . "' AND status = '1' ORDER BY name");

			$zone_data = $query->rows;

			$this->cache->set('zone.' . $country_id, $zone_data);
		}

		return $zone_data;
	}
}
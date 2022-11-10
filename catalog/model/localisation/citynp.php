<?php
class ModelLocalisationCitynp extends Model {
    public function getCity($city_id) {
        $query = $this->db->query("SELECT ref as city_id, CityRef as zone_id, descriptionRu as name, '' as code, status, sort_order  FROM " . DB_PREFIX . "np_offices WHERE ref = '" . $city_id . "'");

		return $query->row;
    }

    public function getCities($data = array()) {
        $city_data = $this->cache->get('city.status');

		if (!$city_data) {
			$query = $this->db->query("SELECT ref as city_id, CityRef as zone_id, descriptionRu as name, '' as code, status, sort_order FROM " . DB_PREFIX . "np_offices WHERE status = '1' ORDER BY name ASC");

			$city_data = $query->rows;

			$this->cache->set('city.status', $city_data);
		}

		return $city_data;
    }

    public function getCitiesByZoneId($zone_id) {
		$city_data = $this->cache->get('city.' . $zone_id);

		if (!$city_data) {
			$query = $this->db->query("SELECT ref as city_id, CityRef as zone_id, descriptionRu as name, '' as code, status, sort_order FROM " . DB_PREFIX . "np_offices WHERE CityRef = '" . $zone_id . "' AND status = '1' ORDER BY sort_order");

			$city_data = $query->rows;

			$this->cache->set('city.' . $zone_id, $city_data);
		}

		return $city_data;
	}
	
}
?>
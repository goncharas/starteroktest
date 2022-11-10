<?php
class ModelLocalisationCountrynp extends Model {
	public function getCountry($country_id) {
		$query = $this->db->query("SELECT ref as country_id, description as name, 'UA' as iso_code_2, 'UKR' as iso_code_3, '' as address_format, 0 as postcode_required, status   FROM " . DB_PREFIX . "np_areas WHERE ref = '" . $country_id . "' AND status = '1'");

		return $query->row;
	}

	public function getCountries() {
		$country_data = $this->cache->get('country.catalog');

		if (!$country_data) {
			$query = $this->db->query("SELECT ref as country_id, description as name, 'UA' as iso_code_2, 'UKR' as iso_code_3, '' as address_format, 0 as postcode_required, status FROM " . DB_PREFIX . "np_areas WHERE status = '1' ORDER BY name ASC");

			$country_data = $query->rows;

			$this->cache->set('country.catalog', $country_data);
		}

		return $country_data;
	}
}
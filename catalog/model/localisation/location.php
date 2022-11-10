<?php
class ModelLocalisationLocation extends Model {
	public function getLocation($location_id) {
		$query = $this->db->query("SELECT location_id, name, address, geocode, telephone, fax, image, open, comment FROM " . DB_PREFIX . "location WHERE location_id = '" . (int)$location_id . "'");

		if($query->num_rows)
			return $query->row;
		else return false;
	}

	public function getLocationList($language_id, $is_parter = '0') {
        $query = $this->db->query("select loc.* from (SELECT locd.name,locd.city,locd.address,loc.fax,loc.telephone,loc.geocode,loc.open,loc.google_map_iframe_src,loc.parent_id,loc.viber,loc.telegram,loc.whatsapp,loc.skype,loc.youtube_id,loc.email,locd.location_type,loc.location_id,loc.order,locd.schedule,locd.region,loc.postcode FROM " . DB_PREFIX . "location loc inner join " . DB_PREFIX . "location_description locd on (loc.location_id = locd.location_id) where locd.language_id = " . (int)$language_id . " and loc.status = 1) loc, (SELECT locd.city,min(loc.order) city_order FROM " . DB_PREFIX . "location loc inner join " . DB_PREFIX . "location_description locd on (loc.location_id = locd.location_id) where locd.language_id = " . (int)$language_id . " and loc.status = 1 " . (empty($is_partner) ? "" : "AND loc.is_partner = '" . (int)$is_partner . "'") . " group by locd.city) c where loc.city = c.city order by c.city_order,loc.order");

		return $query->rows;
	}

	public function getMainLocationList($language_id) {
        $query = $this->db->query("SELECT locd.city, loc.fax FROM " . DB_PREFIX . "location loc inner join " . DB_PREFIX . "location_description locd on (loc.location_id = locd.location_id) where loc.parent_id=0 and locd.language_id = " . (int)$language_id);

		return $query->rows;
	}

	public function getStuffList($location_id, $language_id) {
        $query = $this->db->query("SELECT ed.name,ed.position,e.image,ed.employee_id,dd.name department_name,e.skype FROM " . DB_PREFIX . "employee e inner join " . DB_PREFIX . "employee_details ed on (e.employee_id = ed.employee_id) inner join " . DB_PREFIX . "department d on (e.department_id = d.department_id) inner join " . DB_PREFIX . "department_description dd on (d.department_id = dd.department_id) where d.location_id = " . $location_id . " and dd.language_id = " . $language_id . " and ed.language_id = " . (int)$language_id . " and e.status = 1 order by d.order,e.order");

		return $query->rows;
	}

	public function getStuffPhones($stuff_id) {
        $query = $this->db->query("SELECT c.telephone,c.viber,c.telegram,c.whatsapp FROM " . DB_PREFIX . "employee_contact c where employee_id = " . $stuff_id);

		return $query->rows;
	}
}
<?php
class ModelReportPartnerprice extends Model {
	public function getpartnerprices($data = array()) {
			$sql = "SELECT tbl.* FROM (SELECT p.product_id, p.customer_group_id, p.minprice, p.maxprice, IF(p.maxprice = 0, 0, IF(p.minprice = p.maxprice,0, (p.minprice / p.maxprice)))*100 deviation, ocgd.name cgd_name, pr.sku partner_brand, pd.name, (SELECT CONCAT(l.name, ' - ', l.address) FROM " . DB_PREFIX . "location l INNER JOIN " . DB_PREFIX . "product_price pp ON l.location_id=pp.location_id WHERE pp.product_id=p.product_id AND pp.price = p.minprice LIMIT 1) location FROM (SELECT opp.product_id, opp.customer_group_id, MIN(opp.price) minprice, MAX(opp.price) maxprice FROM " . DB_PREFIX . "product_price opp GROUP BY opp.product_id, opp.customer_group_id) p LEFT JOIN " . DB_PREFIX . "customer_group_description ocgd ON ocgd.customer_group_id = p.customer_group_id AND ocgd.language_id = '" . (int)$this->config->get('config_language_id') . "'  INNER JOIN " . DB_PREFIX . "product pr ON pr.product_id = p.product_id AND pr.status = '1'  LEFT JOIN " . DB_PREFIX . "product_description pd ON pd.product_id = p.product_id AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "') tbl WHERE 1 = 1 ";

      if(isset($data['deviation']))
        $sql .= " AND deviation >= '" . (int)$data['deviation'] . "' ";

			$sort_data = array(
				'partner_brand',
				'location',
				'price'
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
if(admin_model_report_partnerprice__getpartnerprices){
$log = fopen(DIR_LOGS . 'admin_model_report_partnerprice__getpartnerprices.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,' $data )' . print_r($data, true) . '==' . chr(10) . chr(13));
fwrite($log,' $sql )' . print_r($sql, true) . '==' . chr(10) . chr(13));
fclose($log);}

			$query = $this->db->query($sql);

			return $query->rows;
	}

	public function getTotalpartnerprices($data = array()) {
			$sql = "SELECT tbl.* FROM (SELECT p.product_id, p.customer_group_id, p.minprice, p.maxprice, IF(p.maxprice = 0, 0, IF(p.minprice = p.maxprice,0, (p.minprice / p.maxprice)))*100 deviation, ocgd.name cgd_name, pr.sku partner_brand, pd.name, (SELECT CONCAT(l.name, ' - ', l.address) FROM " . DB_PREFIX . "location l INNER JOIN " . DB_PREFIX . "product_price pp ON l.location_id=pp.location_id WHERE pp.product_id=p.product_id AND pp.price = p.minprice LIMIT 1) location FROM (SELECT opp.product_id, opp.customer_group_id, MIN(opp.price) minprice, MAX(opp.price) maxprice FROM " . DB_PREFIX . "product_price opp GROUP BY opp.product_id, opp.customer_group_id) p LEFT JOIN " . DB_PREFIX . "customer_group_description ocgd ON ocgd.customer_group_id = p.customer_group_id AND ocgd.language_id = '" . (int)$this->config->get('config_language_id') . "'  INNER JOIN " . DB_PREFIX . "product pr ON pr.product_id = p.product_id AND pr.status = '1'  LEFT JOIN " . DB_PREFIX . "product_description pd ON pd.product_id = p.product_id AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "') tbl WHERE 1 = 1 ";

      if(isset($data['deviation']))
        $sql .= " AND deviation >= '" . (int)$data['deviation'] . "' ";

		  $sql = "SELECT COUNT(*) AS total FROM (" . $sql . ") cnt";
if(admin_model_report_partnerprice__getTotalpartnerprices){
$log = fopen(DIR_LOGS . 'admin_model_report_partnerprice__getTotalpartnerprices.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,' $data )' . print_r($data, true) . '==' . chr(10) . chr(13));
fwrite($log,' $sql )' . print_r($sql, true) . '==' . chr(10) . chr(13));
fclose($log);}

			$query = $this->db->query($sql);

			return $query->row['total'];
	}
}
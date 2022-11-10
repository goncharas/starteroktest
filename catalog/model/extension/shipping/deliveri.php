<?php
class ModelExtensionShippingDeliveri extends Model {
	function getQuote($address) {
if(catalog_model_extension_shipping_deliveri__getQuote){
$log = fopen(DIR_LOGS . 'catalog_model_extension_shipping_deliveri__getQuote.log', 'w');
fwrite($log,'**********************************' . chr(13) . chr(13));
$t = microtime(true);
$micro = sprintf("%06d",($t - floor($t)) * 1000000);
$d = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );
fwrite($log, $d->format("Y-m-d H:i:s.u") . '==' . chr(10) . chr(13));
fwrite($log, '$address ) ' . print_r($address,true) . chr(13) . chr(13));
fclose($log);}
		$this->load->language('extension/shipping/deliveri');

		$sql = "SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('shipping_deliveri_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')";
if(catalog_model_extension_shipping_deliveri__getQuote){
$log = fopen(DIR_LOGS . 'catalog_model_extension_shipping_deliveri__getQuote.log', 'a');
fwrite($log,'++++++++++++++++++++++++++++++++++' . chr(13) . chr(13));
fwrite($log, '$sql ) ' . print_r($sql,true) . chr(13) . chr(13));
fclose($log);}
		$query = $this->db->query($sql);
if(catalog_model_extension_shipping_deliveri__getQuote){
$log = fopen(DIR_LOGS . 'catalog_model_extension_shipping_deliveri__getQuote.log', 'a');
fwrite($log,'++++++++++++++++++++++++++++++++++' . chr(13) . chr(13));
fwrite($log, '$query->rows ) ' . print_r($query->rows,true) . chr(13) . chr(13));
fclose($log);}

		if (!$this->config->get('shipping_deliveri_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}
if(catalog_model_extension_shipping_deliveri__getQuote){
$log = fopen(DIR_LOGS . 'catalog_model_extension_shipping_deliveri__getQuote.log', 'a');
fwrite($log,'++++++++++++++++++++++++++++++++++' . chr(13) . chr(13));
fwrite($log, '$status ) ' . print_r($status,true) . chr(13) . chr(13));
fclose($log);}

		$method_data = array();

		if ($status) {
			$quote_data = array();

			$quote_data['deliveri'] = array(
				'code'         => 'deliveri.deliveri',
				'title'        => $this->language->get('text_description'),
				'cost'         => 0.00,
				'tax_class_id' => 0,
				'text'         => $this->currency->format(0.00, $this->session->data['currency'])
			);

			$method_data = array(
				'code'       => 'deliveri',
				'title'      => $this->language->get('text_title'),
				'quote'      => $quote_data,
				'sort_order' => $this->config->get('shipping_deliveri_sort_order'),
				'error'      => false
			);
		}

		return $method_data;
	}
}
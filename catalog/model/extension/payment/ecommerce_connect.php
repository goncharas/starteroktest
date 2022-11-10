<?php
class ModelExtensionPaymentecommerceConnect extends Model
{
    private $code = 'ecommerce_connect';

    public function getMethod($address, $total)
    {
        $this->load->language('extension/payment/ecommerce_connect');

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('ecommerce_connect_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

        if ($this->config->get('ecommerce_connect_total') > 0 && $this->config->get('ecommerce_connect_total') > $total) {
            $status = false;
        } elseif (!$this->config->get('ecommerce_connect_geo_zone_id')) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }

        $method_data = array();

        if ($status) {
            $method_data = array(
                'code' => $this->code,
                'title' => $this->language->get('text_title'),
                'terms' => '',
                'sort_order' => $this->config->get('ecommerce_connect_sort_order')
            );
        }

        return $method_data;
    }
}

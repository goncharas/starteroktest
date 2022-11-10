<?php
class ModelCatalogReview extends Model {
	public function addReview($product_id, $data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "review SET author = '" . $this->db->escape($data['name']) . "', customer_id = '" . (int)$this->customer->getId() . "', product_id = '" . (int)$product_id . "', text = '" . $this->db->escape($data['text']) . "', rating = '" . (int)$data['rating'] . "', date_added = NOW()");

		$review_id = $this->db->getLastId();

		if (in_array('review', (array)$this->config->get('config_mail_alert'))) {
			$this->load->language('mail/review');
			$this->load->model('catalog/product');
			
			$product_info = $this->model_catalog_product->getProduct($product_id);

			$subject = sprintf($this->language->get('text_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));

			$message  = $this->language->get('text_waiting') . "\n";
			$message .= sprintf($this->language->get('text_product'), html_entity_decode($product_info['name'], ENT_QUOTES, 'UTF-8')) . "\n";
			$message .= sprintf($this->language->get('text_reviewer'), html_entity_decode($data['name'], ENT_QUOTES, 'UTF-8')) . "\n";
			$message .= sprintf($this->language->get('text_rating'), $data['rating']) . "\n";
			$message .= $this->language->get('text_review') . "\n";
			$message .= html_entity_decode($data['text'], ENT_QUOTES, 'UTF-8') . "\n\n";

			$mail = new Mail($this->config->get('config_mail_engine'));
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

			$mail->setTo($this->config->get('config_email'));
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$mail->setSubject($subject);
			$mail->setText($message);
			$mail->send();

			// Send to additional alert emails
			$emails = explode(',', $this->config->get('config_mail_alert_email'));

			foreach ($emails as $email) {
				if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
					$mail->setTo($email);
					$mail->send();
				}
			}
		}
	}

	public function getReviewsByProductId($product_id, $start = 0, $limit = 20) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 20;
		}

//		$query = $this->db->query("SELECT r.review_id, r.author, r.rating, r.text, p.product_id, pd.name, p.price, p.image, r.date_added FROM " . DB_PREFIX . "review r LEFT JOIN " . DB_PREFIX . "product p ON (r.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND p.date_available <= NOW() AND p.status = '1' AND r.status = '1' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY r.date_added DESC LIMIT " . (int)$start . "," . (int)$limit);
//
//		return $query->rows;
        
        return $this->getReviews([
            'where' => [
                'product_id' => (int)$product_id
            ],
            'limit' => [
                'start' => $start,
                'end' => $limit
            ]
        ]);
	}

	public function getTotalReviewsByProductId($product_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review r LEFT JOIN " . DB_PREFIX . "product p ON (r.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND p.date_available <= NOW() AND p.status = '1' AND r.status = '1' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row['total'];
	}
    
    public function getLastReviewsByCategory($category_id, $customWhere='', $start=0, $limit=3) {
        return $this->getReviews([
            'where' => [
                'category_id' => (int)$category_id,
                'custom' => (!empty($customWhere) ? $customWhere:'')
            ],
            'limit' => [
                'start' => $start,
                'end' => $limit
            ]
        ]);
    }
    public function getLastReviewsByManufacturer($manufacturer_id, $customWhere='', $start=0, $limit=3) {
        return $this->getReviews([
            'where' => [
                'manufacturer_id' => (int)$manufacturer_id,
                'custom' => (!empty($customWhere) ? $customWhere:'')
            ],
            'limit' => [
                'start' => $start,
                'end' => $limit
            ]
        ]);
    }
    
    public function getTotalReviews($options) {
if(catalog_model_catalog_review__getTotalReviews){
$log = fopen(DIR_LOGS . 'catalog_model_catalog_review__getTotalReviews.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,' $options )' . print_r($options, true) . '==' . chr(10) . chr(13));
fclose($log);}
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review r "
                . " INNER JOIN " . DB_PREFIX . "product p ON (r.product_id = p.product_id) "
                . " INNER JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) "
                . " WHERE p.date_available <= NOW() AND p.status = '1' "
                    . " AND r.status = '1' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'"
                . (!empty($options['where']['product_id'])? " AND p.product_id = '" . (int)$options['where']['product_id'] . "' ":'')
                . (!empty($options['where']['category_id'])? " AND p.product_id IN (SELECT p2c.product_id FROM " . DB_PREFIX . "product_to_category p2c WHERE p2c.category_id ='" . (int)$options['where']['category_id'] . "' ) ":'')
                . (!empty($options['where']['manufacturer_id'])? " AND p.manufacturer_id = '" . (int)$options['where']['manufacturer_id'] . "' ":'')
                . (!empty($options['where']['custom'])? " AND (" . $options['where']['custom'].")" :'');
if(catalog_model_catalog_review__getTotalReviews){
$log = fopen(DIR_LOGS . 'catalog_model_catalog_review__getTotalReviews.log', 'a');
fwrite($log,' $sql )' . print_r($sql, true) . '==' . chr(10) . chr(13));
fclose($log);}
        $query = $this->db->query($sql);

		return $query->row['total'];
	}
    
    public function getReviews($options) {
if(catalog_model_catalog_review__getReviews){
$log = fopen(DIR_LOGS . 'catalog_model_catalog_review__getReviews.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,' $options )' . print_r($options, true) . '==' . chr(10) . chr(13));
fclose($log);}
		$sql = "SELECT r.review_id, r.author, r.rating, r.text, p.product_id, pd.name, pp.price, p.image, r.date_added "
            . " FROM " . DB_PREFIX . "review r "
            . " LEFT JOIN " . DB_PREFIX . "product p ON (r.product_id = p.product_id)  INNER JOIN " . DB_PREFIX . "product_price pp ON( pp.product_id = p.product_id AND pp.price > 0  AND pp.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "') "
            . " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) "
            . " WHERE "
                . " p.date_available <= NOW() AND p.status = '1' "
                . " AND r.status = '1' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' "
                . (!empty($options['where']['product_id'])? " AND p.product_id = '" . (int)$options['where']['product_id'] . "' ":'')
                . (!empty($options['where']['category_id'])? " AND p.product_id IN (SELECT p2c.product_id FROM " . DB_PREFIX . "product_to_category p2c WHERE p2c.category_id ='" . (int)$options['where']['category_id'] . "' ) ":'')
                . (!empty($options['where']['manufacturer_id'])? " AND p.manufacturer_id = '" . (int)$options['where']['manufacturer_id'] . "' ":'')
                . (!empty($options['where']['custom'])? " AND (" . $options['where']['custom'].")" :'')
            . " ORDER BY r.date_added DESC "
            . (!empty($options['limit'])? "LIMIT " . (int)$options['limit']['start'] . "," . (int)$options['limit']['end'] :'');
if(catalog_model_catalog_review__getReviews){
$log = fopen(DIR_LOGS . 'catalog_model_catalog_review__getReviews.log', 'a');
fwrite($log,' $sql )' . print_r($sql, true) . '==' . chr(10) . chr(13));
fclose($log);}
        $query = $this->db->query($sql);
//d($sql);
		return $query->rows;     
    }
}
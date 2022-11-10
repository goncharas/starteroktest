<?php
class ModelExtensionModuleJetimpexNewsletter extends Model
{
	public function addNewsletter($data){
		$this->db->query("INSERT INTO " . DB_PREFIX . "jetimpex_newsletter SET jetimpex_newsletter_email = '" . $data ."'");
	}

	public function deleteNewsletter($data){
		$this->db->query("DELETE FROM `" . DB_PREFIX . "jetimpex_newsletter` WHERE jetimpex_newsletter_email = '" . $data . "'");
	}

	public function getNewsletters(){
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "jetimpex_newsletter");
		return $query->rows;
	}

	public function getNewsletterByEmail($email) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "jetimpex_newsletter WHERE jetimpex_newsletter_email = '" . $email . "'");

		return $query->row;
	}

}
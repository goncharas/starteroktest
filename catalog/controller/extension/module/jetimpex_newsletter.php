<?php

class ControllerExtensionModuleJetimpexNewsletter extends Controller
{
	private $error = array();

	private function install()
	{
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "jetimpex_newsletter` (
			`jetimpex_newsletter_id` int(11) NOT NULL AUTO_INCREMENT,
			`jetimpex_newsletter_email` varchar(255) NOT NULL,
			PRIMARY KEY (`jetimpex_newsletter_id`)
			)");
	}

	public function index($setting)
	{		
		
		$this->install();
		$this->load->language('extension/module/jetimpex_newsletter');

		if (isset($setting['jetimpex_newsletter_description'][$this->config->get('config_language_id')])) {
			$data['description'] = html_entity_decode($setting['jetimpex_newsletter_description'][$this->config->get('config_language_id')]['description'], ENT_QUOTES, 'UTF-8');
		}
		if (isset($setting['name']) ){
			$data['name'] = $setting['name'];
		}

		$data['action'] = $this->url->link('extension/module/jetimpex_newsletter', '', true);


		return $this->load->view('extension/module/jetimpex_newsletter', $data);
	}
	public function addNewsletter() {
		if (isset($this->request->post['jetimpex_newsletter_email'])) {
			$this->load->model('account/customer');
			$this->load->model('extension/module/jetimpex_newsletter');
			$this->load->language('extension/module/jetimpex_newsletter');

			$input_email = $this->request->post['jetimpex_newsletter_email'];

			if ($this->customer->isLogged() && strcmp($this->customer->getEmail(), $input_email) == 0) {
				if ($this->customer->getNewsletter() == 0) {
					$this->model_account_customer->editNewsletter(1);
				} else {
					$this->error['jetimpex_newsletter_exist_email'] = $this->language->get('error_exist_email');
				}
			} else {
				if (count($this->model_extension_module_jetimpex_newsletter->getNewsletterByEmail($input_email)) != 0) {
					$this->error['jetimpex_newsletter_exist_email'] = $this->language->get('error_exist_email');
				} else if (count($this->model_account_customer->getCustomerByEmail($input_email)) == 0) {
					$this->model_extension_module_jetimpex_newsletter->addNewsletter($input_email);
				} else {
					$this->error['jetimpex_newsletter_exist_user'] = $this->language->get('error_exist_user');
				}
			}

			if (count($this->error) > 0) {
				foreach ($this->error as $err) {
					echo $err;
				}
			}
		}
	}
}
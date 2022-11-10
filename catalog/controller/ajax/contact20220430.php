<?php
class ControllerAjaxContact extends Controller {
    private $json = [];
    private $allowedTypes = ['repaire','contact','delivery'];
    
    public function index() {
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $type = !empty($this->request->post['type']) && (in_array($this->request->post['type'], $this->allowedTypes)) ? $this->request->post['type'] : 'contact';
        
            $this->load->language('ajax/validate'); 
            $this->load->language('mail/contact_'.$type);

            if ($this->validate($type)) {            
                $data = $this->getDataByType($type);
                $this->send($data, $type);

                $this->json['success'] = $this->language->get('contact_form_request_success');
            }
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($this->json));
    }
    
    public function validate($type) {
        
        if (isset($this->request->post['name']) &&
            ( (utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 70)) )
        {
			$this->json['error']['warning'] = sprintf($this->language->get('error_length'), $this->language->get('contact_form_field_name'),'3','70');
            $this->json['error']['field'] = 'name'; 
            return false;
		}
        if (empty($this->request->post['name'])) {
			$this->json['error']['warning'] = sprintf($this->language->get('error_required'), $this->language->get('contact_form_field_name'));
            $this->json['error']['field'] = 'name';  
            return false;
		}
        if (empty($this->request->post['phone'])) {
			$this->json['error']['warning'] = sprintf($this->language->get('error_required'), $this->language->get('contact_form_field_phone'));
            $this->json['error']['field'] = 'phone';  
            return false;
		}

//		if (!filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
//			$this->json['error']['email'] = $this->language->get('error_email');
//			$this->json['error']['field'] = 'email';  
//		}

		//if (isset($this->request->post['enquiry']) &&
        //    (utf8_strlen($this->request->post['enquiry']) < 10) || (utf8_strlen($this->request->post['enquiry']) > 3000)) 
        if ($type == 'repaire' && empty($this->request->post['enquiry'])) {
            $this->json['error']['warning'] = sprintf($this->language->get('error_required'), $this->language->get('contact_form_field_message'));
            $this->json['error']['field'] = 'enquiry'; 
            return false;    
        }

        if ($type == 'delivery' && empty($this->request->post['address'])) {
            $this->json['error']['warning'] = sprintf($this->language->get('error_required'), $this->language->get('contact_form_field_message'));
            $this->json['error']['field'] = 'address'; 
            return false;    
        }

		// Captcha
//		if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('contact', (array)$this->config->get('config_captcha_page'))) {
//			$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');
//
//			if ($captcha) {
//				$this->error['captcha'] = $captcha;
//			}
//		}

		return true;
        
    }
    
    private function getDataByType($type) {        
        $data = [];
        
        //sava 2022/02/17
        if (isset($this->request->post['mail_employee']) and !empty($this->request->post['mail_employee'])){
            $data['contact_form_receiver'] = $this->request->post['mail_employee']; 
        } else {
            $data['contact_form_receiver'] = $this->config->get('config_email');
        }

        switch($type) {
            case 'repaire': // запрос на ремонт

                //$data['contact_form_subject'] = sprintf($this->language->get('contact_form_subject'), html_entity_decode($this->request->post['name'], ENT_QUOTES, 'UTF-8'));
                $data['contact_form_subject'] = !empty($this->request->post['service']) ? $this->language->get('contact_form_subject') . ' ' . $this->request->post['service'] : $this->language->get('contact_form_subject');
                $data['contact_form_thanks'] = $this->language->get('contact_form_thanks');
                $data['contact_form_field_name'] = $this->language->get('contact_form_field_name');
                $data['contact_form_field_phone'] = $this->language->get('contact_form_field_phone');

                $data['name'] = $this->request->post['name'];
                $data['phone'] = $this->request->post['phone'];
                $data['enquiry'] = nl2br($this->request->post['enquiry']);
                break;
            case 'delivery': // запрос на бесплатную доставку

                $data['contact_form_subject'] = $this->language->get('contact_form_subject');
                $data['contact_form_thanks'] = $this->language->get('contact_form_thanks');
                $data['contact_form_field_name'] = $this->language->get('contact_form_field_name');
                $data['contact_form_field_phone'] = $this->language->get('contact_form_field_phone');
                $data['contact_form_field_address'] = $this->language->get('contact_form_field_address');

                $data['name'] = $this->request->post['name'];
                $data['phone'] = $this->request->post['phone'];
                $data['address'] = $this->request->post['address'];
                break;    
        }
        
        return $data;
    }
    
    private function send($data, $type) {
        $mail = new Mail($this->config->get('config_mail_engine'));
        $mail->parameter = $this->config->get('config_mail_parameter');
        $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
        $mail->smtp_username = $this->config->get('config_mail_smtp_username');
        $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
        $mail->smtp_port = $this->config->get('config_mail_smtp_port');
        $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

        // $mail->setTo($args[0]['email']);
        //$mail->setTo($this->config->get('config_email'));
        $mail->setTo($data['contact_form_receiver']);  //sava 2022/02/17
        
        $mail->setFrom($this->config->get('config_email'));
        $mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
        $mail->setSubject($data['contact_form_subject']);
        //$mail->setText($this->load->view('mail/contact_'.$type, $data));
        $mail->setHtml($this->load->view('mail/contact_'.$type, $data));
        $mail->send(); 
    }
}
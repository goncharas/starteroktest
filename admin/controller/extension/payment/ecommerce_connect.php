<?php

class ControllerExtensionPaymentECommerceConnect extends Controller
{
    private $error = array();

    public function index()
    {
        $this->load->language('extension/payment/ecommerce_connect');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');


        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->form_validate()) {

			$this->model_setting_setting->editSetting('payment_ecommerce_connect', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

	    $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
        }


        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_all_zones'] = $this->language->get('text_all_zones');
        $data['text_test_mode'] = $this->language->get('text_test_mode');
        $data['text_real_mode'] = $this->language->get('text_real_mode');

        $data['entry_merchant_id'] = $this->language->get('entry_merchant_id');
        $data['entry_terminal_id'] = $this->language->get('entry_terminal_id');
        $data['entry_api'] = $this->language->get('entry_api');
        $data['entry_pem'] = $this->language->get('entry_pem');
        $data['entry_cert'] = $this->language->get('entry_cert');
        $data['entry_work_mode'] = $this->language->get('entry_work_mode');
        $data['entry_total'] = $this->language->get('entry_total');
        $data['entry_order_status'] = $this->language->get('entry_order_status');
        $data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');

        $data['help_total'] = $this->language->get('help_total');
        $data['help_upc'] = $this->language->get('help_upc');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['merchant_id'])) {
            $data['error_merchant_id'] = $this->error['merchant_id'];
        } else {
            $data['error_merchant_id'] = '';
        }

        if (isset($this->error['terminal_id'])) {
            $data['error_terminal_id'] = $this->error['terminal_id'];
        } else {
            $data['error_terminal_id'] = '';
        }

        if (isset($this->error['api'])) {
            $data['error_api'] = $this->error['api'];
        } else {
            $data['error_api'] = '';
        }

        if (isset($this->error['pem'])) {
            $data['error_pem'] = $this->error['pem'];
        } else {
            $data['error_pem'] = '';
        }

        if (isset($this->error['cert'])) {
            $data['error_cert'] = $this->error['cert'];
        } else {
            $data['error_cert'] = '';
        }

        if (isset($this->error['action'])) {
            $data['error_action'] = $this->error['action'];
        } else {
            $data['error_action'] = '';
        }

        if (isset($this->error['work_mode'])) {
            $data['error_work_mode'] = $this->error['work_mode'];
        } else {
            $data['error_work_mode'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_payment'),
	    'href' => $this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/payment/ecommerce_connect', 'user_token=' . $this->session->data['user_token'], 'SSL')
        );

        $data['action'] = $this->url->link('extension/payment/ecommerce_connect', 'user_token=' . $this->session->data['user_token'], 'SSL');

        $data['cancel'] = $this->url->link('extension/payment', 'user_token=' . $this->session->data['user_token'], 'SSL');

        if (isset($this->request->post['payment_ecommerce_connect_merchant_id'])) {
            $data['payment_ecommerce_connect_merchant_id'] = $this->request->post['payment_ecommerce_connect_merchant_id'];
        } else {
            $data['payment_ecommerce_connect_merchant_id'] = $this->config->get('payment_ecommerce_connect_merchant_id');
        }

        if (isset($this->request->post['payment_ecommerce_connect_terminal_id'])) {
            $data['payment_ecommerce_connect_terminal_id'] = $this->request->post['payment_ecommerce_connect_terminal_id'];
        } else {
            $data['payment_ecommerce_connect_terminal_id'] = $this->config->get('payment_ecommerce_connect_terminal_id');
        }

        if (isset($this->request->post['payment_ecommerce_connect_api'])) {
            $data['payment_ecommerce_connect_api'] = $this->request->post['payment_ecommerce_connect_api'];
        } else {
            $data['payment_ecommerce_connect_api'] = $this->config->get('payment_ecommerce_connect_api');
        }

        if (isset($this->request->post['payment_ecommerce_connect_pem'])) {
            $data['payment_ecommerce_connect_pem'] = $this->request->post['payment_ecommerce_connect_pem'];
        } else {
            $data['payment_ecommerce_connect_pem'] = $this->config->get('payment_ecommerce_connect_pem');
        }
		
        if (isset($this->request->post['payment_ecommerce_connect_cert'])) {
            $data['payment_ecommerce_connect_cert'] = $this->request->post['payment_ecommerce_connect_cert'];
        } else {
            $data['payment_ecommerce_connect_cert'] = $this->config->get('payment_ecommerce_connect_cert');
        }
		
        if (isset($this->request->post['payment_ecommerce_connect_work_mode'])) {
            $data['payment_ecommerce_connect_work_mode'] = $this->request->post['payment_ecommerce_connect_work_mode'];
        } else {
            $data['payment_ecommerce_connect_work_mode'] = $this->config->get('payment_ecommerce_connect_work_mode');
        }

        if (isset($this->request->post['payment_ecommerce_connect_total'])) {
            $data['payment_ecommerce_connect_total'] = $this->request->post['payment_ecommerce_connect_total'];
        } else {
            $data['payment_ecommerce_connect_total'] = $this->config->get('payment_ecommerce_connect_total');
        }

        if (isset($this->request->post['payment_ecommerce_connect_order_status_id'])) {
            $data['payment_ecommerce_connect_order_status_id'] = $this->request->post['payment_ecommerce_connect_order_status_id'];
        } else {
            $data['payment_ecommerce_connect_order_status_id'] = $this->config->get('payment_ecommerce_connect_order_status_id');
        }

        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        if (isset($this->request->post['payment_ecommerce_connect_geo_zone_id'])) {
            $data['payment_ecommerce_connect_geo_zone_id'] = $this->request->post['payment_ecommerce_connect_geo_zone_id'];
        } else {
            $data['payment_ecommerce_connect_geo_zone_id'] = $this->config->get('payment_ecommerce_connect_geo_zone_id');
        }

        $this->load->model('localisation/geo_zone');

        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        if (isset($this->request->post['payment_ecommerce_connect_status'])) {
            $data['payment_ecommerce_connect_status'] = $this->request->post['payment_ecommerce_connect_status'];
        } else {
            $data['payment_ecommerce_connect_status'] = $this->config->get('payment_ecommerce_connect_status');
        }

        if (isset($this->request->post['payment_ecommerce_connect_sort_order'])) {
            $data['payment_ecommerce_connect_sort_order'] = $this->request->post['payment_ecommerce_connect_sort_order'];
        } else {
            $data['payment_ecommerce_connect_sort_order'] = $this->config->get('payment_ecommerce_connect_sort_order');
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/payment/ecommerce_connect', $data));
    }

    protected function form_validate()
    {

        if (!$this->request->post['payment_ecommerce_connect_merchant_id']) {
            $this->error['merchant_id'] = $this->language->get('error_merchant_id');
        }

        if (!$this->request->post['payment_ecommerce_connect_terminal_id']) {
            $this->error['terminal_id'] = $this->language->get('error_terminal_id');
        }

        if (!$this->user->hasPermission('modify', 'extension/payment/ecommerce_connect')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['payment_ecommerce_connect_api']) {
            $this->error['api'] = $this->language->get('error_api');
        }

        if (!$this->request->post['payment_ecommerce_connect_pem']) {
            $this->error['pem'] = $this->language->get('error_pem');
        }

        if (!$this->request->post['payment_ecommerce_connect_cert']) {
            $this->error['cert'] = $this->language->get('error_cert');
        }
		
		
        return !$this->error;
    }
}
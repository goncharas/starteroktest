<?php
class ControllerExtensionPaymentCod extends Controller {
	public function index() {
		$this->load->language('extension/payment/cod');

		$data['bank'] = nl2br($this->config->get('payment_cod' . $this->config->get('config_language_id')));

		return $this->load->view('extension/payment/cod');
	}

	public function confirm() {
if(catalog_controller_extension_payment_cod__confirm){
$log = fopen(DIR_LOGS . 'catalog_controller_extension_payment_cod__confirm.log', 'w');
fwrite($log,'**********************************' . chr(13) . chr(13));
$t = microtime(true);
$micro = sprintf("%06d",($t - floor($t)) * 1000000);
$d = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );
fwrite($log, $d->format("Y-m-d H:i:s.u") . '==' . chr(10) . chr(13));
fclose($log);}
		$json = array();
		$comment = '';
    
		if ($this->session->data['payment_method']['code'] == 'cod') {
			$this->load->model('checkout/order');

//			$comment  = $this->language->get('text_instruction') . "\n\n";
//			$comment .= $this->config->get('payment_cod' . $this->config->get('config_language_id')) . "\n\n";
//			$comment .= $this->language->get('text_payment');

   
			$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payment_cod_order_status_id'), $comment, true);
		
			$json['redirect'] = $this->url->link('checkout/success');
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));		
	}
}

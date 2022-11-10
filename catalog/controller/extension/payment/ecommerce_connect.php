<?php

class ControllerExtensionPaymentECommerceConnect extends Controller
{
    private $action = 'pay';

    public function index()
    {
        $this->load->language('extension/payment/ecommerce_connect');
        $data['button_confirm'] = $this->language->get('button_confirm');

        $merchant_id = $this->config->get('payment_ecommerce_connect_merchant_id');
        $terminal_id = $this->config->get('payment_ecommerce_connect_terminal_id');
        $data['api'] = $this->config->get('payment_ecommerce_connect_api');
        $pem = $this->config->get('payment_ecommerce_connect_pem');
		
		$currency_code = "980";

        $order_id = $this->session->data['order_id'];
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($order_id);

        // Collect info about the order to be sent to the API

        $currency = $order_info['currency_code'];

        $amount = $this->currency->format(
            $order_info['total'],
            "UAH",
            false,
            false
        )*100;

        $alt_amount = $this->currency->format(
            $order_info['total'],
            $order_info['currency_code'],
            $order_info['currency_value'],
            false
        )*100;

	$currency_number = array("USD"=>"840","UAH"=>"980","RUB"=>"643","EUR"=>"978");
	
		$alt_currency_code = $currency_number[$order_info['currency_code']];

		$PurchaseTime = date("ymdHis") ;
//$alt_currency_code,$alt_amount
		$data_str = "$merchant_id;$terminal_id;$PurchaseTime;$order_id;$currency_code;$amount;;";
		$pkeyid = openssl_get_privatekey($pem); 
		openssl_sign( $data_str , $signature, $pkeyid); 
		openssl_free_key($pkeyid); 
		$b64sign = base64_encode($signature);

		$data['ecommerce_connect']['MerchantID'] = $merchant_id;
		$data['ecommerce_connect']['TerminalID'] = $terminal_id;
		$data['ecommerce_connect']['TotalAmount'] = $amount;
		$data['ecommerce_connect']['Currency'] = $currency_code;
		//$data['ecommerce_connect']['AltTotalAmount'] = $alt_amount;
		//$data['ecommerce_connect']['AltCurrency'] = $alt_currency_code;
		$data['ecommerce_connect']['locale'] = 'ru';
		$data['ecommerce_connect']['PurchaseTime'] = $PurchaseTime;
		$data['ecommerce_connect']['OrderID'] = $order_id;
		$data['ecommerce_connect']['Signature'] = $b64sign;

        $view_path = 'extension/payment/ecommerce_connect';

        return $this->load->view($view_path, $data);
    }

	public function notify()
    {
        $server_cert = $this->config->get('payment_ecommerce_connect_cert');

        $MerchantID = $this->request->post['MerchantID'];
        $TerminalID = $this->request->post['TerminalID'];
        $OrderID = $this->request->post['OrderID'];
        $PurchaseTime = $this->request->post['PurchaseTime'];
        $TotalAmount = $this->request->post['TotalAmount'];
        $AltTotalAmount = $this->request->post["AltTotalAmount"];
        $CurrencyID = $this->request->post['Currency'];
        $AltCurrencyID = $this->request->post['AltCurrency'];
        $XID = $this->request->post['XID'];
        $SD = $this->request->post['SD'];
        $TranCode = $this->request->post['TranCode'];
        $ApprovalCode = $this->request->post['ApprovalCode'];
        $signature = $this->request->post["Signature"];

        echo "MerchantID = " . $MerchantID . "\n";
        echo "TerminalID = " . $TerminalID . "\n";
        echo "OrderID = " . $OrderID . "\n";
        echo "Currency = " . $CurrencyID . "\n";
        echo "TotalAmount = " . $TotalAmount . "\n";
        echo "XID = " . $XID . "\n";
        echo "TranCode = " . $TranCode . "\n";
        echo "PurchaseTime = " . $PurchaseTime . "\n";
        echo "Response.action= approve \n";
        echo "Response.reason= OK \n";
        echo "Response.forwardUrl=  ".$this->url->link('checkout/success')."\n";

        if ($TranCode == '000') { 
            $this->load->model('checkout/order');
            $this->model_checkout_order->addOrderHistory($OrderID, $this->config->get('payment_ecommerce_connect_order_status_id'));
        } 
    }
}

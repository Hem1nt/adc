<?php
class Iksula_Bpay_IndexController extends Mage_Core_Controller_Front_Action{
  
 /* public function IndexAction() 
  {
	$this->loadLayout();
    $this->renderLayout(); 	  
  }*/

  /**
	* Handle $_POST submission
	*/
  /*public function submitDetailsAction(){
  	$_form_data = array();
  	$store = Mage::app()->getStore();

    $_form_data['api_version'] = Mage::getStoreConfig('payment/bpay/apiversion', $store);
    $_form_data['api_key'] = Mage::getStoreConfig('payment/bpay/apikey', $store);
        
    $_form_data['name'] = $this->getRequest()->getParam('firstname');
    $_form_data['acc_no'] = $this->getRequest()->getParam('acc_no');
    $_form_data['phone'] = $this->getRequest()->getParam('phone');
    $_form_data['address'] = $this->getRequest()->getParam('address');
    $_form_data['email1'] = $this->getRequest()->getParam('email');
    $_form_data['other'] = $this->getRequest()->getParam('message');
    $_form_data['amount'] = $this->getRequest()->getParam('amount');
    $_form_data['yrn'] = '';//$this->getRequest()->getParam('yrn');
    $pstring = $this->paramString($_form_data);

    $remote_url = 'https://www.howtopay.com/Member/api_processPayment';
    $result = $this->send_curl_request($remote_url, $pstring, true);
    echo json_encode($result);
    return;
  }

  /**
	 * Construct post params
	 * @param type $params
	 * @return boolean
	 
	private function paramString($params = array()) {
	    if (!empty($params)) {
	        $_parameters = "";

	        foreach ($params as $_key => $_value) {
	            $_parameters .= $_key . '=' . $_value . '&';
	        }
	        $_parameters = substr($_parameters, 0, -1);
	        return $_parameters;
	    }
	    return false;
	}

	/**
	 * Increase timeout limit
	 
	private function SFD_ini_set_adjust() {
	    @ini_set('max_execution_time', 300);
	    @ini_set('memory_limit', '128M');
	}

	*
	 * Send request to server
	 * @param type $url
	 * @param type $postdata
	 * @param type $_type
	 * @return boolean
	 
	private function send_curl_request($url, $postdata, $_type = FALSE) {
	    $this->SFD_ini_set_adjust();

	    $ch = curl_init();

	    $header[] = "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,;q=0.5";
	    $header[] = "Cache-Control: max-age=0";
	    $header[] = "Connection: keep-alive";
	    $header[] = "Keep-Alive: 300";
	    $header[] = "Content-Type: application/x-www-form-urlencoded; charset=UTF-8";
	    $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
	    $header[] = "Accept-Language: en-us,en;q=0.5";
	    $header[] = "Pragma: ";

	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_HEADER, false);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
	    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:7.0.1) Gecko/20100101 Firefox/7.0.12011-10-16 20:23:00");
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	    curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
	    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 1200);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

	    $server_output = curl_exec($ch);
	    $_error = curl_error($ch);

	    curl_close($ch);
	    if ($_error == '') {
	        return json_decode($server_output, $_type);
	    } else {
	        return FALSE;
	    }
	}*/
}
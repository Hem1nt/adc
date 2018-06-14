<?php
class Iksula_Bpay_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function submitDetails($data,$orderId){	
    $orderIncrementId = $orderId;
  	$_form_data = array();
  	$store = Mage::app()->getStore();

    $_form_data['api_version'] = Mage::getStoreConfig('payment/bpay/apiversion', $store);
    $_form_data['api_key'] = Mage::getStoreConfig('payment/bpay/apikey', $store);
        
    $_form_data['name'] = $data['bpay_firstname'];
    $_form_data['acc_no'] = $data['bpay_acc_no'];
    $_form_data['phone'] = $data['bpay_phone'];
    $_form_data['address'] = '';//$data['bpay_firstname'];
    $_form_data['email1'] = $data['bpay_email'];
    $_form_data['other'] = '';//$data['bpay_firstname'];
    $_form_data['amount'] = $data['bpay_amount'];
    $_form_data['yrn'] = $orderIncrementId;//$data['bpay_firstname'];
    //print_r($_form_data);exit;
    $pstring = $this->paramString($_form_data);

    $remote_url = 'https://www.howtopay.com/Member/api_processPayment';
    $result = $this->send_curl_request($remote_url, $pstring, true);
    $timeStamp = $request['timestamp'];
		$nofifydate = date('d-m-Y H:i:s',$timeStamp);
		
		if(empty($timestamp)){
			$nofifydate =  date('d-m-Y H:i:s');
		}
	$_order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
		
	$status = 0;
	$statusName = $this->bpayStatusByCode($status);
	// $this->removeOrderStatusHistory($_order->getId());
	$data = $this->orderStatusByCode($statusName);
	$order_state = $data['state'];
	$order_status = $data['status'];

	if($_order->getId() && isset($order_state) && isset($order_status)){
		$_order->setStatus($order_status);
		$_order->setData('state', $order_state);
		if($result['type'] == 'success'){
			$comment = "Bpay Payment Status for ".$orderIncrementId." : ".$result['message'].' , Payment request Id : '.$result['payment_request_id'].' on '.$nofifydate.' ==>'.$statusName;
		}else{
			$comment = "Bpay Payment Status for ".$orderIncrementId." : ".' payment request declined , invalid details'.' on '.$nofifydate.' ==>'.$statusName;
		}
		$history = $_order->addStatusHistoryComment($comment, false);
		$_order->save();
	}
    return;
    //print_r($result);exit;
    //echo json_encode($result);
    //return;
  }

  /**
	 * Construct post params
	 * @param type $params
	 * @return boolean
	 */
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
	 */
	private function SFD_ini_set_adjust() {
	    @ini_set('max_execution_time', 300);
	    @ini_set('memory_limit', '128M');
	}

	/**
	 * Send request to server
	 * @param type $url
	 * @param type $postdata
	 * @param type $_type
	 * @return boolean
	 */
	private function send_curl_request($url, $postdata, $_type = FALSE) {
	    $this->SFD_ini_set_adjust();

	    $ch = curl_init();

	    $header[] = "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
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
	}

	private function bpayStatusByCode($statusCode)
	{
		$statusData = array( 
				'0' => 'Pending'
		);

		$status = $statusData[$statusCode];
		return $status;
	}

	private function orderStatusByCode($statusName)
	{
		
		switch ($statusName) {
			case 'Pending':
				$information['state'] = 'new';
				$information['status'] = 'pending';
				break;
			
			default:
				$information['state'] = 'new';
				$information['status'] = 'pending';
				break;
		}

		return $information;
	}
}

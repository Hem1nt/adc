<?php
// require('Sale.php');
class Iksula_Drc_Model_Drc extends Mage_Payment_Model_Method_Abstract
{
	protected $_code = 'drc';
	protected $_formBlockType = 'drc/form_drc';

	//protected $_isGateway               = true;
	protected $_canAuthorize            = true;
	protected $_canCapture              = true;
	//protected $_canCapturePartial       = true;
	protected $_canRefund               = true;
	protected $_canUseInternal          = false;
	protected $_canSaveCc = true; //if made try, the actual credit card number and cvv code are stored in database.

	protected $_order;



	// public function assignData($data)
	// {
	// 	if (!($data instanceof Varien_Object)) {
	// 		$data = new Varien_Object($data);
	// 	}
	// 	$info = $this->getInfoInstance();
	// 	$info->setCcType($data->getCcType())
	// 	->setCcOwner($data->getCcOwner())
	// 	->setCcLast4(substr($data->getCcNumber(), -4))
	// 	->setCcNumber($data->getCcNumber())
	// 	->setCcCid($data->getCcCid())
	// 	->setCcExpMonth($data->getCcExpMonth())
	// 	->setCcExpMonth($data->getCcExpMonth())
	// 	->setCcExpYear($data->getCcExpYear());
	// 	return $this;
	// }
	// public function validate()
	// {
	// 	return true;
	// }

	/** For authorization **/
	public function authorize(Varien_Object $payment, $amount)
	{
		$order = $payment->getOrder();
		$result = $this->drcApi($payment);
		if($result['VoucherTransactionID']) {
			Mage::getSingleton('core/session')->setDrcPaymentVar(1);
			$order->setVoucherTransactionId($result['VoucherTransactionID'])->save();
		} else {
			Mage::getSingleton('core/session')->setDrcPaymentVar(0);
		}
			Mage::getSingleton('core/session')->setPaymentMethosUsed('drc');
	}

	public function drcApi(Varien_Object $payment){
		$order = $payment->getOrder();
		$WSUserName = Mage::getStoreConfig('payment/drc/api_username');
		$WSPassphrase = Mage::getStoreConfig('payment/drc/api_auth');
		$posturl = Mage::getStoreConfig('payment/drc/gateway_url');
		$oid = $order->getId();
		$orderAmount = $order->getGrandTotal();
		$currency = $order->getOrderCurrencyCode();
		$reference = $order->getIncrementId();
		$billingAddress = $order->getBillingAddress();
		$countryCode = $billingAddress->getData('country_id');
		$voucherNumber = $order->getPayment()->getData('voucher_number');
		$voucherCvv = $order->getPayment()->getData('voucher_cvv');
		$voucherExp = $order->getPayment()->getData('voucher_exp');
		$json = array(
			"CorrelationID" => $oid,
			"WSUserName" => $WSUserName,
			"WSPassphrase" => $WSPassphrase,
			"OrderRef" => $reference,
			"VoucherNumber"=> $voucherNumber,
			"Cvv"=>$voucherCvv,
			"ExpirationDate"=>$voucherExp,
			"Amount"=> $orderAmount,
			"TerminalID"=> "7887",
			"TerminalDescription" => "description",
			"TerminalLocation" => $countryCode,
			"TransactionCurrency"=> $currency,
			"PaymentType"=>"100",
			"Notes" =>"order"
			);

		$jsondata = json_encode($json);
		// Mage::log($xmlquerybuild,null,'billpro_request.log');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $posturl);
		curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: application/json"));
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $jsondata);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
		$result = curl_exec($ch);
		curl_close($ch);	


		$dataResult = json_decode($result,true);
		$statusSuccessCode = $dataResult["RedeemVoucherAmountResult"]['StatusCode'];//should be 0 for successfull transaction
		$voucherTransactionID = $dataResult["RedeemVoucherAmountResult"] ['VoucherTransactionID'];
		$statusErrorCode = $dataResult['StatusCode'];//should be 0 for successfull transaction
		if((int)$statusErrorCode > (int)0){
			return array('VoucherTransactionID' => 0 );
		}elseif((int)$statusSuccessCode === (int)0){
			return array('VoucherTransactionID' =>$voucherTransactionID );

		}
	}

}
?>

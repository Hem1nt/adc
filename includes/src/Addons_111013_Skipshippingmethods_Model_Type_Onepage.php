<?php 
class Addons_Skipshippingmethods_Model_Type_Onepage extends Mage_Checkout_Model_Type_Onepage
{
protected $_code = 'Skipshippingmethods';	
	public function saveShippingMethod($shippingMethod)    {
 
	 if(empty($shippingMethod))
			  //$shippingMethod = 'freeshipping_freeshipping';
			  if (empty($shippingMethod)) {
				return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid shipping method.'));
			}
			$rate = $this->getQuote()->getShippingAddress()->getShippingRateByCode($shippingMethod);
			if (!$rate) {
				return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid shipping method.'));
			}
			$this->getQuote()->getShippingAddress()
				->setShippingMethod($shippingMethod);

			$this->getCheckout()
				->setStepData('shipping_method', 'complete', true)
				->setStepData('payment', 'allow', true);

			return array();
		  
	}
	
	public function collectRates(Mage_Shipping_Model_Rate_Request $request)
	{
		if (!Mage::getStoreConfig('carriers/'.$this->_code.'/active')) 
		{
			return false;
		}
	 	$handling = Mage::getStoreConfig('carriers/'.$this->_code.'/handling');
		$result = Mage::getModel('shipping/rate_result');

		foreach ($response as $rMethod) 
		{
		  $method = Mage::getModel('shipping/rate_result_method');
		  $method->setCarrier($this->_code);
		  $method->setCarrierTitle(Mage::getStoreConfig('carriers/'.$this->_code.'/title'));
		  $method->setMethod($rMethod['code']);
		  $method->setMethodTitle($rMethod['title']);
		  $method->setCost($rMethod['amount']);
		  $method->setPrice($rMethod['amount']+$handling);
		  $result->append($method);
		}
	 
		return $result;
	}
	
	public function getAllowedMethods() 
	{
		return array($this->_code => $this->getConfigData('name'));
	}
}
?>
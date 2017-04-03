<?php
class Excellence_Pay_Model_Pay extends Mage_Payment_Model_Method_Abstract
{
	protected $_code = 'pay';
	protected $_formBlockType = 'pay/form_pay';
	protected $_infoBlockType = 'pay/info_pay';
	
	protected $_canUseInternal          = true;
	protected $_canUseCheckout          = true;
}
?>

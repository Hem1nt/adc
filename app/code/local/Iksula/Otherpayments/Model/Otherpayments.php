<?php
class Iksula_Otherpayments_Model_Otherpayments extends Mage_Payment_Model_Method_Abstract
{
	protected $_code = 'otherpayments';
	protected $_formBlockType = 'otherpayments/form_otherpayments';
	protected $_infoBlockType = 'otherpayments/info_otherpayments';
	
	protected $_canUseInternal          = true;
	protected $_canUseCheckout          = true;
}
?>

<?php
class Iksula_Dummypayment_Model_Dummypayment extends Mage_Payment_Model_Method_Abstract
{
	protected $_code = 'dummypayment';
	protected $_formBlockType = 'dummypayment/form_dummypayment';

	//protected $_isGateway               = true;
	protected $_canAuthorize            = true;
	protected $_canCapture              = true;
	//protected $_canCapturePartial       = true;
	protected $_canUseInternal = true; //display in admin place order
	protected $_canRefund               = true;
	protected $_canSaveCc = true; //if made try, the actual credit card number and cvv code are stored in database.

	protected $_order;
}
?>
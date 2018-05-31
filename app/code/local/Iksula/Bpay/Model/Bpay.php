<?php
class Iksula_Bpay_Model_Bpay extends Mage_Payment_Model_Method_Abstract
{
	protected $_code = 'bpay';
	protected $_formBlockType = 'bpay/form_bpay';

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
<?php
class Iksula_Dcrfront_Model_Dcrfront extends Mage_Payment_Model_Method_Abstract
{
	protected $_code = 'virtual_dcrfront';
	protected $_formBlockType = 'dcrfront/form_dcrfront';

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
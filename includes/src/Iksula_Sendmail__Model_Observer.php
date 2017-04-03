<?PHP 	
class Iksula_Sendmail_Model_Observer extends Mage_Adminhtml_Model_Sales_Order
{
	
	  protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }
	
}

 ?>
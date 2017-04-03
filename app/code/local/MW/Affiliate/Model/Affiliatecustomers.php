<?php
class MW_Affiliate_Model_Affiliatecustomers extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('affiliate/affiliatecustomers');
    }
	public function saveCustomerAccount($customerData)
    {    
    	$collection = Mage::getModel('affiliate/affiliatecustomers')->getCollection();
    	$write = Mage::getSingleton('core/resource')->getConnection('core_write');
    	$sql = 'INSERT INTO '.$collection->getTable('affiliatecustomers').'(customer_id,active,payment_gateway,auto_withdrawn,invitation_type,withdrawn_level,reserve_level,total_commission,total_paid,customer_invited,status,referral_code,
    	customer_time,payment_email,bank_name,name_account,bank_country,swift_bic,account_number,re_account_number,referral_site) 
    	VALUES('.$customerData['customer_id'].','.$customerData['active'].",'".$customerData['payment_gateway']."',".$customerData['auto_withdrawn'].','.$customerData['invitation_type'].','.$customerData['withdrawn_level'].','.$customerData['reserve_level']
    	.','.$customerData['total_commission'].','.$customerData['total_paid'].','.$customerData['customer_invited'].','.$customerData['status'].",'".$customerData['referral_code']."','".$customerData['customer_time']
    	."','".$customerData['payment_email']."','".$customerData['bank_name']."','".$customerData['name_account']."','".$customerData['bank_country']."','".$customerData['swift_bic']."','".$customerData['account_number']
    	."','".$customerData['re_account_number']."','".$customerData['referral_site']."')";
    	//var_dump($sql);exit;
    	$write->query($sql);
    }
	
}
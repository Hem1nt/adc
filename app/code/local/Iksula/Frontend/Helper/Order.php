<?php 
class Iksula_Frontend_Helper_Order extends Mage_Core_Helper_Abstract{

	public function _orderCollection()
	{
		return Mage::getModel('sales/order')->getCollection();
	}
	public function getOrdersCountByEmail($customer_email)
	{
		if(!empty($customer_email)){
			$_orders = $this->_orderCollection()->addFieldToFilter('customer_email',$customer_email);                        
			$_orderCount = $_orders->getSize(); 
			return $_orderCount;
		}
	}

	public function getOrdersCountById($customer_id)
	{
		if(!empty($customer_id)){
			$_orders = $this->_orderCollection()->addFieldToFilter('customer_id',$customer_id); 
			$_orderCount = $_orders->getSize(); 
			return $_orderCount;
		}
	}

	public function getGuestOrdersCountByEmail($customer_email)
	{
		if(!empty($customer_email)){
			$_orders = $this->_orderCollection()->addFieldToFilter('customer_email',$customer_email);
			$_orders -> addFieldToFilter('customer_is_guest',1);                        
			$_orderCount = $_orders->getSize(); 
			return $_orderCount;
		}
	}

	public function getCustomersOrdersCount($email){
		$status = Mage::getStoreConfig('order_status/general/selected_status');
		$statusArray = explode(',',$status);
		$_orders = $this->_orderCollection()
				  ->addFieldToFilter('status',array('in' => $statusArray))
				  ->addFieldToFilter('customer_email',$email);
		$_orderCount = $_orders->getSize(); 
		return $_orderCount;

	}
}
?>
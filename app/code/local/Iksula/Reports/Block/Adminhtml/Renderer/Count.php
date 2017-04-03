
<?php

Class Iksula_Reports_Block_Adminhtml_Renderer_Count extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row){

		$params = $this->getRequest()->getParams();
		$fromdate = $params['from_date'];
		$todate = $params['to_date'];
		$customer_email = $row->getData('customer_email');
		$resourceCollection = $this->getModelCollection();

		$_orders = $resourceCollection->addFieldToFilter('customer_email',$customer_email)->addFieldToFilter('created_at', array('from' => $fromdate, 'to' => $todate));
		$_orderCnt = $_orders->count(); //orders count

		return $_orderCnt;
	}

	public function getModelCollection()
    {
        return $resourceCollection = Mage::getModel('sales/order')->getCollection();
    }
}

?>

<?php

class EM_DeleteOrder_Model_Export_Csv extends EM_DeleteOrder_Model_Export_Abstract
{
    const ENCLOSURE = '"';
    const DELIMITER = ',';
  
    public function exportOrders($orders) 
    {
        $fileName = 'custom_order_export_'.date("Ymd_His").'.csv';
        $fp = fopen(Mage::getBaseDir('export').'/'.$fileName, 'w');

        $this->writeHeadRow($fp);
        foreach ($orders as $order) {
            $order = Mage::getModel('sales/order')->load($order);
            $this->writeOrder($order, $fp);
        }

        fclose($fp);

        return $fileName;
    }


    protected function writeHeadRow($fp) 
    {
        fputcsv($fp, $this->getHeadRowValues(), self::DELIMITER, self::ENCLOSURE);
    }


    protected function writeOrder($order, $fp) 
    {
        $common = $this->getCommonOrderValues($order);

        $orderItems = $order->getItemsCollection();
        $itemInc = 0;
		$record = $common;
        fputcsv($fp, $record, self::DELIMITER, self::ENCLOSURE);
    }


    protected function getHeadRowValues() 
    {
        return array(
            'Order #',
            'Custom Order #',
			'Echeck Transactionid',
            'Purchased On',
            'Bill to Name',
			'Shipping Country',
            'Status',
            'Grand Total',
    	);
    }

    protected function getCommonOrderValues($order) 
    {
        $customerObj = Mage::getModel('customer/customer')->loadByEmail($order->getData('customer_email'));


        $shippingAddress = !$order->getIsVirtual() ? $order->getShippingAddress() : null;
        $billingAddress = $order->getBillingAddress();
    
        return array(
            $order->getRealOrderId(),
            $order->getCustomerOrderIncrementId(), //customer order increment id
			$order->getData('echeck_transactionid'),
			$order->getData('created_at'),
			$billingAddress->getName(),
			$shippingAddress ? $shippingAddress->getCountry() : '',
            $order->getStatus(),
            $order->getGrandTotal(),
        );
    }


}
?>
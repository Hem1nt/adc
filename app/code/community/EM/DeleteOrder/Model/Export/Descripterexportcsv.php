<?php

class EM_DeleteOrder_Model_Export_Descripterexportcsv extends EM_DeleteOrder_Model_Export_Abstract
{
    const ENCLOSURE = '"';
    const DELIMITER = ',';
  
    public function exportOrders($orders) 
    {
        $fileName = 'descripter_export_'.date("Ymd_His").'.csv';
        $fp = fopen(Mage::getBaseDir('export').'/'.$fileName, 'w');

        $this->writeHeadRow($fp);
        $writeData = $this->getCustomerOrderCollection($orders);
        foreach ($writeData as $order) {
            $this->writeOrder($order, $fp);
        }

        fclose($fp);
        // exit;
        return $fileName;
    }

    
    public function getCustomerOrderCollection($customerEmail)
    {    
        if(count($customerEmail)>0){
          $orderCollection = Mage::getResourceModel('sales/order_collection')
          ->addFieldToSelect(array('customer_id','dispatcher_message','customer_firstname','customer_lastname','customer_email','created_at','customer_order_increment_id','increment_id'))
          ->addFieldToFilter('customer_email', array('in' => $customerEmail))
          ->setOrder('customer_email', 'desc');
          return $orderCollection;
        }

        return;
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
        // exit();
        fputcsv($fp, $record, self::DELIMITER, self::ENCLOSURE);       
    }


    protected function getHeadRowValues() 
    {
        return array(
          'Order No',
          'Custom Order Id',
          'Customer Name',
          'Dispatcher Message',
          'Created Data',     
          'Shipping Country',     
    	);
    }

    protected function getCommonOrderValues($order) 
    {
        $shippingAddress = !$order->getIsVirtual() ? $order->getShippingAddress() : null;        
        return array(
            $order->getRealOrderId(), //Order Id
            $order->getCustomerOrderIncrementId(), //customer order increment id
            $order->getCustomerName(), //Customer Name
            $order->getDispatcherMessage(), //Dispacther Message
            $order->getCreatedAtDate(), //Created Date
            $shippingAddress ? $shippingAddress->getCountry() : '', //Country
        );
    }
}
?>
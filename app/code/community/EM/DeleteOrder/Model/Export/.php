<?php

class EM_DeleteOrder_Model_Export_Prescriptioncsv extends EM_DeleteOrder_Model_Export_Abstract
{
    const ENCLOSURE = '"';
    const DELIMITER = ',';
  
    public function exportOrders($orders) 
    {
        $fileName = 'prescription_export_'.date("Ymd_His").'.csv';
        $fp = fopen(Mage::getBaseDir('export').'/'.$fileName, 'w');

        $this->writeHeadRow($fp);
        foreach ($orders as $order) {
            $order = Mage::getModel('sales/order')->load($order);
            $this->writeOrder($order, $fp);
        }

        fclose($fp);
        // exit;
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
        foreach ($orderItems as $item)
        {
            if (!$item->isDummy()) {
                $record = array_merge($common, $this->getOrderItemValues($item, $order, ++$itemInc));
                fputcsv($fp, $record, self::DELIMITER, self::ENCLOSURE);
            }
        }
            // exit;
    }


    protected function getHeadRowValues() 
    {
        return array(
          'Order Number',
          'First Name*',
          'Middle Initial',
          'Last Name*',
          'Address*',
          'Address 2',
          'City*',
          'State/Province*',
          'Postal Code*',
          'Country*',
          'Physician',
          'Client Reference',
          'SKU*',
          'Product Name*',
          'Number Of Packs*',
          'Dosing Instructions',
          'Number Of Refills',
          'Patient Shipping Fee',
          'Patient Applicable Tax',
          'Patient Price for All Packs of this Item*',
          'Shipped From',
    	);
    }

    protected function getCommonOrderValues($order) 
    {
        $customerObj = Mage::getModel('customer/customer')->loadByEmail($order->getData('customer_email'));

        $shippingAddress = !$order->getIsVirtual() ? $order->getShippingAddress() : null;
        $billingAddress = $order->getBillingAddress();
    
        return array(
            $order->getRealOrderId(),
            $order->getCustomerFirstname(),
            $order->getCustomerMiddlename(),
			$order->getCustomerLastname(),
            $billingAddress ? $billingAddress->getData("street") : '',
    		$billingAddress ? $billingAddress->getData("street") : '',
            $billingAddress ? $billingAddress->getData("city"): '',
            $billingAddress ? $billingAddress->getRegion(): '',
            $billingAddress ? $billingAddress->getData("postcode"): '',
            $billingAddress ? $billingAddress->getCountry() : '',
			'',
            '',
        );
    }

    protected function getOrderItemValues($item, $order, $itemInc=1) 
    {
        // echo '<pre>';
        // print_r($item->getData());//exit();
        return array(
            $this->getItemSku($item),
            $item->getName(),
            // $this->getItemPackSize($item),
            (int)$item->getQtyOrdered(),
            '',
            '',
            '',
            '',
            $item->getData('row_total'),
            $this->getItemShipFrom($item),
            );
    }


}
?>
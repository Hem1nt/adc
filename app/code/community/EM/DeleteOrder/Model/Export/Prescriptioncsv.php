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
        $record = $common;
        // exit();
        fputcsv($fp, $record, self::DELIMITER, self::ENCLOSURE);       
    }


    protected function getHeadRowValues() 
    {
        return array(
          'Product Code',
          'Customer Ref',
          'Custom Order #',
          'Shipper Name',
          'Consignee Name',
          'Consignee Address 1',
          'Consignee Address 2',
          'Consignee City',
          'Consignee State',
          'Consignee Postal Code',
          'Consignee Country',
          'Currency',
          'Total Declared Value',
          'Generic Goods Description',
          'Detailed Goods Description 1',
          'Detailed Goods Description 2',
          'Detailed Goods Description 3',
          'Detailed Goods Description 4',
          'HS Code 1 ',
          'HS Code 2 ',
          'HS Code 3 ',
          'HS Code 4 ',
          'Quantity 1',
          'Quantity 2',
          'Quantity 3',
          'Quantity 4',
          'Value 1',
          'Value 2',
          'Value 3',
          'Value 4',
          'Country of Origin 1',
          'Country of Origin 2',
          'Country of Origin 3',
          'Country of Origin 4',
          'Item Code 1',
          'Item Code 2',
          'Item Code 3',
          'Item Code 4',
          'Weight (g)',
          'Phone',
          'Tracking'         
    	);
    }

    protected function getCommonOrderValues($order) 
    {
        $currencySymbol = Mage::app()->getStore()->getCurrentCurrencyCode();
        $data = $this->getOrderFirstItemData($order);
        $shippingAddress = !$order->getIsVirtual() ? $order->getShippingAddress() : null;
        $billingAddress = $order->getBillingAddress();
        $streetAddress = explode("\n", $shippingAddress->getData("street"));
        return array(
            '', //product code
            $order->getRealOrderId(), //Customer Ref
            $order->getCustomerOrderIncrementId(), //customer order increment id
            '', //Shipper Name
            $order->getShippingAddress()->getName(), //Consignee Name
            $streetAddress[0], //Consignee Address 1
    		    $streetAddress[1],//Consignee Address 2
            $shippingAddress ? $shippingAddress->getData("city"): '', //Consignee City
            $shippingAddress ? $shippingAddress->getRegion(): '', //Consignee State
            $shippingAddress ? $shippingAddress->getData("postcode"): '', //Consignee Postcode
            $shippingAddress ? $shippingAddress->getCountry() : '', //Consignee Country
		      	$currencySymbol, //Currency
            $data['total_declared_value'], //Total Declared Value
            '',//'Generic Goods Description'
            '',//'Detailed Goods Description 1'
            '',//'Detailed Goods Description 2'
            '',//'Detailed Goods Description 3'
            '',//'Detailed Goods Description 4'
            '',//HS Code 1 
            ' ',//HS Code 2 
            ' ',//HS Code 3 
            ' ',//HS Code 4 
            $data['total_qty'],//Quantity 1
            '',//Quantity 2
            '',//Quantity 3
            '',//Quantity 4
            $data['total_price'],//Value 1
            '',//Value 2
            '',//Value 3
            '',//Value 4
            '',//Country of Origin 1
            '',//Country of Origin 2
            '',//Country of Origin 3
            '',//Country of Origin 4
            $data['sku'],//Item Code 1
            '',//Item Code 2
            '',//Item Code 3
            '',//Item Code 4
            '',//Weight (g)
            '',//Phone
            '' // Tracking        
        );
    }

    protected function getOrderFirstItemData($order) 
    {
      $firstItem = true;
      $items = $order->getAllItems();
      // echo "<pre>";
      $data = array();
      foreach($items as $itemId => $item)
      {  
          // get first item of the order
          if($firstItem && $item->getProductType() == 'simple')
          {
            $firstItem = false;
            $productId = $item->getProductId();
            $_product = Mage::getModel('catalog/product')->load($productId);

            $itemArr = $item->getData();
            $attr = $_product->getResource()->getAttribute("pharmaceutical_form");
            $pharmaceuticalformId = $_product->getPharmaceuticalForm();
            $pharm = $attr->getSource()->getOptionText($pharmaceuticalformId);
            $item_sku = explode("-",$itemArr['sku']);
            $p_packagesize = trim($item_sku[1])." ".$pharm;
            $p_bonus=$_product->getBonus();
            $qty = number_format($item->getQtyOrdered());

            if(empty($p_bonus)) {
              $total_pills = (trim($item_sku[1]) * $qty);
            }
            else {
              $total_pills = (trim($item_sku[1]) * $qty)*2;
            }
            $total_price = Mage::helper('core')->currency($itemArr['row_total_incl_tax'],true,false);
            $data['sku'] = $item->getSku();
            $data['total_qty'] = $total_pills;
            $data['total_price'] = $total_price;
            $data['total_declared_value'] = $total_price;
          }          
      }    
      // print_r($data);  
      // exit;
      return $data;
    }


}
?>
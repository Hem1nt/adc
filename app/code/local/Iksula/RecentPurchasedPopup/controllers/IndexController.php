<?php
class Iksula_RecentPurchasedPopup_IndexController extends Mage_Core_Controller_Front_Action{
    public function getRecentlySoldItemsAction()
    {
      //get recent purchased order with size one
      $storeID = Mage::app()->getStore()->getId();
      $now = Mage::getModel('core/date')->timestamp(time());
      $dateStart = date('Y-m-d' . ' 00:00:00', $now);
      $dateEnd = date('Y-m-d' . ' 23:59:59', $now);
      $itemsCollection = Mage::getResourceModel('sales/order_item_collection')
      ->join('order', 'order_id=entity_id')
      ->addFieldToFilter('main_table.store_id', array('eq'=>$storeID))
      ->addFieldToFilter('main_table.created_at', array('from' => $dateStart, 'to' => $dateEnd))
      ->setOrder('main_table.created_at','desc')
      ->setPageSize(1);

      //check if customer is loggedIn
      if(Mage::getSingleton('customer/session')->isLoggedIn()){
        $customerLoggedIn = true;
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $customerId = $customer->getId(); //get Customer Id
      }

      $html = "";
      //$itemsCollection->getSelect()->group(`main_table`.'product_id');
        if(sizeof($itemsCollection)>0)
        {
          foreach ($itemsCollection as $item) {
            $orderId = $item->getData('order_id');

            //Get the order details based on the order id ($orderId)
            $order = Mage::getModel('sales/order')->load($orderId);

            //hide popup if order placed from active customer
            if($customerLoggedIn && $order->getData('customer_id') == $customerId){
              echo json_encode(array('success'=>'false','customer'=>'active'));exit;
            }

            // Get the id of the orders shipping address
            $shippingId = $order->getShippingAddress()->getId();

            // Get shipping address data using the id
            $address = Mage::getModel('sales/order_address')->load($shippingId);

            //get product details
            $productId = $item->getData('product_id');
            $_product = Mage::getModel('catalog/product')->load($productId);
            $parentIds = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($_product->getId());
          }

          //prepare html
          if(Mage::getSingleton('core/session')->getData('orderid') != $orderId){
              $html .= "<div class='recent'>";
              if(isset($parentIds[0])){
                $parentProduct = Mage::getModel('catalog/product')->load($parentIds[0]);
                $html  .= "<div class='showProducts_img'><a href='".Mage::getBaseurl().$parentProduct->getData('url_path')."'><img src=".Mage::helper('catalog/image')->init($parentProduct, 'image')->resize(80,80)."></a></div>";
              }

              $html .= "<div class='showProducts_desp'>";
              $html .= "<b>Someone in ".$address->getData('city')." , ".$address->getData('region')."</b>";
              $html .= "<span> recently purchased</span> "."<span class='showProducts_name'>"."<a href='".Mage::getBaseurl().$parentProduct->getData('url_path')."'>".$item->getData('name')."</a></span>";
              $html .= "</div>";
              $html .= "</div>";
              Mage::getSingleton('core/session')->setData('orderid',$orderId);
              echo json_encode(array('success'=>'true','data'=>$html));
          }else{
            echo json_encode(array('success'=>'false','order'=>'same order'));
          }
      }else{
        echo json_encode(array('success'=>'false'));
      }
      
    }
}
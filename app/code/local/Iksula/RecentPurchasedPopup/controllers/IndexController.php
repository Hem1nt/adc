<?php
class Iksula_RecentPurchasedPopup_IndexController extends Mage_Core_Controller_Front_Action{
    public function getRecentlySoldItemsAction()
    {
      //get recent purchased order with size one
      $storeID = Mage::app()->getStore()->getId();
      $now = Mage::getModel('core/date')->timestamp(time());
      $flag = false;
      //$dateStart = date('Y-m-d' . ' 00:00:00', $now);
      //$dateEnd = date('Y-m-d' . ' 23:59:59', $now);
      /*$itemsCollection = Mage::getResourceModel('sales/order_item_collection')
      ->join('order', 'order_id=entity_id')
      ->addFieldToFilter('main_table.store_id', array('eq'=>$storeID))
      //->addFieldToFilter('main_table.created_at', array('from' => $dateStart, 'to' => $dateEnd))
      ->setOrder('main_table.created_at','desc')
      ->setPageSize(1);*/
      if(Mage::getModel('recentpurchasedpopup/recentpurchasedpopup')->load(1)){
        $itemsCollection = Mage::getModel('recentpurchasedpopup/recentpurchasedpopup')->load(1);
        $flag = true;
      }

      //check if customer is loggedIn
      if(Mage::getSingleton('customer/session')->isLoggedIn()){
        $customerLoggedIn = true;
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $customerId = $customer->getId(); //get Customer Id
      }

      $html = "";
      //$itemsCollection->getSelect()->group(`main_table`.'product_id');
        if($flag)
        {
          $orderId = $itemsCollection->getData('order_id');

          //hide popup if order placed from active customer
          if($itemsCollection->getData('customer_id')){
            $customerID = $itemsCollection->getData('customer_id');
            $customer = Mage::getModel('customer/customer')->load($customerID);
            if($customerLoggedIn == true && $customerID == $customerId){
              echo json_encode(array('success'=>'false','customer'=>'active'));
              return;
            }
          }

          // Get the id of the orders shipping address
          $shippingId = $itemsCollection->getData('shipping_id');

          // Get shipping address data using the id
          $address = Mage::getModel('sales/order_address')->load($shippingId);

          //get product details
          $_sku = $itemsCollection->getData('product_sku');
          $_catalog = Mage::getModel('catalog/product');
          $productId = $_catalog->getIdBySku($_sku);
          $_product = Mage::getModel('catalog/product')->load($productId);
          $parentIds = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($_product->getId());
          

          //prepare html
          if(Mage::getSingleton('core/session')->getData('orderid') != $orderId){
              $html .= "<div class='recent'>";
              $parentProduct = Mage::getModel('catalog/product')->load($parentIds[0]);
              $html  .= "<div class='showProducts_img'><a href='".Mage::getBaseurl().$parentProduct->getData('url_path')."'><img src=".Mage::helper('catalog/image')->init($parentProduct, 'image')->resize(80,80)."></a></div>";

              $html .= "<div class='showProducts_desp'>";
              $html .= "<b>Someone in ".$address->getData('city')." , ".$address->getData('region')."</b>";
              $html .= "<span> recently purchased</span> "."<span class='showProducts_name'>"."<a href='".Mage::getBaseurl().$parentProduct->getData('url_path')."'>".$_product->getData('name')."</a></span>";
              $html .= "</div>";
              $html .= "</div>";
              Mage::getSingleton('core/session')->setData('orderid',$orderId);
              echo json_encode(array('success'=>'true','data'=>$html));
              return;
          }else{
            echo json_encode(array('success'=>'false','order'=>'same order'));
            return;
          }
      }else{
        echo json_encode(array('success'=>'false'));
        return;
      }
      
    }
}
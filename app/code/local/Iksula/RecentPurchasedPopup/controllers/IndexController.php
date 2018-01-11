<?php
class Iksula_RecentPurchasedPopup_IndexController extends Mage_Core_Controller_Front_Action{
    public function getRecentlySoldItemsAction()
    {
      $storeID = Mage::app()->getStore()->getId();
      $now = Mage::getModel('core/date')->timestamp(time());
      $dateStart = date('Y-m-d' . ' 00:00:00', $now);
      $dateEnd = date('Y-m-d' . ' 23:59:59', $now);
      $itemsCollection = Mage::getResourceModel('sales/order_item_collection')
      ->join('order', 'order_id=entity_id')
      ->addFieldToFilter('main_table.store_id', array('eq'=>$storeID))
      ->addFieldToFilter('`main_table`.created_at', array('from' => $dateStart, 'to' => $dateEnd))
      ->setOrder('main_table.created_at','desc')
      ->setPageSize(1);
      $html = "";
      $itemsCollection->getSelect()->group(`main_table`.'product_id');
        if(sizeof($itemsCollection)>0)
        {
          foreach ($itemsCollection as $item) {
            $orderId = $item->getData('order_id');
            //Get the order details based on the order id ($orderId)
            $order = Mage::getModel('sales/order')->load($orderId);

            // Get the id of the orders shipping address
            $shippingId = $order->getShippingAddress()->getId();

            // Get shipping address data using the id
            $address = Mage::getModel('sales/order_address')->load($shippingId);

            //get product details
            $productId = $item->getData('product_id');
            $_product = Mage::getModel('catalog/product')->load($productId);
            $parentIds = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($_product->getId());
            $html .= "<div class='recent'>";
            if(isset($parentIds[0])){
              $parentProduct = Mage::getModel('catalog/product')->load($parentIds[0]);
              $html  .= "<div class='showProducts_img'><img src=".Mage::helper('catalog/image')->init($parentProduct, 'image')->resize(80,80)."></div>";
            }
            /*if($_product->getThumbnail() != 'no_selection'){
              
            }*/
            $html .= "<div class='showProducts_desp'>";
            $html .= "<b>Someone in ".$address->getData('region')." , ".$address->getData('city')."</b>";
            $html .= "<span> recently purchased</span> "."<span class='showProducts_name'>".$item->getData('name')."</span>";
            $html .= "</div>";
            $html .= "</div>";
          }

          echo $html;
      }
      
    }
}
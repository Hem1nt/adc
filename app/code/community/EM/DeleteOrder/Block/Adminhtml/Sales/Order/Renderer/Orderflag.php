<?php

class EM_DeleteOrder_Block_Adminhtml_Sales_Order_Renderer_Orderflag extends  Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
          $ipaddress = $row->getRemoteIp();
          // $_order= Mage::getModel('sales/order')->load($row->getId());
          $order_placed_country = $row->getData('order_placed_country');


          // $billing_address = $row->getBillingAddress();
          $shipping_address = $row->getShippingAddress();

          // $billing_country = $row->getData('country_id');
          $shipping_country = $row->getData('country_id');
          $agent_name = $row->getData('agent_name');

          if($shipping_country != $order_placed_country && !empty($order_placed_country) && empty($agent_name)){
              $country_name = Mage::app()->getLocale()->getCountryTranslation($order_placed_country);
              $order_placed_country = strtolower($order_placed_country);
              $url = Mage::getBaseUrl('media').'flags/'. $order_placed_country .'.png';
              $html = '<img src="'.$url.'" alt="Order Tags" height="25" width="25"></br>'.$country_name;

              return $html;
          }

          // $countryID = Mage::getStoreConfig('custom_snippet/true_client/country_dropdown');
          // if(!empty($order_placed_country)){
          //   $countryArrayID = explode(",", $countryID);
          //   if (in_array($order_placed_country, $countryArrayID)) {
          //     $url = Mage::getBaseUrl('media').'theme/'. Mage::getStoreConfig('custom_snippet/true_client/country_logo');
          //     $html = '<img src="'.$url.'" alt="Order Tags" height="25" width="25">';
          //     return $html;
          //   }else{
          //     return ;
          //   }
          // }


          return;
    }
}
?>

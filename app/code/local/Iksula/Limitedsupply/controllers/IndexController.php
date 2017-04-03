<?php
class Iksula_Limitedsupply_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {

      $product_sku = $_POST['product_sku'];
      $product_name = $_POST['product_name'];
      $subscription_email = $_POST['subscription_email'];
      $subscription_name = $_POST['subscription_name'];
      $subscription_phone = $_POST['subscription_phone'];
      $subscription_quantity = $_POST['subscription_quantity'];
      $subscription_comment = $_POST['subscription_comment'];
      $date = Mage::getModel('core/date')->date('Y-m-d H:i:s');
      $url = Mage::getUrl().''.$_POST['product_url'];

     if(isset($subscription_email)&&($subscription_email!=''))
     {
        $limitedsubscription = Mage::getModel('limitedsupply/limitedsupply');
        $limitedsubscription->setData('product_sku', $product_sku);
        $limitedsubscription->setData('product_name', $product_name);
        $limitedsubscription->setData('email', $subscription_email);
        $limitedsubscription->setData('name', $subscription_name);
        $limitedsubscription->setData('phone_no', $subscription_phone);
        $limitedsubscription->setData('quantity', $subscription_quantity);
        $limitedsubscription->setData('comment', $subscription_comment);
        $limitedsubscription->setData('date', $date);
        $limitedsubscription->setData('is_active', "active");
        $limitedsubscription->save();
      // $this->_redirect($url);
     }

    else {
       $this->_redirect('');
    }

      $senderEmail = $subscription_email;
      $sender = array(
        'name' => 'customer',
        'email' => $senderEmail
      );

      // Set recepient information
      $recepientEmail = Mage::getStoreConfig('custom_snippet/limitedsupply_tabs/limitedsupply_email');
      if(empty($recepientEmail)){
        $recepientEmail = 'david@alldaychemist.com';
      }
      // print_r($recepientEmail); exit;
      $recepientName = 'Derricwood';

      $templateId = 51;
      // Get Store ID
      $storeId = Mage::app()->getStore()->getId();



      // Set variables that can be used in email template
      $vars = array('limited_product_name' => $product_name,
                    'limited_product_url' => $url,
                    'limited_email' => $subscription_email,
                    'limited_supply_quantity' => $limited_supply_quantity,
                    'limited_comment' => $subscription_comment,
                    'limited_product_sku' => $product_sku,
                    'limited_subscription_name' => $subscription_name,
                    'limited_product_quantity' => $subscription_quantity
                  );


      $translate  = Mage::getSingleton('core/translate');

      // Send Transactional Email
      Mage::getModel('core/email_template')
          ->sendTransactional($templateId, $sender, $recepientEmail, $recepientName, $vars, $storeId);

      $translate->setTranslateInline(true);

    }
}

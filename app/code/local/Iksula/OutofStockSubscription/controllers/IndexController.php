<?php
class Iksula_OutofStockSubscription_IndexController extends Mage_Core_Controller_Front_Action
{
      public function indexAction()
       {

       $product_sku = $_POST['product_sku'];
       $product_id = $_POST['product_id'];
       $product_name = $_POST['product_name'];
       $subscription_email = $_POST['subscription_email'];
       $date = Mage::getModel('core/date')->date('Y-m-d H:i:s');
       $url = $_POST['product_url'];
       $productModel = Mage::getModel('catalog/product');
       if($_POST['product_status'] == '')
       {
               $product_status = "outofstock";
       }else {
        	$productData = $productModel->load($product_id);
        	$customStockStatus = $productData->getCustomStockStatus();
            $attributeText = $productModel->getResource()->getAttribute('custom_stock_status');
			$attributeLabel = $attributeText->getSource()->getOptionText($customStockStatus);
       }
       if($attributeLabel == 'Coming Soon'){
       		$attributeLabel = 'comingsoon';
       }elseif($attributeLabel == 'Discontinued'){
       		$attributeLabel = 'discontinued';
       }



          if(isset($subscription_email)&&($subscription_email!=''))
          {
             $outofstocksubscription = Mage::getModel('outofstocksubscription/info');
             $outofstocksubscription->setData('product_sku', $product_sku);
             $outofstocksubscription->setData('product_name', $product_name);
             $outofstocksubscription->setData('email', $subscription_email);
             $outofstocksubscription->setData('notification_type', $attributeLabel);
             $outofstocksubscription->setData('date', $date);
             $outofstocksubscription->setData('is_active', "active");
             $outofstocksubscription->save();
                 // $this->_redirect($url);
          }

               else {
                        $this->_redirect('');
               }

}

    protected function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }

 //    public function deleteAction()
    // {
       //      $model = Mage::getModel('outofstocksubscription/info')->getCollection();
       //      try {
       //          $model->addFieldToFilter('is_active','deactive');
       //          $model->walk('delete');
       //          echo "Data deleted successfully.";

       //      } catch (Exception $e){
       //          echo $e->getMessage();
       //      }
       // }

}
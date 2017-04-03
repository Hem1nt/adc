<?php

/**
 * @category    BusinessKing
 * @package     BusinessKing_OutofStockSubscription
 */
class Iksula_OutofStockSubscription_Model_Observer
{
	const OUTOFSTOCKSUBSCRIPTION_MAIL_TEMPLATE = 'outofstock_subscription';

	public function sendEmailToOutofStockSubscription($observer)
    {

        $product = $observer->getEvent()->getProduct();
		if ($product) {
			if ($product->getStockItem()) {
				$stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getId());

				$isInStock = $stockItem->getIsInStock();

			    if ($isInStock>=1) {


			    	$model = Mage::getModel('outofstocksubscription/info')->getCollection();
			    	$product_sku = $product->getData('sku');


					$filtermodel = $model->addFieldToFilter('product_sku',$product_sku);
					$ids = $filtermodel->getData();

					$product_data = array();
					foreach($ids as $value) {
						$id = $value['id'];
						$data_model = Mage::getModel('outofstocksubscription/info')->load($id);

						// print_r($data_model->getData());

						$product_data = $data_model->getData();
						// print_r($data_model->getData());

					//$prodUrl = $product->getProductUrl();
					$prodUrl = Mage::getBaseUrl();
					$prodUrl = str_replace("/index.php", "", $prodUrl);
					$prodUrl = $prodUrl.$product->getData('url_path');


						if($product_data['is_active'] == 'active') {

			    			if($product_data['notification_type'] == 'outofstock') {
			    				$notification_notice = "This is to inform you that the ".$product_data['product_name']." product is back in stock, so you can purchase this product now.";
			    			} else {
			    				$notification_notice = "This is to inform you that the ".$product_data['product_name']." product is now not present in our store.";
			    			}
			    			$senderName = Mage::getStoreConfig('trans_email/ident_general/name');
			    			$senderEmail = Mage::getStoreConfig('trans_email/ident_general/email');
						    // exit();
						    $sender = array(
						    			'name' => $senderName,
						                'email' => $senderEmail
						                );

						    // Set recepient information
						    $image = $product->getImageUrl();

						    $recepientEmail = $product_data['email'];
						    $recepientName = 'User';

						    $templateId = 42;
						    // Get Store ID
						    $storeId = Mage::app()->getStore()->getId();



						    // Set variables that can be used in email template
						    $vars = array('product_name'     => $product_data['product_name'],
					                	'product_url' => $prodUrl,
					                	'notification_notice' => $notification_notice,
					                	'product_image' => $image
					                	);


						    $translate  = Mage::getSingleton('core/translate');

						    // Send Transactional Email
						    Mage::getModel('core/email_template')
						        ->sendTransactional($templateId, $sender, $recepientEmail, $recepientName, $vars, $storeId);

						    $translate->setTranslateInline(true);

						    if($product_data['is_active'] == 'active') {
								$data_model->setIsActive('deactive');
								$data_model->save();
							}
						}
					}
		    		// exit;
			    }
			}
		}
        // return $this;
    }

}

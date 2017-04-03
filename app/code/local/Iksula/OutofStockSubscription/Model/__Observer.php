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



				    // if($product->getTypeId() == 'configurable') {
	       //               $childIds = Mage::getModel('catalog/product')->getIdBySku($sku);
	       //               $parent_ids = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($childIds);
	       //               $product_sku = Mage::getResourceModel('catalog/product')->getProductsSku($parent_ids);
	       //           } else if($product->getTypeId() == 'bundle') {
	       //               $childIds = Mage::getModel('catalog/product')->getIdBySku($sku);
	       //               $parent_ids = Mage::getModel('bundle/product_type')->getParentIdsByChild($childIds);
	       //               $product_sku = Mage::getResourceModel('catalog/product')->getProductsSku($parent_ids);
	       //           } else {
	       //               $product_sku = $sku;
	       //           }

					$filtermodel = $model->addFieldToFilter('product_sku',$product_sku);
					$ids = $filtermodel->getData();

					$product_data = array();
					foreach($ids as $value) {
						$id = $value['id'];
						$data_model = Mage::getModel('outofstocksubscription/info')->load($id);
						// print_r($data_model->getData());
						if($value['notification_type'] == 'active') {
							$data_model->setIsActive('deactive');
							$data_model->save();
						}
						$product_data = $data_model->getData();
						// print_r($data_model->getData());
					}


					//$prodUrl = $product->getProductUrl();
					$prodUrl = Mage::getBaseUrl();
					$prodUrl = str_replace("/index.php", "", $prodUrl);
					$prodUrl = $prodUrl.$product->getData('url_path');

		    		foreach ($product_data as $models) {
		    			// Set sender information
					    // /$senderName = Mage::getStoreConfig('trans_email/ident_support/name');
					    //$senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
		    			if($models['notification_type'] == 'outofstock') {
		    				$notification_notice = "This is to inform you that the ".$models['product_name']." product is back in stock, so you can purchase this product now.";
		    			} else {
		    				$notification_notice = "This is to inform you that the ".$models['product_name']." product is now not present in our store.";
		    			}

					    $sender = array('name' => '$senderName',
					                'email' => 'avinash@iksula.com');

					    // Set recepient information
					    $recepientEmail = $models['email'];
					    $recepientName = 'all day chemist';

					    $templateId = 41;
					    // Get Store ID
					    $store = Mage::app()->getStore()->getId();

					    // Set variables that can be used in email template
					    $vars = array('product'     => $models['product_name'],
				                	'product_url' => $prodUrl,
				                	'notification_notice' => $notification_notice
				                	);

					    $translate  = Mage::getSingleton('core/translate');

					    // Send Transactional Email
					    Mage::getModel('core/email_template')
					        ->sendTransactional($templateId, $sender, $recepientEmail, $recepientName, $vars, $storeId);

					    $translate->setTranslateInline(true);

		    		}
			    }
			}
		}
        // return $this;
    }

}

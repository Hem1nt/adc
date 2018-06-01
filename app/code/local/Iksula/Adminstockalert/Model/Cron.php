<?php
class Iksula_Adminstockalert_Model_Cron{	
	public function notifyAdmin(){

		$store = Mage::app()->getStore();
		$isActive = Mage::getStoreConfig('notifyadmin_sec/notifyadmin_group/is_running',$store);
		if($isActive){
			$defaultValue =  Mage::getStoreConfig('cataloginventory/item_options/notify_stock_qty',$store);
			$orderCollection = Mage::getModel('cataloginventory/stock_item')->getCollection()->addFieldToFilter('qty', array('lteq' => $defaultValue))->addFieldToFilter('is_in_stock', array('eq' => 1))->addFieldToFilter('type_id', array('eq' => 'simple'));
			if(!empty($orderCollection->getData())){
			    foreach ($orderCollection as $key => $value) {
			        $product = Mage::getModel('catalog/product')->load($value['product_id']);
			        $data[] = $product->getSku();
			    }    
			}

			if(!empty($data)){
				$filename = Mage::getBaseDir()."/media/notify_stock_qty.csv";
				$f = fopen($filename, 'w+');
				fwrite($f, 'SKU');
				foreach ($data as $line) {
				    fwrite($f, PHP_EOL);
				    fwrite($f, $line); 
				}
				fclose($f, 0);

				$mail = new Zend_Mail('utf-8');

				$recipients = array('Kundan - Derric Wood' => Mage::getStoreConfig('notifyadmin_sec/notifyadmin_group/recipient_email',$store));
				$mailBody   = Mage::getStoreConfig('notifyadmin_sec/notifyadmin_group/mail_body',$store);
				$mail->setBodyHtml($mailBody)
				    ->setSubject('Minimum Stock Quantity CSV')
				    ->addTo($recipients)
				    ->setFrom(Mage::getStoreConfig('trans_email/ident_support/email'), Mage::getStoreConfig('trans_email/ident_support/name'));

				$file  = Mage::getBaseDir()."/media/notify_stock_qty.csv";
				$attachment = file_get_contents($file);
				$mail->createAttachment(
				    $attachment,
				    Zend_Mime::TYPE_OCTETSTREAM,
				    Zend_Mime::DISPOSITION_ATTACHMENT,
				    Zend_Mime::ENCODING_BASE64,
				    'notify_stock_qty.csv'
				);

				try {
				    $mail->send();
				} catch (Exception $e) {
				    Mage::logException($e);
				}	
			}
			
		}
	} 
}
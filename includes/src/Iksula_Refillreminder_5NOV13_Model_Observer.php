<?php
class Iksula_Refillreminder_Model_Observer {
	
	public function checkProductInReminder(Varien_Event_Observer $observer) {

		$order = $observer->getEvent()->getOrder(); //observer event
		//print_r($order);
		$mail = $order->getData('customer_email'); //get mail from order event
		
		$model = Mage::getModel('refillreminder/refillreminder'); //model to process reminder checking data
		foreach($order->getAllItems() as $item) {
			//echo $item->getSku();
			$collection = $model->getCollection();
			$collection->addFieldToFilter('product_sku', $item->getSku());
			$collection->addFieldToFilter('customer_email', $mail);
			$collection->addFieldToFilter('remind_flag', 0);
			
			if($collection->getSize()) { // process if count is not zero
				$ReminderData = $collection->getData();
				$data = array('remind_flag'=>1); //primary key of reminder table which will update
				$UpdateModel = $model->load()->addData($data);
				try {
					#echo $ReminderData[0]['reminder_id']."<br/>";
					$UpdateModel->setId($ReminderData[0]['reminder_id'])->save();
					//echo "Data has been updated successfully.";
				} catch (Exception $e){
					//echo $e->getMessage();
				}
			}
		}

	}	
}

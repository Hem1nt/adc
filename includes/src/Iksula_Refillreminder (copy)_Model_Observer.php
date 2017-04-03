<?php
class Iksula_Refillreminder_Model_Observer {
	
	public function checkProductInReminder(Varien_Event_Observer $observer) {
		
		$order = $observer->getEvent()->getOrder(); //observer event
		// exit;
		//print_r($order);
		$mail = $order->getData('customer_email'); //get mail from order event
		$preferableTimeToCall = Mage::getSingleton('core/session')->getTimetocall();
		Mage::getSingleton('core/session')->unsTimetocall();
		if(Mage::getSingleton('core/session')->getOrderReminder()) {
			$model = Mage::getModel('refillreminder/orderreminder'); //model to process reminder checking data
		} else {
			$model = Mage::getModel('refillreminder/refillreminder'); //model to process reminder checking data
		}
		$order_id = $order->getIncrementId();
		
		if(Mage::getSingleton('core/session')->getLatestEntry()) {
			$data = array('order_total' => $order->getGrandTotal(),'order_id'=>$order_id, 'remind_flag'=>1, 'time_to_call' => $preferableTimeToCall); //primary key of reminder table which will update
			$UpdateModel = $model->load()->addData($data);
				try {
					$UpdateModel->setId(Mage::getSingleton('core/session')->getLatestEntry())->save();
					Mage::getSingleton('core/session')->unsLatestEntry();
					//echo "Data has been updated successfully.";
				} catch (Exception $e){
					//echo $e->getMessage();
				}
		}
		foreach($order->getAllItems() as $item) {
				$collection = $model->getCollection();
				$collection->addFieldToFilter('product_sku', $item->getSku());
				$collection->addFieldToFilter('customer_email', $mail);
				$collection->addFieldToFilter('remind_flag', 0);
				//echo $collection->getSelect(); //print_r($collection->getData());
				if($collection->getSize()) { // process if count is not zero
					
					$ReminderData = $collection->getData();
					
					$data = array('remind_flag'=>1, 'time_to_call' => $preferableTimeToCall);
					$UpdateModel = $model->load()->addData($data);
					try {
						$UpdateModel->setId($ReminderData[0]['reminder_id'])->save();
						//echo "Data has been updated successfully.";
					} catch (Exception $e){
						//echo $e->getMessage();
					}
				}
			}
		 
		Mage::getSingleton('core/session')->unsOrderReminder();// exit;
		
		/*$customer_email = $mail;
		if($customer_email) {
			$customer = Mage::getModel("customer/customer");
			$customer->setWebsiteId(Mage::app()->getWebsite()->getId());
			$customer->loadByEmail($customer_email); //load customer by email id
			$name = "User";
			$customerData = $customer->getData();
			// $this->CheetaApi($email, $eid, $customerData['firstname'], $customerData['lastname']);     uncomment this for cheeta api call
	    }*/

	}
	public function FireCheetaSubscribe(Varien_Event_Observer $observer) {
		$subscriber = $observer->getEvent()->getSubscriber();
	    $email = $subscriber->getEmail();		

		$cheetahIds = explode("#", Mage::getModel('core/variable')->loadByCode('cheetah_newsletter')->getValue('plain'));
		$eid = $cheetahIds[0];
		$aid = $cheetahIds[1];
		$callOfferModel = Mage::getModel('callforoffer/callforoffers');
		$customerName = $callOfferModel->getUserName($email);
		$callOfferModel->CheetaApi($email, $eid, $aid, $customerName['firstname'], $customerName['lastname']);
		//exit;
	}
	
}

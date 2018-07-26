<?php
class Manoj_Abandoned_Model_Observer
{
	public function removeentry(Varien_Event_Observer $observer)
	{				
		$event = $observer->getEvent();
		$order = $event->getOrder(); 
		$abandonedcollection = Mage::getModel('abandoned/abandoned');
		$abandonedordermodel = Mage::getModel('abandoned/abandonedorder');
		$custid = $order->getCustomerId();
		$order_num = $order->getIncrementId();
		$customer_email = $order->getCustomerEmail();



		$abandonedcartCollection = Mage::getModel('abandoned/abandoned')->getCollection();
		$abandonedcartCollection->addFieldToFilter('email_id',$customer_email);
		    // exit;
		$dataCollection = $abandonedcartCollection->getData();
		foreach ($dataCollection as $value) {
			$cartid = $value['abandoned_cart_id'];
		}
		if(count($dataCollection)>0){        
			$abandonedcollection->load($cartid);
			//$abandonedcollection->setData('is_purchase',1)->save();
			$abandonedcollection->delete();


			$abandonedordermodel->setData('order_number',$order_num);
			$abandonedordermodel->setData('email_id',$customer_email);
			$abandonedordermodel->setData('created_time',date("Y-m-d H:i:s"));
			$abandonedordermodel->save();



		}
	}
		
}

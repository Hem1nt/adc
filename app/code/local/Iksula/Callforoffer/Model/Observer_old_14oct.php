<?php
class Iksula_Callforoffer_Model_Observer
{

			public function callforfreeregistration(Varien_Event_Observer $observer)
			{
				//Mage::dispatchEvent('admin_session_user_login_success', array('user'=>$user));
				//$user = $observer->getEvent()->getUser();
				//$user->doSomething();
				$event = $observer->getEvent();
				$order = $event->getOrder(); 
				$orderId = $order->getRealOrderId();
				$collection = Mage::getModel('callforoffer/callforoffers');
				$callforfreeval=Mage::getSingleton('core/session')->getCallforfreeval();
				Mage::getSingleton('core/session')->unsCallforfreeval();
				$custid = $order->getCustomerId();

		        //save callforoffers value in database
		       // $order->setCallforoffers($callforfreeval);
				$order->setData('callforoffers',$callforfreeval);
				$order->save();
				// print_r($order->getData('callforoffers'));
				// exit;
				$name = ucwords($order->getShippingAddress()->getName());
				$customertelephoneno = $order->getShippingAddress()->getTelephone();
				$callforofferscollection =$collection->getCollection();
				$callforofferscollection->addFieldToFilter('customerid',$custid);
				$callforofferscollection->addFieldToFilter('customertelephoneno',$customertelephoneno);
				$cfo_id =$callforofferscollection->getData('id');
				if($custid!=''){
					$customertype = 0;
				}
				else{
					$customertype = 1;
				}

        //if user unsubscribe the offer ser as No in Database table 
				if($cfo_id[0]['status']!=$callforfreeval){
					$newcollection = $collection->load($cfo_id[0]['id']);
					$newcollection->setData('status',$callforfreeval);
					$newcollection->save();
				}

        //set value for the call for offers in callforfree table
				if(count($callforofferscollection->getData())==0){
					if($callforfreeval=='1'){
						$collection->setData('cust_name',$name);
						$collection->setData('customerid',$custid);
						$collection->setData('customertelephoneno',$customertelephoneno);
						$collection->setData('status',$callforfreeval);
						$collection->setData('customertype',$customertype);
						$collection->save();
					}
				}        
        //set value for the call for offers in customer section
				if($custid){
					$customerObj = Mage::getModel("customer/customer")->load($custid);
					if($callforfreeval==0){
						$callforoffer = '1';
					}
					else{
						$callforoffer = '2';
					}
					$customerObj->setData('callforoffers',$callforoffer);
					$customerObj->save();
				}


			}
		
}

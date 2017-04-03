<?php
Class Iksula_Overrides_MethodController extends Mage_Core_Controller_Front_Action{
	
	public function changepaymentAction(){
		$quote = Mage::getSingleton("checkout/session")->getQuote();
		$paymentCode = $this->getRequest()->getPost('data');
        // $quote = Mage::getSingleton('checkout/session')->getQuote();
		
		if ($quote->isVirtual()) {
		    $quote->getBillingAddress()->setPaymentMethod($paymentCode);
		} else {
		    $quote->getShippingAddress()->setPaymentMethod($paymentCode);
		}
		// $quote->setTotalsCollectedFlag(false)->collectTotals();
		$quote->save();
        $response['Status'] = (int)1;
        return $this->getResponse()->setBody(json_encode($response));
	}

	public function changegroupAction(){
		$currentTime = Mage::getModel('core/date')->date('Y-m-d H:i:s', strtotime('3 hour'));
		$previousTime = Mage::getModel('core/date')->date('Y-m-d H:i:s', strtotime('4 hour'));
		$regularGroupId = 6;
		$generalGroupId = 1;
		$time =  Mage::getModel('core/date')->date('Y-m-d H:i:s');
		$order_items = Mage::getModel('sales/order')
					  ->getCollection()
				      ->addAttributeToFilter('created_at', array('from' => $currentTime, 'to' => $previousTime));
		Mage::log($time.'-----------'.$currentTime.'-------------------'.$previousTime,null,'crontime.log');
		$data = $order_items->getData();
		$customerModel = Mage::getModel('customer/customer');

		foreach ($data as $value) {
			$customerEmail = $value['customer_email'];
			$customerId = $value['customer_id'];
			$_customer = $customerModel->load($customerId);
			$orderHelper = Mage::helper('frontend/order');
			$customersOrdersCount = $orderHelper->getCustomersOrdersCount($customerEmail);
			$orderCollection = $orderHelper->_orderCollection();
		    $orderCollection->addFieldToFilter('customer_email',$customerEmail);

			Mage::log($customerEmail,null,'cronJob.log');
			Mage::log($customersOrdersCount.'------',null,'cronOrderCount.log');
		    if($customerId){
		    	if($customersOrdersCount >= 2){
		            foreach ($orderCollection as $_order) {
		                $_order->setCustomerGroupId($regularGroupId);
		                $_order->save();
		            }

		            $_customer->setGroupId($regularGroupId);
		            $_customer->save();

		        }elseif($customersOrdersCount == 1){
		            foreach ($orderCollection as $_order) {
		                $_order->setCustomerGroupId($generalGroupId);
		                $_order->save();
		            }

		            $_customer->setGroupId($generalGroupId);
		            $_customer->save(); 

		        }
		        
		    }else{
		    	if($customersOrdersCount == 1){
		            foreach ($orderCollection as $_order) {
		                $_order->setCustomerGroupId($generalGroupId);
		                $_order->save();
		            }

		        }elseif($customersOrdersCount >= 2){
		            foreach ($orderCollection as $_order) {
		                $_order->setCustomerGroupId($regularGroupId);
		                $_order->save();
		            } 

		        }
		    }
		}
	}


	public function changeGroupAllAction(){	

		// echo $lastweek = date('Y-m-d', strtotime("-1 year"));
		$fromdate = date('Y-m-d', strtotime("-1 year"));
		$todate = date('Y-m-d', strtotime("-11 month"));
		$status = Mage::getStoreConfig('order_status/general/selected_status');
		$statusArray = explode(',',$status);
		
		$regularGroupId = 6;
		$generalGroupId = 1;
		$orderHelper = Mage::helper('frontend/order');
		$orderCollection = $orderHelper->_orderCollection();
		$order_items = $orderHelper->_orderCollection();
	    $order_items->addFieldToFilter('created_at', array('from'  => $lastyear,'to'  => $todate));
	    $order_items->addFieldToFilter('status',array('in' => $statusArray));
		$data = $order_items->getData();
		// print_r($data);exit;
		$customerModel = Mage::getModel('customer/customer');

		foreach ($data as $value) {	
			$customerEmail = $value['customer_email'];
			$customerId = $value['customer_id'];
			$customersOrdersCount = $orderHelper->getCustomersOrdersCount($customerEmail);			
		    $orderCollection->addFieldToFilter('customer_email',$customerEmail);
		    if($customerId){
		    	if($customersOrdersCount >= 2){
		            foreach ($orderCollection as $_order) {
		            	$_orderdata = Mage::getModel('sales/order')->load($_order->getId());
		                $_orderdata->setCustomerGroupId($regularGroupId);
		                $_orderdata->save();
		            }
		            if($customerId){
		            	$_customer = $customerModel->load($customerId);
		            	$_customer->setGroupId($regularGroupId);
		            	$_customer->save();
		            }
		            
		        }elseif($customersOrdersCount == 1){
		            foreach ($orderCollection as $_order) {
		            	$_orderdata = Mage::getModel('sales/order')->load($_order->getId());
		                $_orderdata->setCustomerGroupId($generalGroupId);
		                $_orderdata->save();
		            }
		            if($customerId){
		            	$_customer = $customerModel->load($customerId);
		            	$_customer->setGroupId($generalGroupId);
		            	$_customer->save(); 
		            }

		        }
		        
		    }else{
		    	if($customersOrdersCount == 1){
		            foreach ($orderCollection as $_order) {
		            	$_orderdata = Mage::getModel('sales/order')->load($_order->getId());
		                $_orderdata->setCustomerGroupId($generalGroupId);
		                $_orderdata->save();
		            }

		        }elseif($customersOrdersCount >= 2){
		            foreach ($orderCollection as $_order) {
		            	$_orderdata = Mage::getModel('sales/order')->load($_order->getId());
		                $_orderdata->setCustomerGroupId($regularGroupId);
		                $_orderdata->save();
		            } 

		        }
		    }
		}
	}

	public function changeCustomerGroupAction(){
        $params = $this->getRequest()->getParams();
        $incrementId = $params['incrementId'];
        $order = Mage::getModel('sales/order')->loadByIncrementId($incrementId);
        $regularGroupId = 6;
        $premiumGroupId = 2;
        $newGroupId = 8;
        $guestGroupId = 9;
        $customerId = $order->getCustomerId();
        $customerEmail = $order->getCustomerEmail();
        $orderCollection = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('customer_email',$customerEmail);
        $orderHelper = Mage::helper('frontend/order');
        $customersOrdersCount = $orderHelper->getCustomersOrdersCount($customerEmail);
        if($customerId){
            if($customersOrdersCount >= 3){
                foreach ($orderCollection as $_order) {
                    $_order->setCustomerGroupId($premiumGroupId);
                    $_order->save();
                }

                $_customer = Mage::getModel('customer/customer')->load($customerId);
                $_customer->setGroupId($premiumGroupId);
                $_customer->save();

            }elseif($customersOrdersCount >= 1 && $customersOrdersCount <= 2){
                foreach ($orderCollection as $_order) {
                    $_order->setCustomerGroupId($regularGroupId);
                    $_order->save();
                }

                $_customer = Mage::getModel('customer/customer')->load($customerId);
                $_customer->setGroupId($regularGroupId);
                $_customer->save(); 

            }elseif($customersOrdersCount == 0){
            	foreach ($orderCollection as $_order) {
                    $_order->setCustomerGroupId($newGroupId);
                    $_order->save();
                }

                $_customer = Mage::getModel('customer/customer')->load($customerId);
                $_customer->setGroupId($newGroupId);
                $_customer->save(); 
            }
        }else{
            if($customersOrdersCount >= 3){
                foreach ($orderCollection as $_order) {
                    $_order->setCustomerGroupId($premiumGroupId);
                    $_order->save();
                }
            }elseif($customersOrdersCount >= 1 && $customersOrdersCount <= 2){
                foreach ($orderCollection as $_order) {
                    $_order->setCustomerGroupId($regularGroupId);
                    $_order->save();
                }
            }elseif($customersOrdersCount == 0){
                foreach ($orderCollection as $_order) {
                    $_order->setCustomerGroupId($guestGroupId);
                    $_order->save();
                }
            }
        }
	}
}
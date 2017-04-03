<?php
class MW_Credit_Model_Observer
{
	
	private function _getSession() 
	{
		return Mage::getSingleton('checkout/session');
	}
	
	private function _getCustomer()
	{
		return Mage::getSingleton('customer/session')->getCustomer();
	} 
	
	/**
	 * Save Credit for Checkout
	 * 
	 * @param $observer
	 * @return void
	 * su dung credit de check out . luu vao credit hisotry
	 */
	public function saveOrderAfter($observer)
    {
    	 $order = $observer->getOrder();
    	 $storeId = $order->getStoreId();
		 if(Mage::helper('affiliate/data')->getEnabledStore($storeId)) 
		 {
	    	
	    	$customerId = (int)$order->getCustomerId();
	    	//$customerId = (int)$this->_getCustomer()->getId();
	    	if($customerId){            
	            //Subtract credit points of customer and save to order if customer use this credit to checkout Mage::getSingleton('checkout/session')
	    		$amountCredit = $this->_getSession()->getCredit();
	            if($amountCredit >0){	            	
	            	$creditcustomer = Mage::getModel('credit/creditcustomer')->load($customerId);
	            	$oldCredit = $creditcustomer->getCredit();
	            	$newCredit = $oldCredit - $amountCredit;
	            	$newCredit = round($newCredit,2);
 					$amountCredit = round($amountCredit,2);
 					$oldCredit=round($oldCredit,2);
	            	// Save history transaction
	            	$historyData = array('customer_id'=>$customerId,
	            						 'type_transaction'=>MW_Credit_Model_Transactiontype::USE_TO_CHECKOUT, 
	            					     'transaction_detail'=>$order->getIncrementId(), 
	            						 'amount'=>-$amountCredit, 
	            						 'beginning_transaction'=>$oldCredit,
	            						 'end_transaction'=>$newCredit,
	            						 'status'=>MW_Credit_Model_Orderstatus::PENDING,
	            					     'created_time'=>now());
	            	Mage::getModel("credit/credithistory")->setData($historyData)->save();
	            	//Subtract credit of customer
	            	$creditcustomer->setCredit($newCredit)->save();
	            	
	            	// gui mail cho khach hang khi dung credit de check out
	            	$store_name = Mage::getStoreConfig('general/store_information/name', $storeId);
		    		$sender = Mage::getStoreConfig('affiliate/customer/email_sender', $storeId);
		    		$email = Mage::getModel('customer/customer')->load($customerId)->getEmail();
		    		$name = Mage::getModel('customer/customer')->load($customerId)->getName();
		    		$teampale = 'affiliate/customer/email_template_credit_balance_changed';
		    		$sender_name = Mage::getStoreConfig('trans_email/ident_'.$sender.'/name', $storeId);
		    		$customer_credit_link = Mage::app()->getStore($storeId)->getUrl('credit');
		    		$data_mail['customer_name'] = $name;
		    		$data_mail['transaction_amount'] = Mage::helper('core')->currency(-$amountCredit,true,false);
		    		$data_mail['customer_balance'] = Mage::helper('core')->currency($newCredit,true,false);
		    		$comment = 'You checkout by credit order #'.$order->getIncrementId();
		    		$data_mail['transaction_detail'] = $comment;
		    		$data_mail['transaction_time'] = now();
		    		$data_mail['sender_name'] = $sender_name;
		    		$data_mail['store_name'] = $store_name;
		    		$data_mail['customer_credit_link'] = $customer_credit_link;
		    		Mage::helper('affiliate')->_sendEmailTransactionNew($sender,$email,$name,$teampale,$data_mail,$storeId);	  		
	            }
	            //Reset credit in Session
	            $this->_getSession()->unsetData('credit');
	    	}
		}
    }
    // update status when use credit to checkout
    public function updateStatusOrder($arvgs)
    {
    	$order = $arvgs->getOrder();
    	$store_id = $order->getStoreId();
    	if(Mage::helper('affiliate/data')->getEnabledStore($store_id)) 
    	{
	    	$customer_id = (int)$order->getCustomerId();
	    	$order_id = $order->getIncrementId();
	    	
	    	if($order->getStatus() == 'complete' || $order->getStatus() == 'processing') $this->saveOrderComplete($order_id, $customer_id);
    	 	
	    	if($order->getStatus() == 'closed')  $this->saveOrderClosed($order_id, $customer_id);
	    	
	    	if($order->getStatus() == 'canceled')	$this->saveOrderCanceled($order,$order_id, $customer_id);
    	}
    }
   
	// update status complete cho order use credit to checkout
    public function saveOrderComplete($order_id, $customer_id)
    {   
    	if($customer_id)
    	{
    		$collection = Mage::getModel('credit/credithistory')->getCollection()
		                    	->addFieldToFilter('type_transaction',MW_Credit_Model_Transactiontype::USE_TO_CHECKOUT)
		                    	->addFieldToFilter('customer_id',$customer_id)
		                    	->addFieldToFilter('transaction_detail',$order_id);
                    	
            foreach($collection as $credithistory)
             {
             	$status = MW_Credit_Model_Orderstatus::COMPLETE;
				$credithistory ->setStatus($status)->save();
				//$credithistory->setCreatedTime(now())->save();
             }
             // gui mail cho customer khi checkout credit thanh cong
    	}	
    }
    // update status canceled cho order use credit to checkout
    public function saveOrderCanceled($order,$order_id, $customer_id)
    {
     	if($customer_id)
    	{
    		$creditcustomer = Mage::getModel('credit/creditcustomer')->load($customer_id);
    		$oldcredit = $creditcustomer->getCredit();
    		$collection = Mage::getModel('credit/credithistory')->getCollection()
		                    	->addFieldToFilter('type_transaction',MW_Credit_Model_Transactiontype::USE_TO_CHECKOUT)
		                    	->addFieldToFilter('customer_id',$customer_id)
		                    	->addFieldToFilter('transaction_detail',$order_id)
		                    	->addFieldToFilter('status',MW_Credit_Model_Orderstatus::PENDING);
                    	
            foreach($collection as $credithistory)
             {  
             	// chi cap nhat lai trang thai va set lai credit
             	$amount=$credithistory->getAmount();
				$newcredit = $oldcredit - $amount;
				$oldcredit = round($oldcredit,2);
				$newcredit = round($newcredit,2);
 				$amount = round($amount,2);
             	$status = MW_Credit_Model_Orderstatus::CANCELED;
             	$credithistory ->setStatus($status)->save();
				// luu them vao credit history kieu cancel order using credit to checkout
	       		$historyData = array('customer_id'=>$customer_id,
				 					 'type_transaction'=>MW_Credit_Model_Transactiontype::CANCEL_USE_TO_CHECKOUT, 
				 					 'status'=>MW_Credit_Model_Orderstatus::COMPLETE,
					     		     'transaction_detail'=>$order_id, 
					           		 'amount'=>-$amount, 
					         		 'beginning_transaction'=>$oldcredit,
					        		 'end_transaction'=>$newcredit,
					           	     'created_time'=>now());
	   			Mage::getModel("credit/credithistory")->setData($historyData)->save();
				// refund credit, add again credit
				$creditcustomer->setCredit($newcredit)->save();
             }
            // gui mail cho customer khi checkout credit co don hang bi huy
            $credit = Mage::getModel('credit/creditorder')->load($order_id)->getCredit();
            if($credit >0)
            {
            	$storeId = $order->getStoreId();
            	$store_name = Mage::getStoreConfig('general/store_information/name', $storeId);
	    		$sender = Mage::getStoreConfig('affiliate/customer/email_sender', $storeId);
	    		$email = Mage::getModel('customer/customer')->load($customer_id)->getEmail();
	    		$name = Mage::getModel('customer/customer')->load($customer_id)->getName();
	    		$teampale = 'affiliate/customer/email_template_credit_balance_changed';
	    		$sender_name = Mage::getStoreConfig('trans_email/ident_'.$sender.'/name', $storeId);
	    		$customer_credit_link = Mage::app()->getStore($storeId)->getUrl('credit');
	    		$data_mail['customer_name'] = $name;
	    		$data_mail['transaction_amount'] = Mage::helper('core')->currency(-$amount,true,false);
	    		$data_mail['customer_balance'] = Mage::helper('core')->currency($newcredit,true,false);
	    		$comment = 'You checkout by credit order #'.$order_id.' Your order was cancelled';
	    		$data_mail['transaction_detail'] = $comment;
	    		$data_mail['transaction_time'] = now();
	    		$data_mail['sender_name'] = $sender_name;
	    		$data_mail['store_name'] = $store_name;
	    		$data_mail['customer_credit_link'] = $customer_credit_link;
	    		Mage::helper('affiliate')->_sendEmailTransactionNew($sender,$email,$name,$teampale,$data_mail,$storeId);
            } 
    	}
    }
	//bat su kien refund order khi su dung credit de check out
	public function saveOrderClosed($order_id, $customer_id)
    {   
    	$collection = Mage::getModel('credit/credithistory')->getCollection()
                    	->addFieldToFilter('type_transaction',MW_Credit_Model_Transactiontype::USE_TO_CHECKOUT)
                    	->addFieldToFilter('customer_id',$customer_id)
                    	->addFieldToFilter('transaction_detail',$order_id)
                    	->addFieldToFilter('status',MW_Credit_Model_Orderstatus::COMPLETE);
       
            foreach($collection as $credithistory)
    	  {  		
          		$status = MW_Credit_Model_Orderstatus::CLOSED;
				$credithistory ->setStatus($status)->save();
								
    	  }
    }
    /**
     * 
     * @param $observer
     * @return void
     */
    // khoi tao credit voi customer dang ky vao website
    public function initializeCredit($observer)
    {
    	$customer_id = (int)$observer->getCustomer()->getId();
		$creditcustomer = Mage::getModel('credit/creditcustomer')->load($customer_id);
     	if(!($creditcustomer->getId()) ) 
     		{
		  		 //Add credit to new customer
           		$customerData = array('customer_id'=>$customer_id,
            	   		       	      'credit'=>0);
          		Mage::getModel("credit/creditcustomer")->saveCreditCustomer($customerData); 
			}
    }
	
    // bat su kien add commssion complete cua affiliate truyen sang
	public function saveAffiliateCreditHistory($observer)
	{
		$order_ids = $observer->getOrderIds();
		foreach ($order_ids as $order_id) {
	        $transaction_collections = Mage::getModel('affiliate/affiliatetransaction')->getCollection()
			        							->addFieldToFilter('status',MW_Credit_Model_Orderstatus::COMPLETE)
			                    				->addFieldToFilter('order_id',$order_id);
	        if(sizeof($transaction_collections)>0)
	        {
	        	foreach ($transaction_collections as $transaction_collection) {
	        		$customer_id = $transaction_collection->getCustomerInvited();
	        		if($customer_id != 0)
	        		{   
	        			$affiliate_customer = Mage::getModel('affiliate/affiliatecustomers')->load($customer_id);
	        			$creditcustomer = Mage::getModel('credit/creditcustomer')->load($customer_id);
	        			$oldTotalCommission = $affiliate_customer->getTotalCommission();
				    	$oldCredit = $creditcustomer->getCredit();
						$amount = $transaction_collection->getTotalCommission();
						$newTotalCommission = $oldTotalCommission + $amount;
						$newCredit=$oldCredit + $amount;
						
						$newTotalCommission = round($newTotalCommission,2);
						$newCredit=round($newCredit,2);
				 		$amount=round($amount,2);
				 		$oldCredit=round($oldCredit,2);
				 		
				 		$affiliate_customer->setData('total_commission',$newTotalCommission)->save();
						$creditcustomer->setData('credit',$newCredit)->save();
						
						$historyData = array('customer_id'=>$customer_id,
						 					 'type_transaction'=>MW_Credit_Model_Transactiontype::BUY_PRODUCT, 
						 					 'status'=>MW_Credit_Model_Orderstatus::COMPLETE,
				            			     'transaction_detail'=>$order_id, 
				            				 'amount'=>$amount, 
				            				 'beginning_transaction'=>$oldCredit,
				        					 'end_transaction'=>$newCredit,
				            			     'created_time'=>now());
	   					//Mage::getModel("credit/credithistory")->saveAffiliateTransactionHistory($historyData);
	   					Mage::getModel("credit/credithistory")->setData($historyData)->save();
	   					// gui mail cho khach hang khi khach hang do nhan dc hoa hong tu viec moi thanh vien mua hang
	   					$storeId = Mage::getModel("sales/order")->loadByIncrementId($order_id)->getStoreId();
		            	$store_name = Mage::getStoreConfig('general/store_information/name', $storeId);
			    		$sender = Mage::getStoreConfig('affiliate/customer/email_sender', $storeId);
			    		$email = Mage::getModel('customer/customer')->load($customer_id)->getEmail();
			    		$name = Mage::getModel('customer/customer')->load($customer_id)->getName();
			    		$teampale = 'affiliate/customer/email_template_credit_balance_changed';
			    		$sender_name = Mage::getStoreConfig('trans_email/ident_'.$sender.'/name', $storeId);
			    		$customer_credit_link = Mage::app()->getStore($storeId)->getUrl('credit');
			    		$data_mail['customer_name'] = $name;
			    		$data_mail['transaction_amount'] = Mage::helper('core')->currency($amount,true,false);
			    		$data_mail['customer_balance'] = Mage::helper('core')->currency($newCredit,true,false);
			    		$comment = 'Commission of order #'.$order_id;
			    		$data_mail['transaction_detail'] = $comment;
			    		$data_mail['transaction_time'] = now();
			    		$data_mail['sender_name'] = $sender_name;
			    		$data_mail['store_name'] = $store_name;
			    		$data_mail['customer_credit_link'] = $customer_credit_link;
			    		Mage::helper('affiliate')->_sendEmailTransactionNew($sender,$email,$name,$teampale,$data_mail,$storeId);
	        		}
	        	}
	        }
		}
	}
	// bat su kien subtract commission cua affiliate truyen sang
	// neu cau hinh co bi tru tien khi refund product
	public function saveAffiliateRefundOrder($observer)
	{
		$order_ids = $observer->getOrderIds();
		foreach ($order_ids as $order_id) {
	        $transaction_collections = Mage::getModel('affiliate/affiliatetransaction')->getCollection()
	        							->addFieldToFilter('status',MW_Credit_Model_Orderstatus::CLOSED)
	                    				->addFieldToFilter('order_id',$order_id);
	        if(sizeof($transaction_collections)>0)
	        {
	        	foreach ($transaction_collections as $transaction_collection) {
	        		$customer_id = $transaction_collection->getCustomerInvited();
	        		if($customer_id != 0)
	        		{   
	        			$affiliate_customer = Mage::getModel('affiliate/affiliatecustomers')->load($customer_id);
	        			$creditcustomer = Mage::getModel('credit/creditcustomer')->load($customer_id);
	        			
	        			$oldTotalCommission = $affiliate_customer->getTotalCommission();
				    	$oldCredit = $creditcustomer->getCredit();
						$amount=$transaction_collection->getTotalCommission();
						$newTotalCommission = $oldTotalCommission - $amount;
						$newCredit=$oldCredit - $amount;
						
						$newTotalCommission = round($newTotalCommission,2);
						$newCredit=round($newCredit,2);
				 		$amount=round($amount,2);
				 		$oldCredit=round($oldCredit,2);
				 		
				 		$affiliate_customer->setData('total_commission',$newTotalCommission)->save();
						$creditcustomer->setData('credit',$newCredit)->save();
						
						$historyData = array('customer_id'=>$customer_id,
						 					 'type_transaction'=>MW_Credit_Model_Transactiontype::REFUND_PRODUCT_AFFILIATE, 
						 					 'status'=>MW_Credit_Model_Orderstatus::COMPLETE,
				            			     'transaction_detail'=>$order_id, 
				            				 'amount'=>-$amount, 
				            				 'beginning_transaction'=>$oldCredit,
				        					 'end_transaction'=>$newCredit,
				            			     'created_time'=>now());
	   					//Mage::getModel("credit/credithistory")->saveAffiliateTransactionHistory($historyData);
	   					Mage::getModel("credit/credithistory")->setData($historyData)->save();
	   					// gui mail cho khach hang khi khach hang do nhan dc hoa hong tu viec moi thanh vien mua hang
	   					// xong bi tra lai do refund product va cau hinh config
	   					$storeId = Mage::getModel("sales/order")->loadByIncrementId($order_id)->getStoreId();
		            	$store_name = Mage::getStoreConfig('general/store_information/name', $storeId);
			    		$sender = Mage::getStoreConfig('affiliate/customer/email_sender', $storeId);
			    		$email = Mage::getModel('customer/customer')->load($customer_id)->getEmail();
			    		$name = Mage::getModel('customer/customer')->load($customer_id)->getName();
			    		$teampale = 'affiliate/customer/email_template_credit_balance_changed';
			    		$sender_name = Mage::getStoreConfig('trans_email/ident_'.$sender.'/name', $storeId);
			    		$customer_credit_link = Mage::app()->getStore($storeId)->getUrl('credit');
			    		$data_mail['customer_name'] = $name;
			    		$data_mail['transaction_amount'] = Mage::helper('core')->currency(-$amount,true,false);
			    		$data_mail['customer_balance'] = Mage::helper('core')->currency($newCredit,true,false);
			    		$comment = 'Commission refund of order #'.$order_id;
			    		$data_mail['transaction_detail'] = $comment;
			    		$data_mail['transaction_time'] = now();
			    		$data_mail['sender_name'] = $sender_name;
			    		$data_mail['store_name'] = $store_name;
			    		$data_mail['customer_credit_link'] = $customer_credit_link;
			    		Mage::helper('affiliate')->_sendEmailTransactionNew($sender,$email,$name,$teampale,$data_mail,$storeId);
	        		}
	        	}
	        }
		}
	}
	public function collectTotalBefore($argv)
    {
    	$store_id = Mage::app()->getStore()->getId();
    	if(Mage::helper('affiliate/data')->getEnabledStore($store_id)) 
    	 {
    	 	$credit  = $this->_getSession()->getCredit();
	    	$quote = $argv->getQuote();
	    	$address = $quote->isVirtual()?$quote->getBillingAddress():$quote->getShippingAddress();
			$subtotal = $address->getBaseSubtotal();
			$subtotal += $address->getBaseDiscountAmount() + $credit;
			//Convert subtotal to base currency
			//$rate = Mage::helper('core')->currency(1,false);
			if($credit > $subtotal) 
			{
				$this->_getSession()->setCredit(0);
			}
		}
    }
}
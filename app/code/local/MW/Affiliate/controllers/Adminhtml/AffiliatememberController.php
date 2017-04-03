<?php

class MW_Affiliate_Adminhtml_AffiliatememberController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('affiliate/member')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Manager Members'), Mage::helper('adminhtml')->__('Manager Member'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('affiliate/affiliatecustomers')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('affiliate_data_member', $model);

			$this->loadLayout();
			$this->_setActiveMenu('affiliate/member');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('affiliate/adminhtml_affiliatemember_edit'))
				->_addLeft($this->getLayout()->createBlock('affiliate/adminhtml_affiliatemember_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('affiliate')->__('Member does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
//	public function newAction() {
//		$this->_forward('edit');
//	}
	public function invitationAction()
	{
        $this->getResponse()->setBody($this->getLayout()->createBlock('affiliate/adminhtml_affiliatemember_edit_tab_invitation')->toHtml());
	}
	public function transactionAction()
	{
        $this->getResponse()->setBody($this->getLayout()->createBlock('affiliate/adminhtml_affiliatemember_edit_tab_transaction')->toHtml());
	}
	public function viewtransactionAction()
	{
        $this->getResponse()->setBody($this->getLayout()->createBlock('affiliate/adminhtml_affiliatemember_edit_tab_viewtransaction')->toHtml());
	}
	public function withdrawnAction()
	{
        $this->getResponse()->setBody($this->getLayout()->createBlock('affiliate/adminhtml_affiliatemember_edit_tab_withdrawn')->toHtml());
	}
	public function credithistoryAction()
	{
        $this->getResponse()->setBody($this->getLayout()->createBlock('affiliate/adminhtml_affiliatemember_edit_tab_credithistory')->toHtml());
	}
 
	public function saveAction() {
		$data = $this->getRequest()->getPost();
		if ($data) {
		    //Zend_Debug::dump($data);die();
		    //var_dump($this->getRequest()->getParam('status'));exit;	
		    $data = $this->getRequest()->getPost();
			$member_id = $this->getRequest()->getParam('id');
			$model = Mage::getModel('affiliate/affiliatecustomers');
			try {	
				if($member_id!=''){	
					 //$model->setData($data)->setId($member_id);
					 $store_id = Mage::getModel('customer/customer')->load($member_id)->getStoreId();
					 $max = (double)Mage::helper('affiliate/data')->getWithdrawMaxStore($store_id);
	  				 $min = (double)Mage::helper('affiliate/data')->getWithdrawMinStore($store_id);
					 $collection = $model ->load($member_id);
					 if(isset($data['affiliate_parent']) && $data['affiliate_parent'] != ''){
					 	$affiliateFilters = Mage::getModel('customer/customer')->getCollection()
		                    			    ->addFieldToFilter('email',$data['affiliate_parent']);
						 if(sizeof($affiliateFilters) > 0 )
					      	{ 
					         	foreach ($affiliateFilters as $affiliateFilter) 
					         	{
						          $mw_check = Mage::helper('affiliate')->getActiveAndEnableAffiliate($affiliateFilter->getId());
					         	  if($affiliateFilter->getId()!= $member_id && $mw_check == 1)
						           {	
						           	   $collection ->setCustomerInvited($affiliateFilter->getId());
							           
						           }else{
						           		$this->_getSession()->addError($this->__('Affiliate parent invalid'));
							            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
						 			    return;
						           }
						           break;
					         	} 
					           
					         }else{
					         	$this->_getSession()->addError($this->__('Affiliate parent invalid'));
					            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				 			    return;
					         }  			  
					 	
					 }else{
					 	$collection ->setCustomerInvited(0);
					 }
					 
					 $collection ->setStatus($data['affiliate_status']);
					 $collection ->setPaymentGateway($data['payment_gateway']);
					 if($data['payment_gateway'] != 'banktransfer'){
						 $collection ->setPaymentEmail($data['payment_email']);
						 $collectionFilters = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
		                    			->addFieldToFilter('payment_email',$data['payment_email']);
			            //var_dump(sizeof($collectionFilter));exit;
					     if(sizeof($collectionFilters) > 0 )
				         { 
				         	foreach ($collectionFilters as $collectionFilter) 
				         	{
					           if($collectionFilter->getCustomerId()!= $member_id)
					           {	
						           $this->_getSession()->addError($this->__('There is already an account with this emails paypal'));
						           $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
					 			   return;
					           }
				         	} 
				           
				         }
					 }else{
					 	$collection ->setPaymentEmail('');
					 }
					 $collection ->setAutoWithdrawn($data['auto_withdrawn']);
					 $withdrawn_level = (double)$data['withdrawn_level'];
					 $auto_withdrawn = (int)$data['auto_withdrawn'];
					 if($auto_withdrawn == MW_Affiliate_Model_Autowithdrawn::AUTO)
					 {  
			            if($withdrawn_level < $min  || $withdrawn_level > $max)
			            {
			            	$this->_getSession()->addError($this->__('Please insert a value of Auto payment when account balance reaches that is in range of [%s, %s]',$min,$max));
			            	$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			 			   	return;
			            	
			            } 
					 	$collection ->setWithdrawnLevel($data['withdrawn_level']);
					 }
					 if(!$data['reserve_level'])$data['reserve_level'] == 0;
					 if(!$data['referral_site'])$data['referral_site'] == '';
					 $collection ->setReferralSite($data['referral_site']); 
					 $collection ->setReserveLevel($data['reserve_level']);
					 $collection->save();
					 
					 if($data['payment_gateway'] == 'banktransfer'){
		    			$bank_name = $data['bank_name'];
		    			$name_account = $data['name_account'];
		    			$bank_country = $data['bank_country'];
		    			$swift_bic = $data['swift_bic'];
		    			$account_number = $data['account_number'];
		    			$re_account_number = $data['re_account_number'];
					 }else{
					 	$bank_name = '';
		    			$name_account = '';
		    			$bank_country = '';
		    			$swift_bic = '';
		    			$account_number = '';
		    			$re_account_number = '';
					 	
					 }
	    			 $data_bank  =  array('bank_name'=>$bank_name,
								    	  'name_account'=>$name_account,
								    	  'bank_country'=>$bank_country,
								    	  'swift_bic'=>$swift_bic,
								    	  'account_number'=>$account_number,
								    	  're_account_number'=>$re_account_number);
	    			
	    			 $model1 = Mage::getModel('affiliate/affiliatecustomers')->load($member_id);
	    			 $model1->setData($data_bank) ->setId($member_id);
	    			 $model1->save();
					  // them member vao group
					 if($data['group_id'] != 0){
					 	// xoa cac member trong group de update lai du lieu moi
						$group_members = Mage::getModel('affiliate/affiliategroupmember')->getCollection()
				        									 ->addFieldToFilter('customer_id',$member_id);
				        									
				        if(sizeof($group_members) > 0){
				        	 foreach ($group_members as $group_member) {
				        	 	$group_member ->delete();
				        	 }
				        }
				        $datamembergroup['group_id'] = $data['group_id'];
				        $datamembergroup['customer_id'] = $member_id;
				        Mage::getModel("affiliate/affiliategroupmember")->setData($datamembergroup)->save();
					 }
					 //set total member customer program 
					 Mage::helper('affiliate')->setTotalMemberProgram();
					 // edit customer
					 // truong hop them bot credit do admin voi ly do.....
					 $amountCredit = $data['amount_credit'];
					 if($amountCredit!=0)
					 {
					 	 $creditcustomer = Mage::getModel('credit/creditcustomer')->load($member_id);
				    	 $oldCredit = $creditcustomer->getCredit();
				    	 $typeTransaction = MW_Credit_Model_Transactiontype::ADMIN_CHANGE;
				    	 $comment = '';
				    	 if($data['comment']) $comment = $data['comment'];
				    	 $newCredit = $oldCredit + $amountCredit;
				    	//echo $newCredit;exit;
				    	 $newCredit=round($newCredit,2);
				 		 $amountCredit=round($amountCredit,2);
				 		 $oldCredit=round($oldCredit,2);
					     $creditcustomer->setData('credit',$newCredit)->save();
				
				    	// Save history transaction for customer
				         $historyData = array('customer_id' => $member_id,
				         					  'type_transaction'=>MW_Credit_Model_Transactiontype::ADMIN_CHANGE, 
				            			      'transaction_detail'=>$comment, 
				            				  'amount'=>$amountCredit,
				            				  'beginning_transaction'=>$oldCredit,
				        					  'end_transaction'=>$newCredit,
				                              'status' => MW_Credit_Model_Orderstatus::COMPLETE,
				            			      'created_time'=>now());
					    
				   		 Mage::getModel("credit/credithistory")->setData($historyData)->save();
				   		 
				   		 // gui mail cho khach hang khi admin them bot credit voi ly do.........
				   		 $storeId = Mage::getModel('customer/customer')->load($member_id)->getStoreId();
				   		 $store_name = Mage::getStoreConfig('general/store_information/name', $storeId);
				    	 $sender = Mage::getStoreConfig('affiliate/customer/email_sender', $storeId);
				    	 $email = Mage::getModel('customer/customer')->load($member_id)->getEmail();
				    	 $name = Mage::getModel('customer/customer')->load($member_id)->getName();
				    	 $teampale = 'affiliate/customer/email_template_credit_balance_changed';
				    	 $sender_name = Mage::getStoreConfig('trans_email/ident_'.$sender.'/name', $storeId);
			    		 $customer_credit_link = Mage::app()->getStore($storeId)->getUrl('credit');
			    		 $data_mail['customer_name'] = $name;
			    		 $data_mail['transaction_amount'] = Mage::helper('core')->currency($amountCredit,true,false);
			    		 $data_mail['customer_balance'] = Mage::helper('core')->currency($newCredit,true,false);
			    		 $data_mail['transaction_detail'] = $comment;
			    		 $data_mail['transaction_time'] = now();
			    		 $data_mail['sender_name'] = $sender_name;
			    		 $data_mail['store_name'] = $store_name;
			    		 $data_mail['customer_credit_link'] = $customer_credit_link;
			    		 Mage::helper('affiliate')->_sendEmailTransactionNew($sender,$email,$name,$teampale,$data_mail,$storeId);
					 }
					 
					 // truong hop payout cho customer
					 $payout_credit = $data['payout_credit'];
					 if(isset($payout_credit) && $payout_credit !=''){
					 	$payout_credit = (double)$payout_credit;
					 	$creditcustomer = Mage::getModel('credit/creditcustomer')->load($member_id);
				    	$oldCredit = (double)$creditcustomer->getCredit();
					 	if($payout_credit > 0 && $payout_credit <=  $oldCredit){
					 		 $this ->getRequestWithdrawComplete($member_id, $payout_credit);
					 	}else{
					 		$this->_getSession()->addError($this->__('Please insert a value of Payout that is in range of (%s, %s]',0,$oldCredit));
			            	$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			 			   	return;
					 	}
					 }  
					 
				}
				if($member_id==''){
				}	
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('affiliate')->__('The member has successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('affiliate')->__('Unable to find member to save'));
        $this->_redirect('*/*/');
	}
	public function getRequestWithdrawComplete($customer_id,$credit)
	{
		$store_id = Mage::getModel('customer/customer')->load($customer_id)->getStoreId();
		$fee = (int)Mage::helper('affiliate/data')->getFeeStore($store_id);
		$withdraw_receive = $credit - $fee;
		$affiliate_customer = Mage::getModel('affiliate/affiliatecustomers')->load($customer_id);
		$payment_gateway = $affiliate_customer ->getPaymentGateway();
		$payment_email = $affiliate_customer ->getPaymentEmail();
		if($payment_gateway == 'banktransfer') $payment_email = '';
	    	
	  	$bank_name = $affiliate_customer->getBankName();
		$name_account = $affiliate_customer->getNameAccount();
		$bank_country = $affiliate_customer->getBankCountry();
		$swift_bic = $affiliate_customer->getSwiftBic();
		$account_number= $affiliate_customer->getAccountNumber();
		$re_account_number = $affiliate_customer->getReAccountNumber();

        $withdrawnData =  array('customer_id'=>$customer_id,
        						'payment_gateway'=>$payment_gateway,
	              				'payment_email'=>$payment_email,
	              				'bank_name'=>$bank_name,
					    	    'name_account'=>$name_account,
					    	    'bank_country'=>$bank_country,
					    	    'swift_bic'=>$swift_bic,
					    	    'account_number'=>$account_number,
					    	    're_account_number'=>$re_account_number,
                 				'withdrawn_amount'=>$credit,
              					'fee'=>$fee,
								'amount_receive'=>$withdraw_receive,
          						'status'=>MW_Affiliate_Model_Status::COMPLETE,
                        		'withdrawn_time'=>now());
		Mage::getModel('affiliate/affiliatewithdrawn')->setData($withdrawnData)->save(); 
		// update lai credit
		$creditcustomer = Mage::getModel('credit/creditcustomer')->load($customer_id);
    	$oldCredit = $creditcustomer->getCredit();
   		$amount = - $credit;
  		$newCredit = $oldCredit + $amount;
  		$newCredit=round($newCredit,2);
   		$creditcustomer->setCredit($newCredit)->save();
   		
   		$collectionWithdrawns = Mage::getModel('affiliate/affiliatewithdrawn')->getCollection()
			                    				 ->addFieldToFilter('customer_id',$customer_id)
		                    					 ->setOrder('withdrawn_id','DESC');
        foreach($collectionWithdrawns as $collectionWithdrawn)
        {
        	$withdrawn_id = $collectionWithdrawn->getWithdrawnId();
        	break;
        }
   		// luu vao bang credit history
       	$historyData = array('customer_id'=>$customer_id,
		 					 'type_transaction'=>MW_Credit_Model_Transactiontype::WITHDRAWN, 
		 					 'status'=>MW_Credit_Model_Orderstatus::COMPLETE,
			     		     'transaction_detail'=>$withdrawn_id, 
			           		 'amount'=>$amount, 
			         		 'beginning_transaction'=>$oldCredit,
			        		 'end_transaction'=>$newCredit,
			           	     'created_time'=>now());
   		Mage::getModel("credit/credithistory")->setData($historyData)->save();
   		// gui mail cho khach hang khi rut tien thanh cong, do admin rut
   		Mage::helper('affiliate')->sendMailCustomerWithdrawnComplete($customer_id, $amount);
   		
	}

	public function withdrawnstatusAction()
    {   
    	//echo $this->getRequest()->getParam('status_program');exit;
        $withdrawn_ids = $this->getRequest()->getParam('Affiliate_member_withdrawn');
        $customer_Id = $this->getRequest()->getParam('id');
        $status     = (int)$this->getRequest()->getParam('status');
        if(!is_array($withdrawn_ids)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select withdrawn(s)'));
        } else {
            try {
            		Mage::helper('affiliate') ->processWithdrawn($status, $withdrawn_ids);            
             // thuc hien
            } catch (Mage_Core_Model_Exception $e) {
            	$this->_getSession()->addError($e->getMessage());
        	}
            
            catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/edit/id/'.$customer_Id);
    }
	
    public function massStatusAction()
    {   
    	//echo $this->getRequest()->getParam('status_program');exit;
        $customerIds = $this->getRequest()->getParam('affiliatememberGrid');
        if(!is_array($customerIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select member(s)'));
        } else {
            try {
                foreach ($customerIds as $customerId) {
                	$status_affiliate = Mage::getSingleton('affiliate/affiliatecustomers')->load($customerId)->getStatus();
                	// gui mail trong truong hop khoa customer affiliate
                	if($this->getRequest()->getParam('status') == MW_Affiliate_Model_Statusreferral::LOCKED && $status_affiliate ==MW_Affiliate_Model_Statusreferral::ENABLED)
                	{
                		$storeId = Mage::getModel('customer/customer')->load($customerId)->getStoreId();
                		$store_name = Mage::getStoreConfig('general/store_information/name', $storeId);
		    			$sender = Mage::getStoreConfig('affiliate/customer/email_sender', $storeId);
		    			$email = Mage::getModel('customer/customer')->load($customerId)->getEmail();
		    			$name = Mage::getModel('customer/customer')->load($customerId)->getName();
		    			$teampale = 'affiliate/customer/email_template_lock';
		    			$sender_name = Mage::getStoreConfig('trans_email/ident_'.$sender.'/name', $storeId);
		    			$link = Mage::app()->getStore($storeId)->getUrl('affiliate');
		    			$data_mail['customer_name'] = $name;
		    			$data_mail['sender_name'] = $sender_name;
		    			$data_mail['store_name'] = $store_name;
		    			$data_mail['link'] = $link;
		    			Mage::helper('affiliate')->_sendEmailTransactionNew($sender,$email,$name,$teampale,$data_mail,$storeId);
                	}
                	// gui mail trong truong hop mo khoa customer affiliate
                	else if($this->getRequest()->getParam('status')==MW_Affiliate_Model_Statusreferral::ENABLED && $status_affiliate==MW_Affiliate_Model_Statusreferral::LOCKED)
                	{
                		$storeId = Mage::getModel('customer/customer')->load($customerId)->getStoreId();
                		$store_name = Mage::getStoreConfig('general/store_information/name', $storeId);
		    			$sender = Mage::getStoreConfig('affiliate/customer/email_sender', $storeId);
		    			$email = Mage::getModel('customer/customer')->load($customerId)->getEmail();
		    			$name = Mage::getModel('customer/customer')->load($customerId)->getName();
		    			$teampale = 'affiliate/customer/email_template_unlock';
		    			$sender_name = Mage::getStoreConfig('trans_email/ident_'.$sender.'/name', $storeId);
		    			$customer_affiliate_link = Mage::app()->getStore($storeId)->getUrl('affiliate');
		    			$data_mail['customer_name'] = $name;
		    			$data_mail['sender_name'] = $sender_name;
		    			$data_mail['store_name'] = $store_name;
		    			$data_mail['customer_affiliate_link'] = $customer_affiliate_link;
		    			Mage::helper('affiliate')->_sendEmailTransactionNew($sender,$email,$name,$teampale,$data_mail,$storeId);
                	}
                    $customer = Mage::getSingleton('affiliate/affiliatecustomers')
                        ->load($customerId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($customerIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	public function massParentAffiliateAction()
    {   
        $customerIds = $this->getRequest()->getParam('affiliatememberGrid');
        if(!is_array($customerIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select member(s)'));
        } else {
            try {
            	$parent_affiliate_id = 0;
            	$parent_affiliate = $this->getRequest()->getParam('parent_affiliate');
             	if(isset($parent_affiliate) && $parent_affiliate != ''){
             		
				 	$affiliateFilters = Mage::getModel('customer/customer')->getCollection()->addFieldToFilter('email',$parent_affiliate);
					if(sizeof($affiliateFilters) > 0 )
			      	{ 
			         	foreach ($affiliateFilters as $affiliateFilter) 
			         	{
			         	  
				          $mw_check = Mage::helper('affiliate')->getActiveAndEnableAffiliate($affiliateFilter->getId());
			         	  if($mw_check == 1)
				           {	
				           	  $parent_affiliate_id = $affiliateFilter->getId();   
				           }else{
				           		$this->_getSession()->addError($this->__('Affiliate parent invalid'));
					            $this->_redirect('*/*/index');
				 			    return;	
				           }
				           break;
			         	} 
			           
			         }else{
			         	$this->_getSession()->addError($this->__('Affiliate parent invalid'));
			            $this->_redirect('*/*/index');
		 			    return;
			         }  			  
				}
				
				$count = 0;
                foreach ($customerIds as $customerId) {
                	if($parent_affiliate_id && $parent_affiliate_id != $customerId){
                		$count = $count + 1;
                		Mage::getSingleton('affiliate/affiliatecustomers')->load($customerId)->setCustomerInvited($parent_affiliate_id)->save();
                	}	
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', $count)
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	public function productGridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('affiliate/adminhtml_affiliateprogram_edit_tab_grid', 'admin.program.products')->toHtml()
        );
    }
 	public function productAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    public function exportCsvAction()
    {
        $fileName   = 'affiliate_member.csv';
        $content    = $this->getLayout()->createBlock('affiliate/adminhtml_affiliatemember_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'affiliate_member.xml';
        $content    = $this->getLayout()->createBlock('affiliate/adminhtml_affiliatemember_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}
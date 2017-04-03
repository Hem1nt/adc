<?php
class MW_Affiliate_Helper_Data extends Mage_Core_Helper_Abstract
{  
	public function getEnterprisePro()
	{
		$modules = array_keys((array)Mage::getConfig()->getNode('modules')->children());
		if(in_array('Enterprise_Enterprise',$modules)) 
		{
				return true;
		}
		return false;
	}
	public function getDeleteCustomer()
	{
		$customer_ids = Mage::getModel('customer/customer')->getCollection()->getAllIds();
    	$customer_affiliate_ids = Mage::getModel('affiliate/affiliatecustomers')->getCollection()->getAllIds();
    	$array_customer_deletes = array_diff($customer_affiliate_ids, $customer_ids);
    	if(sizeof($array_customer_deletes)>0){
    		foreach ($array_customer_deletes as $array_customer_delete) {
    			Mage::getModel('affiliate/affiliatecustomers')->load($array_customer_delete)->delete();
    		}
    	}
	}
	public function getAffiliateHistory()
	{
		$customer_id = (int)Mage::getSingleton("customer/session")->getCustomer()->getId();
		$collectionFilter = Mage::getModel('affiliate/affiliatehistory')->getCollection()
                    	->addFieldToFilter('customer_id',$customer_id);
		return sizeof($collectionFilter);
	}
	public function getReferralCodeByCheckout()
    {
    	return Mage::getSingleton('checkout/session')->getReferralCode();
    }
    public function setReferralCode($customer_id)
    {   
    	$affiliate_customers = Mage::getModel('affiliate/affiliatecustomers')->load($customer_id);
    	$store_id = $affiliate_customers ->getStoreId();
    	$length = (int)Mage::helper('affiliate/data')->getLengthReferralCodeStore($store_id);
    	$i = 0;
		$referral_code = $this ->rand_str($length);
		while ($i == 0) {
		       $affiliate_customers_filter = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
								                        ->addFieldToFilter('referral_code', $referral_code);
		       if(sizeof($affiliate_customers_filter) > 0){
			       	$i = 0;
			       	$referral_code = $this ->rand_str($length);
		       }else {
		       			$i = 1;
		       }  				        
		}
		$referral_code_new = $referral_code;
		$affiliate_customers ->setReferralCode($referral_code_new) ->save();
    }
	public function rand_str($length = 32, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890')
	{
	    // Length of character list
	    $chars_length = (strlen($chars) - 1);
	
	    // Start our string
	    $string = $chars{rand(0, $chars_length)};
	   
	    // Generate random string
	    for ($i = 1; $i < $length; $i = strlen($string))
	    {
	        // Grab a random character from our list
	        $r = $chars{rand(0, $chars_length)};
	       
	        // Make sure the same two characters don't appear next to each other
	        if ($r != $string{$i - 1}) $string .=  $r;
	    }
	   
	    // Return the string
	    return $string;
	}
    public function getCustomerIdByReferralCode($referral_code, $cokie)
    {   
    	$result = $cokie;
    	if(isset($referral_code) && $referral_code != ''){
	    	$collectionCustomers = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
		                    					->addFieldToFilter('referral_code',$referral_code);
		    if(sizeof($collectionCustomers) >0){
		    	foreach ($collectionCustomers as $collectionCustomer) {
		    		$customer_id = $collectionCustomer ->getCustomerId();
		    		$active = $collectionCustomer ->getActive();
	    			$status = $collectionCustomer ->getStatus();
	    			break;
		    	}
		    	if($active == MW_Affiliate_Model_Statusactive::ACTIVE && $status == MW_Affiliate_Model_Statusreferral::ENABLED){
		            $result = $customer_id;		
		        }
		    }
    	}
    	return $result;
    	
    }
    public function checkReferralCode($referral_code)
    {   
    	$result = 0;
    	$collectionCustomers = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
	                    					->addFieldToFilter('referral_code',$referral_code);
	    if(sizeof($collectionCustomers) >0){
	    	foreach ($collectionCustomers as $collectionCustomer) {
	    		$active = $collectionCustomer ->getActive();
    			$status = $collectionCustomer ->getStatus();
    			break;
	    	}
	    	if($active == MW_Affiliate_Model_Statusactive::ACTIVE && $status == MW_Affiliate_Model_Statusreferral::ENABLED){
	            $result = 1;		
	        }
	    }
    	return $result;
    }
	public function checkReferralCodeCart($referral_code)
    {   
    	$result = 0;
    	$customer_id = (int)Mage::getSingleton("customer/session")->getCustomer()->getId();
    	$collectionCustomers = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
	                    					->addFieldToFilter('referral_code',$referral_code);
	    if(sizeof($collectionCustomers) >0){
	    	foreach ($collectionCustomers as $collectionCustomer) {
	    		$customer_id_referral_code = $collectionCustomer ->getCustomerId();
	    		$active = $collectionCustomer ->getActive();
    			$status = $collectionCustomer ->getStatus();
    			break;
	    	}
	    	if($active == MW_Affiliate_Model_Statusactive::ACTIVE && $status == MW_Affiliate_Model_Statusreferral::ENABLED){
	            $result = 1;
	            if(isset($customer_id) && $customer_id == $customer_id_referral_code)	$result = 0;	
	        }
	    }
    	return $result;
    }
    public function setMemberGroupAffiliate($group_id, $customer_id)
    {
    	$data = array('customer_id'=>$customer_id,
                      'group_id'=>$group_id);
    	
    	Mage::getModel('affiliate/affiliategroupmember')->setData($data)->save();
    }
    
	public function setTotalMemberProgram()
	{
		$collectionPrograms = Mage::getModel('affiliate/affiliateprogram')->getCollection();
		if(sizeof($collectionPrograms) >0){
			foreach ($collectionPrograms as $collectionProgram) {
				$total_member = 0;
				$program_id = $collectionProgram ->getProgramId();
				$groupPrograms = Mage::getModel('affiliate/affiliategroupprogram')->getCollection()
										->addFieldToFilter('program_id',$program_id);
				if(sizeof($groupPrograms) >0){
					foreach ($groupPrograms as $groupProgram) {
						$group_id = $groupProgram ->getGroupId();
						$customerPrograms = Mage::getModel('affiliate/affiliategroupmember')->getCollection()
											->addFieldToFilter('group_id',$group_id);
						$total_member = $total_member + sizeof($customerPrograms);
					}						
					
				}
				Mage::getModel('affiliate/affiliateprogram')->load($program_id)->setTotalMembers($total_member)->save();
			}
		}
		
	}
	public function getAffiliateActive()
	{
		$customer_id = (int)Mage::getSingleton("customer/session")->getCustomer()->getId();
		$collectionFilter = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
                    	->addFieldToFilter('customer_id',$customer_id)
                    	->addFieldToFilter('active',MW_Affiliate_Model_Statusactive::ACTIVE);
		return sizeof($collectionFilter);
	}
	public function formatMoney($money)
	{
		return Mage::helper('core')->currency($money);
	}
	public function getGroupNameByCustomer($customer_id)
	{
		  $customerGroups = Mage::getModel('affiliate/affiliategroupmember')->getCollection()
									->addFieldToFilter('customer_id',$customer_id);
		  $group_name  = '';
		  if(sizeof($customerGroups) >0 ){
		  	 foreach ($customerGroups as $customerGroup) {
			  	  $group_id = $customerGroup ->getGroupId();
			  	  break;
			  }
			  $affiliate_group = Mage::getModel('affiliate/affiliategroup')->load($group_id);
		      $group_name = $affiliate_group ->getGroupName();
		  }						
		  
	      return $group_name;
	}
	public function getActiveAndEnableAffiliate($customer_id)
	{
		$customer_id = (int)$customer_id;
		$collectionFilter = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
		                    	->addFieldToFilter('customer_id',$customer_id)
		                    	->addFieldToFilter('active',MW_Affiliate_Model_Statusactive::ACTIVE)
		                    	->addFieldToFilter('status',MW_Affiliate_Model_Statusreferral::ENABLED);
		return sizeof($collectionFilter);
	}
	public function getLockAffiliate($customer_id)
	{   
		$customer_id = (int)$customer_id;
		//echo $customer_id;
		$collectionFilter = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
		                    	->addFieldToFilter('customer_id',$customer_id)
		                    	->addFieldToFilter('status',MW_Affiliate_Model_Statusreferral::LOCKED);
		                    	//echo $collectionFilter->getSelect(); exit;
		return sizeof($collectionFilter);
	}
	public function getAffiliateLock()
	{   
		$customer_id = (int)Mage::getSingleton("customer/session")->getCustomer()->getId();
		$collectionFilter = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
		                    	->addFieldToFilter('customer_id',$customer_id)
		                    	->addFieldToFilter('status',MW_Affiliate_Model_Statusreferral::LOCKED);
		return sizeof($collectionFilter);
	}
	public function getActiveAffiliate($customer_id)
	{   
		$customer_id = (int)$customer_id;
		$collectionFilter = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
		                    	->addFieldToFilter('customer_id',$customer_id)
		                    	->addFieldToFilter('active',MW_Affiliate_Model_Statusactive::ACTIVE);
		return sizeof($collectionFilter);
	}
	// customer khong la thanh vien affiliate va khong co customer invited tra ve 0
	// hoac khong ton tai customer id tra ve 0
	// nguoc lai tra ve 1
	public function checkCustomer($customer_id)
	{   
		$result = 0;
		if($customer_id){
			
			$result = 1;
			$active = $this ->getActiveAffiliate($customer_id);
			$customer_invited = Mage::getModel('affiliate/affiliatecustomers')->load($customer_id)->getCustomerInvited();
			if(!$customer_invited) $customer_invited = 0;
			if($active == 0 && $customer_invited == 0 && $customer_invited == '') $result = 0;
		}
		
		return $result;
	}
	public function getEnabled()
	{
		return Mage::getStoreConfig('affiliate/config/enabled');
	}
	public function getEnabledStore($store_id)
	{   
		//$store_id = $order->getStoreId();
		//$store_id = Mage::app()->getStore()->getId();
		//Mage::helper('affiliate/data')->getEnabledStore($store_id);
		return Mage::getStoreConfig('affiliate/config/enabled',$store_id);
	}
	public function checkGroupExits($group_id)
	{
		$collectionFilter = Mage::getModel('affiliate/affiliategroup')->getCollection()
		                    	              ->addFieldToFilter('group_id',$group_id);

		return sizeof($collectionFilter);
	}
	public function setMemberDefaultGroupAffiliate($customer_id,$store_id=null)
    {
    	if($store_id == null) $store_id = Mage::app()->getStore()->getId();
    	$group_id = $this->getDefaultGroupAffiliateStore($store_id);
    	$check_group = $this->checkGroupExits($group_id);
    	if($check_group == 0) $group_id = 1;
    	$data = array('customer_id'=>$customer_id,
                      'group_id'=>$group_id);
    	
    	Mage::getModel('affiliate/affiliategroupmember')->setData($data)->save();
    }
	public function getTimeCookieStore($store_id)
	{
		return Mage::getStoreConfig('affiliate/config/affiliate_cookie',$store_id);
	}
	public function getAffiliatePositionStore($store_id)
	{
		return Mage::getStoreConfig('affiliate/general/affiliate_position',$store_id);
	}
	public function getAffiliateDiscountStore($store_id)
	{
		return Mage::getStoreConfig('affiliate/general/affiliate_discount',$store_id);
	}
	public function getAffiliateTaxtStore($store_id)
	{
		return Mage::getStoreConfig('affiliate/general/affiliate_tax',$store_id);
	}
	public function getAutoApproveRegisterStore($store_id)
	{
		return Mage::getStoreConfig('affiliate/config/auto_approve',$store_id);
	}
	public function getDefaultGroupAffiliateStore($store_id)
	{
		return Mage::getStoreConfig('affiliate/config/default_group',$store_id);
	}
	public function setNewCustomerInvitedStore($store_id)
	{
		return Mage::getStoreConfig('affiliate/config/set_customerinvited',$store_id);
	}
	public function getAffiliateCommissionbyThemselves($store_id)
	{
		return Mage::getStoreConfig('affiliate/general/affiliate_commission',$store_id);
	}
	public function getStatusAddCommissionStore($store_id)
	{
		return Mage::getStoreConfig('affiliate/general/status_add_commission',$store_id);
	}
	public function getStatusSubtractCommissionStore($store_id)
	{
		return Mage::getStoreConfig('affiliate/general/status_subtract_commission',$store_id);
	}
	public function getDicountWhenRefundProductStore($store_id)
	{
		return Mage::getStoreConfig('affiliate/general/enabled_reward',$store_id);
	}
	public function getFeeStore($store_id)
	{
		return Mage::getStoreConfig('affiliate/money/affiliate_fee_taken',$store_id);
	}
	public function getWithdrawMinStore($store_id)
	{
		return Mage::getStoreConfig('affiliate/money/affiliate_withdraw_min',$store_id);
	}
	public function getWithdrawMaxStore($store_id)
	{
		return Mage::getStoreConfig('affiliate/money/affiliate_withdraw_max',$store_id);
	}
	public function getWithdrawnPeriodStore($store_id)
	{
		return Mage::getStoreConfig('affiliate/money/affiliate_withdrawn_period',$store_id);
	}
	public function getWithdrawnDayStore($store_id)
	{
		return Mage::getStoreConfig('affiliate/money/affiliate_withdrawn_day',$store_id);
	}
	public function getWithdrawnMonthStore($store_id)
	{
		return Mage::getStoreConfig('affiliate/money/affiliate_withdrawn_month',$store_id);
	}
	public function getLengthReferralCodeStore($store_id)
	{
		return Mage::getStoreConfig('affiliate/config/referral_code',$store_id);
	}
	public function getAutoSignUpAffiliateStore($store_id)
	{
		return Mage::getStoreConfig('affiliate/config/auto_signup_affiliate',$store_id);
	}
	public function getOverWriteRegisterStore($store_id)
	{
		return Mage::getStoreConfig('affiliate/config/overwrite_register',$store_id);
	}
	public function getShowReferralCodeRegisterStore($store_id)
	{
		return Mage::getStoreConfig('affiliate/config/show_referral_code_register',$store_id);
	}
	public function getShowSignUpFormAffiliateRegisterStore($store_id)
	{
		return Mage::getStoreConfig('affiliate/config/signup_affiliate',$store_id);
	}
	public function getShowReferralCodeCartStore($store_id)
	{
		return Mage::getStoreConfig('affiliate/config/show_referral_code_cart',$store_id);
	}
	public function getAffiliateShareStore($store_id)
	{
		return Mage::getStoreConfig('affiliate/config/affiliate_share',$store_id);
	}
	public function getGatewayStore()
	{
		return Mage::getStoreConfig('affiliate/money/gateway');
	}
//	public function getLink(Mage_Customer_Model_Customer $customer)
//	{
//		return trim(Mage::getUrl('affiliate/invited'),"/")."?c=".$customer->getEmail();
//	}
	public function getLink(Mage_Customer_Model_Customer $customer)
	{   
		$Url = Mage::getBaseUrl();
		return trim($Url)."?mw_aref=".md5($customer->getEmail());
	}
	public function getLinkBanner(Mage_Customer_Model_Customer $customer,$link_banner)
	{
//		return trim(Mage::getUrl('invitation'),"/")."?c=".$customer->getEmail();
		return trim($link_banner)."?mw_aref=".md5($customer->getEmail());
	}
	public function getAffiliateLink()
	{
		return Mage::getUrl('customer/account');
	}
	public function getInvitationHistory()
	{
		$customer_id = (int)Mage::getSingleton("customer/session")->getCustomer()->getId();
		$collectionFilter = Mage::getModel('affiliate/affiliateinvitation')->getCollection()
                    			->addFieldToFilter('customer_id',$customer_id);
		return sizeof($collectionFilter);
	}
	public function getSizeAffiliateHistory()
	{
		$customer_id = (int)Mage::getSingleton("customer/session")->getCustomer()->getId();
		$collectionFilter = Mage::getModel('affiliate/affiliatehistory')->getCollection()
                    			->addFieldToFilter('customer_invited',$customer_id);
		return sizeof($collectionFilter);
	}
	public function getSizeWithdrawnHistory()
	{
		$customer_id = (int)Mage::getSingleton("customer/session")->getCustomer()->getId();
		$collectionFilter = Mage::getModel('affiliate/affiliatewithdrawn')->getCollection()
                    			->addFieldToFilter('customer_id',$customer_id);
		return sizeof($collectionFilter);
	}
	public function getProductName($product_id)
	{   
		return Mage::getModel('catalog/product')->load($product_id)->getName();
	}
	public function getProgramName($program_id)
	{   
		return Mage::getModel('affiliate/affiliateprogram')->load($program_id)->getProgramName();
	}
	public function getLabelPaymentGateway($payment_gateway)
	{   
		$label = '';
		$gateways = unserialize(Mage::helper('affiliate/data')->getGatewayStore());
		foreach ($gateways as $gateway) 
		{
			if($payment_gateway == $gateway['gateway_value']) $label = $gateway['gateway_title']; 
		}
		return $label;
	}
	public function getPaymentLevel()
	{   
		$result = array();
		$payment_levels = Mage::getStoreConfig('affiliate/config/affiliate_withdrawn_level');
		$payment_level= explode(',',$payment_levels);
		foreach ($payment_level as $_payment_level) {
			$result[] = array('value'=>$_payment_level, 'label'=>Mage::helper('affiliate')->__($_payment_level));
		}
		return $result;
	}
	public function getLinkCustomer($customer_id,$detail)
	{   
		$url = "adminhtml/customer/edit";
		$result='';
		//$url = Mage::getUrl($url,array('id'=>$customer_id));
		$result = Mage::helper('affiliate')->__("<b><a href=\"%s\">%s</a></b>",Mage::helper('adminhtml')->getUrl($url,array('id'=>$customer_id)),$detail);
		//$result = Mage::helper('affiliate')->__("<b><a href='$url'>$detail</a></b>");
		return $result;
	}
	public function getWithdrawnPeriod()
	{     
		  $period='';
		  $store_id = Mage::app()->getStore()->getId();
		  $withdrawn_period = (int)Mage::helper('affiliate/data')->getWithdrawnPeriodStore($store_id);
	      if($withdrawn_period == 1)
	      {
	      	$withdrawn_days = (int)Mage::helper('affiliate/data')->getWithdrawnDayStore($store_id);
	      	$days = Mage::getModel('affiliate/days')->getLabel($withdrawn_days);
	      	$period = $this->__('Weekly, on %s',$days);
	      }
	      else if($withdrawn_period == 2)
	      {
	      	$withdrawn_month = (int)Mage::helper('affiliate/data')->getWithdrawnMonthStore($store_id);
	      	$period = $this->__('Monthly, Date %s',$withdrawn_month);
	      }
	      return $period;
	}
	// customer do tham gia bao nhieu program theo customer id
	public function getSizeMemberProgram()
	{
		$customer_id = (int)Mage::getSingleton("customer/session")->getCustomer()->getId();
		$program_ids = array();
    	$customer_groups =  Mage::getModel('affiliate/affiliategroupmember')->getCollection()
    										->addFieldToFilter('customer_id',$customer_id);
    	if(sizeof($customer_groups) >0 )
    	{   
    		$group_id = 0;
	    	foreach ($customer_groups as $customer_group) {
	    		$group_id = $customer_group ->getGroupId();
	    		break;
	    	}
	    	$customer_programs = Mage::getModel('affiliate/affiliategroupprogram')->getCollection()
	    										->addFieldToFilter('group_id',$group_id);
	    	foreach ($customer_programs as $customer_program) {
	    		$program_ids[] = $customer_program->getProgramId();
	    	}
    	}
        $programs = Mage::getModel('affiliate/affiliateprogram')->getCollection()
        			 ->addFieldtoFilter('program_id',array('in'=>$program_ids))
					 ->addFieldtoFilter('status',MW_Affiliate_Model_Statusprogram::ENABLED);
		return sizeof($programs);
	}
	protected function _getSession()
    {
    	return Mage::getSingleton('core/session');
    }
	public function updateAffiliateInvition($customer_id, $cokie, $clientIP)
	{
		if($cokie!= 0 )
    	{
    		$email = Mage::getModel('customer/customer')->load($customer_id)->getEmail();
    		$collection = Mage::getModel('affiliate/affiliateinvitation')->getCollection()
							    				  ->addFieldToFilter('customer_id',$cokie)
						                    	  ->addFieldToFilter('email',$email);
		    // neu ban be dc moi dang ky lam thanh vien cua website ?
		    // voi email trung voi email moi se update lai trang thai 
		    if(sizeof($collection)>0)
		    {
		    	foreach ($collection as $obj) 
		    	{
		    		$obj->setStatus(MW_Affiliate_Model_Statusinvitation::REGISTER);
		    		$obj->setIp($clientIP);
		    		$obj->setInvitationTime(now());
		    		$obj->save();
		    	}
		    }
		    // nguoc lai luu moi vao csdl
		    else
		    {
		    	$historyData = array('customer_id'=>$cokie,
	                        		 'email'=>$email, 
	                        		 'status'=>MW_Affiliate_Model_Statusinvitation::REGISTER, 
	                        		 'ip'=>$clientIP,
	                        		 'invitation_time'=>now());
                Mage::getModel('affiliate/affiliateinvitation')->setData($historyData)->save();
		    }	
        }
	}
	public function updateAffiliateInvitionNew($customer_id, $cokie, $clientIP,$referral_from,$referral_from_domain,$referral_to,$invitation_type)
	{
		if($cokie!= 0 )
    	{
    		if($invitation_type == MW_Affiliate_Model_Typeinvitation::REFERRAL_CODE){
    			$referral_from = '';
    			$referral_to = '';
    			$referral_from_domain = '';
    		}
    		$email = Mage::getModel('customer/customer')->load($customer_id)->getEmail();
    		$collection = Mage::getModel('affiliate/affiliateinvitation')->getCollection()
							    				  ->addFieldToFilter('customer_id',$cokie)
						                    	  ->addFieldToFilter('email',$email);
		    // neu ban be dc moi dang ky lam thanh vien cua website ?
		    // voi email trung voi email moi se update lai trang thai 
		    if(sizeof($collection)>0)
		    {
		    	foreach ($collection as $obj) 
		    	{
		    		$obj->setStatus(MW_Affiliate_Model_Statusinvitation::REGISTER);
		    		$obj->setIp($clientIP);
		    		$obj->setInvitationTime(now());
		    		$obj->setCountClickLink(0);
		    		$obj->setCountRegister(1);
		    		$obj->setCountPurChase(0);
		    		$obj->setReferralFrom($referral_from);
		    		$obj->setReferralFromDomain($referral_from_domain);
		    		$obj->setReferralTo($referral_to);
		    		$obj->setOrderId('');
		    		$obj->setInvitationType($invitation_type);
		    		$obj->save();
		    	}
		    }
		    // nguoc lai luu moi vao csdl
		    else
		    {
		    	$historyData = array('customer_id'=>$cokie,
	                        		 'email'=>$email, 
	                        		 'status'=>MW_Affiliate_Model_Statusinvitation::REGISTER, 
	                        		 'ip'=>$clientIP,
		    						 'count_click_link'=> 0,
                        			 'count_register'=> 1, 
                        			 'count_purchase'=> 0,
                                 	 'referral_from'=>$referral_from, 
		    						 'referral_from_domain'=>$referral_from_domain,
                        			 'referral_to'=>$referral_to,
                        			 'order_id'=>'',
		    	                     'invitation_type'=> $invitation_type,
	                        		 'invitation_time'=>now());
                Mage::getModel('affiliate/affiliateinvitation')->setData($historyData)->save();
		    }	
        }
	}
	public function processWithdrawn($status, $withdrawn_ids)
	{
		// thuc hien
        $is_complete=0;
        $is_cancel=0;
        foreach($withdrawn_ids as $withdrawn_id)
        {
        	$affiliatedrawn = Mage::getModel('affiliate/affiliatewithdrawn')->load($withdrawn_id);
    		$withdrawn_status = $affiliatedrawn ->getStatus();
    		if($withdrawn_status == MW_Affiliate_Model_Status::COMPLETE) $is_complete=1;
    		else if($withdrawn_status == MW_Affiliate_Model_Status::CANCELED)$is_cancel=1;
        }
        if($status == MW_Affiliate_Model_Status::COMPLETE)
        {   
        	if(($is_complete==0) && ($is_cancel==0))
        	{
        		foreach($withdrawn_ids as $withdrawn_id)
        		{
        			$affiliatedrawn = Mage::getModel('affiliate/affiliatewithdrawn')->load($withdrawn_id);
    				$withdrawn_amount = $affiliatedrawn ->getWithdrawnAmount();
    				$customer_Id = $affiliatedrawn ->getCustomerId();
        			$affiliatedrawn->setStatus(MW_Affiliate_Model_Status::COMPLETE)->save();
        			$affiliatedrawn->setWithdrawnTime(now())->save();
        			
        			// gui mail cho khach hang khi rut tien thanh cong
        			$this ->sendMailCustomerWithdrawnComplete($customer_Id, $withdrawn_amount);
        		
        			// cap nhat lai trang thai trong bang credit history
        			$collection = Mage::getModel('credit/credithistory')->getCollection()
			                    	->addFieldToFilter('type_transaction',MW_Credit_Model_Transactiontype::WITHDRAWN)
			                    	->addFieldToFilter('customer_id',$customer_Id)
			                    	->addFieldToFilter('transaction_detail',$withdrawn_id)
			                    	->addFieldToFilter('status',MW_Credit_Model_Orderstatus::PENDING);
                    $affiliate_customer = Mage::getModel('affiliate/affiliatecustomers')->load($customer_Id);
		            foreach($collection as $credithistory)
		             {  
		             	$oldTotalPaid = $affiliate_customer->getTotalPaid();
		             	$amount = $credithistory->getAmount();
		             	$newTotalPaid = $oldTotalPaid - $amount;
		             	$affiliate_customer->setData('total_paid',$newTotalPaid)->save();
		             	
		             	$status_credit = MW_Credit_Model_Orderstatus::COMPLETE;
						$credithistory->setStatus($status_credit)->save();
						//$credithistory->setCreatedTime(now())->save();
						
		             }
        		}
        		Mage::getSingleton('adminhtml/session')->addSuccess("You have successfully updated the withdrawn(s) status");
        	}
        	else
        		if($is_complete==1 )  Mage::getSingleton('adminhtml/session')->addError("Withdrawn_id you have chosen, having status: Complete");
        	else
        		if($is_cancel==1)  Mage::getSingleton('adminhtml/session')->addError("Withdrawn_id you have chosen, having status: Canceled");
        	
        }
        else if($status== MW_Affiliate_Model_Status::CANCELED)
        {
        	
        	if(($is_complete==0)&& ($is_cancel==0))
        	{
        		foreach($withdrawn_ids as $withdrawn_id)
        		{
        		
        			$affiliatedraw = Mage::getModel('affiliate/affiliatewithdrawn')->load($withdrawn_id);
    				$withdrawn_amount = $affiliatedraw ->getWithdrawnAmount();
    				$customer_Id = $affiliatedrawn ->getCustomerId();
        			$affiliatedraw->setStatus(MW_Affiliate_Model_Status::CANCELED)->save();
        			$affiliatedraw->setWithdrawnTime(now())->save();
        			
        			// gui mail cho khach hang khi rut tien that bai
        			$this ->sendMailCustomerWithdrawnCancel($customer_Id, $withdrawn_amount);
		    		
        			// cap nhat lai trang thai trong bang credit history, them kieu cancel withdrawn
        			$collection = Mage::getModel('credit/credithistory')->getCollection()
				                    		->addFieldToFilter('type_transaction',MW_Credit_Model_Transactiontype::WITHDRAWN)
				                    		->addFieldToFilter('customer_id',$customer_Id)
				                    		->addFieldToFilter('transaction_detail',$withdrawn_id)
				                    		->addFieldToFilter('status',MW_Credit_Model_Orderstatus::PENDING);
                    $creditcustomer = Mage::getModel('credit/creditcustomer')->load($customer_Id);
                    $oldcredit=$creditcustomer->getCredit();
		            foreach($collection as $credithistory)
		             {  
		             	$amount=$credithistory->getAmount();
						$newcredit = $oldcredit - $amount;
		             	$status_credit = MW_Credit_Model_Orderstatus::CANCELED;
		             	$credithistory->setStatus($status_credit)->save();
		             	$creditcustomer->setCredit($newcredit)->save();
		             	
						// luu them vao credit history kieu cancel withdraw
			       		$historyData = array('customer_id'=>$customer_Id,
						 					 'type_transaction'=>MW_Credit_Model_Transactiontype::CANCEL_WITHDRAWN, 
						 					 'status'=>MW_Credit_Model_Orderstatus::COMPLETE,
							     		     'transaction_detail'=>$withdrawn_id, 
							           		 'amount'=>-$amount, 
							         		 'beginning_transaction'=>$oldcredit,
							        		 'end_transaction'=>$newcredit,
							           	     'created_time'=>now());
			   			Mage::getModel("credit/credithistory")->setData($historyData)->save();
						
		             }
        		}
        		Mage::getSingleton('adminhtml/session')->addSuccess("You have successfully updated the Withdrawn(s) status");
        	}
        	else
        		if($is_complete==1 )  Mage::getSingleton('adminhtml/session') ->addError("Withdrawn_id you have chosen, having status: Complete");
        	else
        		if($is_cancel==1)  Mage::getSingleton('adminhtml/session') ->addError("Withdrawn_id you have chosen, having status: Canceled");
        }
	}
	public function sendMailCustomerWithdrawnCancel($customer_id, $withdrawn_amount)
	{
		//$storeId = Mage::app()->getStore()->getId();
		$storeId = Mage::getModel('customer/customer')->load($customer_id)->getStoreId();
        $store_name = Mage::getStoreConfig('general/store_information/name', $storeId);
    	$sender = Mage::getStoreConfig('affiliate/customer/email_sender', $storeId);
    	$email = Mage::getModel('customer/customer')->load($customer_id)->getEmail();
    	$name = Mage::getModel('customer/customer')->load($customer_id)->getName();
    	$teampale = 'affiliate/customer/email_template_withdrawn_cancel';
    	$sender_name = Mage::getStoreConfig('trans_email/ident_'.$sender.'/name', $storeId);
    	$link = Mage::app()->getStore($storeId)->getUrl('affiliate');
    	$data_mail['customer_name'] = $name;
    	$data_mail['amount'] = Mage::helper('core')->currency($withdrawn_amount,true,false);
    	$data_mail['sender_name'] = $sender_name;
    	$data_mail['store_name'] = $store_name;
    	$data_mail['link'] = $link;
    	$this ->_sendEmailTransactionNew($sender,$email,$name,$teampale,$data_mail,$storeId);
	}
	public function sendMailCustomerWithdrawnComplete($customer_id, $withdrawn_amount)
	{
		//$storeId = Mage::app()->getStore()->getId();
		$storeId = Mage::getModel('customer/customer')->load($customer_id)->getStoreId();
        $store_name = Mage::getStoreConfig('general/store_information/name', $storeId);
    	$sender = Mage::getStoreConfig('affiliate/customer/email_sender', $storeId);
    	$email = Mage::getModel('customer/customer')->load($customer_id)->getEmail();
    	$name = Mage::getModel('customer/customer')->load($customer_id)->getName();
    	$teampale = 'affiliate/customer/email_template_withdrawn_complete';
    	$sender_name = Mage::getStoreConfig('trans_email/ident_'.$sender.'/name', $storeId);
    	$customer_withdrawal_link = Mage::app()->getStore($storeId)->getUrl('affiliate/index/withdrawn');
    	$data_mail['customer_name'] = $name;
    	$data_mail['amount'] = Mage::helper('core')->currency($withdrawn_amount,true,false);
    	$data_mail['sender_name'] = $sender_name;
    	$data_mail['store_name'] = $store_name;
    	$data_mail['customer_withdrawal_link'] = $customer_withdrawal_link;
    	$this->_sendEmailTransactionNew($sender,$email,$name,$teampale,$data_mail,$storeId);
	}
	public function sendMailCustomerRequestWithdrawn($customer_id, $withdrawn_amount)
	{
		//$storeId = Mage::app()->getStore()->getId();
		$storeId = Mage::getModel('customer/customer')->load($customer_id)->getStoreId();
   		$store_name = Mage::getStoreConfig('general/store_information/name', $storeId);
    	$sender = Mage::getStoreConfig('affiliate/customer/email_sender', $storeId);
    	$email = Mage::getModel('customer/customer')->load($customer_id)->getEmail();
    	$name = Mage::getModel('customer/customer')->load($customer_id)->getName();
    	$teampale = 'affiliate/customer/email_template_withdrawn';
    	$sender_name = Mage::getStoreConfig('trans_email/ident_'.$sender.'/name', $storeId);
    	$customer_withdrawal_link = Mage::app()->getStore($storeId)->getUrl('affiliate/index/withdrawn');
    	$data_mail['customer_name'] = $name;
    	$data_mail['amount'] = Mage::helper('core')->currency($withdrawn_amount,true,false);
    	$data_mail['sender_name'] = $sender_name;
    	$data_mail['store_name'] = $store_name;
    	$data_mail['customer_withdrawal_link'] = $customer_withdrawal_link;
    	$this ->_sendEmailTransactionNew($sender,$email,$name,$teampale,$data_mail,$storeId);
	}
    public function sendEmailCustomerPending($customer_id)
    {
    	//$storeId = Mage::app()->getStore()->getId();
    	$storeId = Mage::getModel('customer/customer')->load($customer_id)->getStoreId();
    	$store_name = Mage::getStoreConfig('general/store_information/name', $storeId);
    	$sender = Mage::getStoreConfig('affiliate/customer/email_sender', $storeId);
    	$email = Mage::getModel('customer/customer')->load($customer_id)->getEmail();
    	$name = Mage::getModel('customer/customer')->load($customer_id)->getName();
    	$teampale = 'affiliate/customer/email_template';
    	$sender_name = Mage::getStoreConfig('trans_email/ident_'.$sender.'/name', $storeId);
    	$link = Mage::app()->getStore($storeId)->getUrl('affiliate');
    	$data_mail['customer_name'] = $name;
    	$data_mail['sender_name'] = $sender_name;
    	$data_mail['store_name'] = $store_name;
    	$data_mail['link'] = $link;
    	$this->_sendEmailTransactionNew($sender,$email,$name,$teampale,$data_mail,$storeId);
    }
    public function sendMailCustomerActiveAffiliate($customer_id)
    {
    	//$storeId = Mage::app()->getStore()->getId();
    	$storeId = Mage::getModel('customer/customer')->load($customer_id)->getStoreId();
        $store_name = Mage::getStoreConfig('general/store_information/name', $storeId);
    	$sender = Mage::getStoreConfig('affiliate/customer/email_sender', $storeId);
    	$email = Mage::getModel('customer/customer')->load($customer_id)->getEmail();
    	$name = Mage::getModel('customer/customer')->load($customer_id)->getName();
    	$teampale = 'affiliate/customer/email_template_successful';
    	$sender_name = Mage::getStoreConfig('trans_email/ident_'.$sender.'/name', $storeId);
    	$customer_affiliate_link = Mage::app()->getStore($storeId)->getUrl('affiliate');
    	$data_mail['customer_name'] = $name;
    	$data_mail['sender_name'] = $sender_name;
    	$data_mail['store_name'] = $store_name;
    	$data_mail['customer_affiliate_link'] = $customer_affiliate_link;
    	$this ->_sendEmailTransactionNew($sender,$email,$name,$teampale,$data_mail,$storeId);
    }
    public function sendMailCustomerNotActiveAffiliate($customer_id)
    {
    	//$storeId = Mage::app()->getStore()->getId();
    	$storeId = Mage::getModel('customer/customer')->load($customer_id)->getStoreId();
	    $store_name = Mage::getStoreConfig('general/store_information/name', $storeId);
	    $sender = Mage::getStoreConfig('affiliate/customer/email_sender', $storeId);
	    $email = Mage::getModel('customer/customer')->load($customer_id)->getEmail();
	    $name = Mage::getModel('customer/customer')->load($customer_id)->getName();
	    $teampale = 'affiliate/customer/email_template_unsuccessful';
	    $sender_name = Mage::getStoreConfig('trans_email/ident_'.$sender.'/name', $storeId);
	    $link = Mage::app()->getStore($storeId)->getUrl('affiliate');
	    $data_mail['customer_name'] = $name;
	    $data_mail['sender_name'] = $sender_name;
	    $data_mail['store_name'] = $store_name;
	    $data_mail['link'] = $link;
	    $this ->_sendEmailTransactionNew($sender,$email,$name,$teampale,$data_mail,$storeId);
    }
    public function sendEmailAdminActiveAffiliate($customer_id) 
    {
    	//$storeId = Mage::app()->getStore()->getId();
    	$storeId = Mage::getModel('customer/customer')->load($customer_id)->getStoreId();
		$store_name = Mage::getStoreConfig('general/store_information/name', $storeId);
    	$email = Mage::getModel('customer/customer')->load($customer_id)->getEmail();
    	$name = Mage::getModel('customer/customer')->load($customer_id)->getName();
    	$validator = new Zend_Validate_EmailAddress();
    	$sender_admin = Mage::getStoreConfig('affiliate/admin_customer/email_sender', $storeId);
    	$sender_name_admin = Mage::getStoreConfig('trans_email/ident_'.$sender_admin.'/name', $storeId);
    	$teampale_admin = 'affiliate/admin_customer/email_template';
    	$email_adminss = Mage::getStoreConfig('affiliate/admin_customer/email_to');
    	$name_admin = null;
    	$data_mail_admin['customer_name'] = $name;
    	$data_mail_admin['link_admin'] = Mage::getUrl('adminhtml');
    	$data_mail_admin['sender_name_admin'] = $sender_name_admin;
    	$data_mail_admin['store_name'] = $store_name;
    	$data_mail_admin['customer_email'] = $email;
    	if(substr_count($email_adminss,',')==0)
    	{
    		if($validator->isValid($email_adminss)) 
    		{
    			$this->_sendEmailTransactionNew($sender_admin,$email_adminss,$name_admin,$teampale_admin,$data_mail_admin,$storeId);
    		}
    	}
    	else if(substr_count($email_adminss,',') > 0)
    	{
    		$email_admins = explode(",",$email_adminss);
    		foreach ($email_admins as $email_admin) 
    		{
	    		if($validator->isValid($email_admin)) 
	    		{
	    			$this->_sendEmailTransactionNew($sender_admin,$email_admin,$name_admin,$teampale_admin,$data_mail_admin,$storeId);
	    		}
    		}
    	}
    }
	public function _sendEmailTransaction($sender, $emailto, $name, $template, $data)
   	{   
   		$data['subject'] = 'Affiliate system !';
		$storeId = Mage::app()->getStore()->getId();  
   		$templateId = Mage::getStoreConfig($template,$storeId);
		//$customer = $this->_getSession()->getCustomer();
		  $translate  = Mage::getSingleton('core/translate');
		  $translate->setTranslateInline(false);
		  //$sender = Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $storeId);
		 // if(Mage::getStoreConfig('affiliate/invitation/using_customer_email'))
		  //	$sender = array('name'=>$customer->getName(),'email'=>$customer->getEmail());
		  try{
		  	
			  Mage::getModel('core/email_template')
			      ->sendTransactional(
			      $templateId, 
			      $sender, 
			      $emailto, 
			      $name, 
			      $data, 
			      $storeId);
			  $translate->setTranslateInline(true);
			  //echo'b';die();
		  }catch(Exception $e){
		  		//echo'a';die();
		  		$this->_getSession()->addError($this->__("Email can not send !"));
		  }
   	}
	public function _sendEmailTransactionNew($sender, $emailto, $name, $template, $data, $storeId)
   	{   
   		$data['subject'] = 'Affiliate system !';
		//$storeId = Mage::app()->getStore()->getId();  
   		$templateId = Mage::getStoreConfig($template,$storeId);
		//$customer = $this->_getSession()->getCustomer();
		  $translate  = Mage::getSingleton('core/translate');
		  $translate->setTranslateInline(false);
		  //$sender = Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $storeId);
		 // if(Mage::getStoreConfig('affiliate/invitation/using_customer_email'))
		  //	$sender = array('name'=>$customer->getName(),'email'=>$customer->getEmail());
		  try{
		  	
			  Mage::getModel('core/email_template')
			      ->sendTransactional(
			      $templateId, 
			      $sender, 
			      $emailto, 
			      $name, 
			      $data, 
			      $storeId);
			  $translate->setTranslateInline(true);
			  //echo'b';die();
		  }catch(Exception $e){
		  		//echo'a';die();
		  		$this->_getSession()->addError($this->__("Email can not send !"));
		  }
   	}
	
}
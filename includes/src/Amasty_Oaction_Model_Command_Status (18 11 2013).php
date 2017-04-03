<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2010-2011 Amasty (http://www.amasty.com)
* @package Amasty_Oaction
*/
class Amasty_Oaction_Model_Command_Status extends Amasty_Oaction_Model_Command_Abstract
{ 
    public function __construct($type)
    {
        parent::__construct($type);
        $this->_label      = 'Change Status';
        $this->_fieldLabel = 'To';
    } 
        
    /**
     * Executes the command
     *
     * @param array $ids product ids
     * @param string $val field value
     * @return string success message if any
     */   
    public function execute($ids, $val)
    {  
	   //echo "asdasd--".$val;exit;
        $success = parent::execute($ids, $val);
        
        $numAffectedOrders = 0;

        $hlp = Mage::helper('amoaction'); 
        foreach($ids as $id)
		{
			$order = Mage::getModel('sales/order')->load($id);
		
			
			switch($val)
			{
				  	
			case "Money Order/Check Received":	 
				 $templateId = 1;
				 break;
				 
			case "canceled": 
				 $templateId = 3;
				 break;
				 
			/*case "cheque_received":  
				 $templateId = 2;
				 break;*/
				 
			case "Refund Issued":	
				 $templateId = 4;
				 break;	 
							 
			case "Order Shipped":	
				 $templateId = 6;
				 break;
			
			case "On Hold":	
				 $templateId = 7;
				 break;
		
			case "Shipped With Post":	
				 $templateId = 8;
				 break;
	 	
			case "Incomplete Payment Process":	
				 $templateId = 9;
				 break;
 			
			case "awaiting_check_transfer":	
				 $templateId = 10;
				 break;
				 
		 	case "dispensing":	
				 $templateId = 11;
				 break;
				 
			case "Dispensing":	
				 $templateId = 11;
				 break;
				 
			case "Shipped With Registerd Mail":	
				 $templateId = 16;
				 break;
				 
			case "Shipped With tracking Number":	
				 $templateId = 17;
				 break;
			
			case "eCheck Payment Accepted":	
				 $templateId = 18;
				 break;
				 
			case "Pending eCheck Payment":	
				 $templateId = 19;
				 break;
				 
			case "eCheck Payment Declined":	
				 $templateId = 20;
				 break;
				 
		 	case "Awaiting eCheck Payment":	
				 $templateId = 21;
				 break;
				 
			case "payment_accepted":	
				 $templateId = 23;
				 break;
				 
			case "transaction_declined":	
				 $templateId = 24;
				 break;

			case "transaction_declined_vt":	
				 $templateId = 30;
				 break;
				  
			case "pendingecheck":	
				 $templateId = 26;
				 break;

			case "want_to_pay":	
				 $templateId = 27;
				 break;

			case "voice_message_left":	
				 $templateId = 28;
				 break;

			case "payment_accepted_vt":
				$templateId = 29;
				 break;

		    case "donotcall":
		    	$templateId = 999;
		    	 break;

		    case "complete":
		    	$templateId = 999;
		    	 break;

		    case "closed":
				$templateId = 999;
				 break;
				 
			default:
 			 	 $templateId = 15;
			}
			
            $tempID = $templateId;
			$mailTemplate = Mage::getModel('core/email_template');
			$translate  = Mage::getSingleton('core/translate');
            //$templateId = 1; //template for sending customer data
            $template_collection =  $mailTemplate->load($templateId);                                
            $template_data = $template_collection->getData();
			
				 if(!empty($template_data))
                    {
                        $templateId = $template_data['template_id'];
                        $mailSubject = $template_data['template_subject'];                          
                          
                        //fetch sender data from Adminend > System > Configuration > Store Email Addresses > General Contact
                        $from_email = Mage::getStoreConfig('trans_email/ident_general/email'); //fetch sender email
                        $from_name = Mage::getStoreConfig('trans_email/ident_general/name'); //fetch sender name
                 
                        $sender = array('name'  => $from_name,
                                        'email' => $from_email);                                 
                         $customer['cust_name'] =  $order->getBillingAddress()->getName();
                         //$customer['cust_name'] =  $order->getData('customer_firstname')." ".$order->getData('customer_lastname');
						 $customer['order_id']= $order->getData('increment_id');
						$customer['staus_label'] = $order->getData('status');
						$customer['customer_email'] = $order->getData("customer_email");
						//$customer['customer_email'] = $order->getData("email");
					    
						//add code for get tracking number
						if($tempID == '17' || $tempID == '16')
						{
							$shipment_collection = Mage::getResourceModel('sales/order_shipment_collection');
							$shipment_collection->addAttributeToFilter('order_id', $order->getId());
							$track_no = "";
							$date = "";
							foreach($shipment_collection as $sc) {
					
								$shipment = Mage::getModel('sales/order_shipment');
								$shipment->load($sc->getId());
								//echo "<pre>";
								//print_r($shipment->getAllTracks());exit;
								foreach ($shipment->getAllTracks() as $o_item)
								{
								  if($track_no == "")
								  {
									$track_no = $o_item->getNumber();
									$date = date("M d", strtotime(Mage::helper('core')->formatDate($o_item->getAssignDate().' 12:12:12', 'medium', 'true')));
								  }
								  else
								  {
									$track_no = $track_no.", ".$o_item->getNumber();
									$date = $date." and ".date("M d", strtotime(Mage::helper('core')->formatDate($o_item->getAssignDate().' 12:12:12', 'medium', 'true')));
								  }
								  $year = date("Y", strtotime(Mage::helper('core')->formatDate($o_item->getAssignDate().' 12:12:12', 'medium', 'true')));
							   //echo $_item->getTitle()."--sdf--".$_item->getNumber();
							  
							}
							
						   }
						}
						$customer['track_no'] = $track_no;
						$customer['date'] = $date;
						$customer['year'] = $year;
						//echo "sdf--".date('F-d-Y', time('20/05/2013'));exit;
						//echo "sdf--".$date;exit;
						//end codeing 
						/*echo "<pre>";
						var_dump($vars);
						exit;	*/
						$vars = $customer; //for replacing the variables in email with data 
					    $storeId = 1;	/*This is optional*/					
                        $model = $mailTemplate->setReplyTo($sender['email'])->setTemplateSubject($mailSubject);
                        $email = $vars['customer_email'];
                        $name = $vars['cust_name'];                                        
                        $status = $vars['staus_label'];                                        
                        $model->sendTransactional($templateId, $sender, $email, $name, $vars, $storeId);                     
                        if (!$mailTemplate->getSentSuccess()) {
                                throw new Exception();
                        }
                        $translate->setTranslateInline(true);
                    }
			
			
		}
		//echo "<pre>";
		//echo $order.getCustomerName()
			//print_r($order);
			//print_r($vars);
			//print_r($template_data);
		//exit;
        foreach ($ids as $id){
            $order = Mage::getModel('sales/order')->load($id);
            $orderCode = $order->getIncrementId();
            try {
                Mage::getModel('sales/order_api')->addComment($orderCode, $val, '', false);
                ++$numAffectedOrders;          
            }
            catch (Exception $e) {
                $err = $e->getCustomMessage() ? $e->getCustomMessage() : $e->getMessage();
                $this->_errors[] = $hlp->__(
                    'Can not update order #%s: %s', $orderCode, $err);
            }
            $order = null;
            unset($order); 
        }
        
        if ($numAffectedOrders){
            $success = $hlp->__('Total of %d order(s) have been successfully updated.', 
                $numAffectedOrders);
        }         
        
        return $success; 
    }

    protected function _getValueField($title)
    {
        $field = array('amoaction_value' => array(
            'name'   => 'amoaction_value',
            'type'   => 'select',
            'class'  => 'required-entry',
            'label'  => $title,
            'values' => Mage::getModel('sales/order_config')->getStatuses(),
        )); 
        return $field;       
    }    
}
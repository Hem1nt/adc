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
        $success = parent::execute($ids, $val);

        $numAffectedOrders = 0;
         $serializerData = unserialize(Mage::getStoreConfig('amoaction/shipping_url/serialize'));
        $hlp = Mage::helper('amoaction');
        foreach($ids as $id){
			$order = Mage::getModel('sales/order')->load($id);

			/* start to get template id respective of order id from system config*/
			
            $templateMapping = array();

	        foreach (unserialize(Mage::getStoreConfig("custom_snippet/snippet/email")) as $mapping) {
	            if (array_key_exists('list_template', $mapping)) {
	                $templateMapping[$mapping['list_template']] = $mapping['magento_template'];
	            }
	        }
	        if($templateMapping[$val] != ""){
	        	$templateId = $templateMapping[$val];
	        }
	        else{
	        	$templateId = Mage::getStoreConfig("custom_snippet/snippet/default_template");
	        }

			/* end to get template id respective of order id from system config*/


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
                      
                        $from_email = Mage::getStoreConfig('trans_email/ident_general/email'); //fetch sender email
                        $from_name = Mage::getStoreConfig('trans_email/ident_general/name'); //fetch sender name

                        $sender = array('name'  => $from_name,'email' => $from_email);

                        $increment_id = $order->getData('increment_id');

                        if(!$order->getData('customer_order_increment_id')) {
                           $order->setData('customer_order_increment_id',$increment_id)->save();
                        }

                        $customer['order']= $order;
                        $subtotal =  $order->getSubtotal();
                        $grand_total = $order->getGrandTotal();

                        $order_total = $grand_total;
                        $payment_method_code = $order->getPayment()->getMethodInstance()->getCode();
                        
                        if($payment_method_code=='echeckpayment'){
                            if($subtotal>100){
                                $order_total = $grand_total;
                            }
                        }else{
                            if($payment_method_code!='checkmo' && $payment_method_code!='wiretransfer'){
                                $order_total = $grand_total;
                            }
                        }                     
                
                        $formattedPrice = Mage::helper('core')->currency($order_total, true, false);//exit;
                        $customer['order_amount']= $formattedPrice;

                        $customer['cust_name'] =  $order->getBillingAddress()->getName();
                
						$customer['order_id']= $order->getData('increment_id');
						$customer['order_entity_id']= $order->getId();
						$customer['amount']= sprintf("%0.2f", $order->getGrandTotal());
												
                        $customer['staus_label'] = $val;
						$customer['customer_email'] = $order->getData("customer_email");
						$customer['email'] = $order->getData("email");
						$customer['street'] = $order->getShippingAddress()->getData('street');
						$customer['region'] = $order->getShippingAddress()->getData('region');
						$customer['city'] = $order->getShippingAddress()->getData('city');
						$customer['postcode'] = $order->getShippingAddress()->getData('postcode');
						$country_code = $order->getShippingAddress()->getData('country_id');
						$billing_country_code = $order->getBillingAddress()->getData('country_id');
						$customer['country'] = Mage::app()->getLocale()->getCountryTranslation($country_code);
						$customer['bi_country'] = Mage::app()->getLocale()->getCountryTranslation($billing_country_code);
                        
                        $paymentLinkUrl = Mage::getStoreConfig('custom_snippet/snippet/paymentlink');
                        $linkPayment = Mage::getStoreConfig('payment/offlinepayment/paymentlink');
                        $echeckPayment = Mage::getStoreConfig('payment/newofflinepayment/newpaymentlink');
                        $frontendHelper = Mage::helper('frontend');

                        $encodedurl = $frontendHelper->encrypt_decrypt('encrypt',$order->getData('entity_id'));
                        $customer['link'] = $paymentLinkUrl.'?order_id='.$encodedurl.'&utm_source=back-payment&utm_medium=email&utm_campaign=pay-link';
                        $customer['online_link'] = $linkPayment.'?order_id='.$encodedurl.'&utm_source=back-payment&utm_medium=email&utm_campaign=online-pay-link';
						$customer['echeck_link'] = $echeckPayment.'?order_id='.$encodedurl.'&utm_source=echeck-payment&utm_medium=email&utm_campaign=echeck-pay-link';
					    
                        $overridesHelper = Mage::helper('overrides');
                        $customerLoggedIn = Mage::getSingleton('customer/session')->isLoggedIn();
                        $billingCountry = $order->getBillingAddress()->getCountry();
                        if($customerLoggedIn){
                            $customerData = Mage::getSingleton('customer/session')->getCustomer();
                            $customer_id = $customerData->getId(); // set this to the ID of the customer.
                            $customer_data = Mage::getModel('customer/customer')->load($customer_id);
                            $newCustomerCreatedDate = Mage::getStoreConfig('payment/pay/new_customer_created_date');
                            $limitDateNewCustomerCreatedDate = date('d-m-Y', strtotime($newCustomerCreatedDate));
                            $configValue = Mage::getStoreConfig('payment/pay/registration_date');
                            $customerCreatedDate = Mage::getStoreConfig('payment/pay/customer_created_date');

                            $limitDate = date('d-m-Y', strtotime($configValue));

                            $customerDate = date('d-m-Y', strtotime($customer_data->getData('created_at')));

                            if(strtotime($limitDate) > strtotime($customerDate))
                            {
                                $oldCustomerPayments = $this->oldCustomerPayments();
                                $payLinkCounter = 0;
                            }
                            else{
                                $payOrderCount = $overridesHelper->payOrderCount($customer['email']);
                                if($payOrderCount>=1){
                                    $payLinkCounter = 1;
                                }else{
                                    $payLinkCounter = 0;        
                                }
                            }
                        }else{
                            // $payLinkCounter = 0;
                                // if($billingCountry != 'US'){
                                    $payOrderCount = $overridesHelper->payOrderCount($customer['email']);
                                    if($payOrderCount>=1){
                                        $payLinkCounter = 1;
                                    }else{
                                        $payLinkCounter = 0;  
                                    }
                                // }
                        }
                        $customer['pay_link_counter'] = $payLinkCounter;
                        $customer['pay_link_counter'] = 1;
                        $customer['supply_issue_message'] = $order->getSupplyIssueMessage();
                        $customer['dispatcher_message'] = $order->getDispatcherMessage();

                        $tracking_email_guest = Mage::getStoreConfig('amoaction/ship/tracking_template_guest');
                        $tracking_email_registred = Mage::getStoreConfig('amoaction/ship/tracking_template_registered');

						if($tempID == $tracking_email_guest || $tempID == $tracking_email_registred)
						{
                            $track_no = $date = $year = "";
                            $i = $j = 0;

                            $trackNumber = array();
                            foreach ($order->getTracksCollection() as $track){
                                $trackNumber[$i]['track_number'] = $track->getNumber();
                                $trackNumber[$i]['assign_date'] = date("M d", strtotime(Mage::helper('core')->formatDate($track->getAssignDate().' 12:12:12', 'medium', 'true')));
                                $year = date("Y", strtotime(Mage::helper('core')->formatDate($track->getAssignDate().' 12:12:12', 'medium', 'true')));
                                $i++;
                            }

                            // $this->array_sort_by_column($trackNumber, 'assign_date');
                            
                            foreach ($trackNumber as $track_key => $track_value) {
                                $trackNumberCollection[$j] = $track_value['track_number'];
                                $trackDateCollection[$j] = $track_value['assign_date'];
                                $j++;
                            }

                            if(count(array_unique($trackNumberCollection))==1){
                                $track_no = $trackNumberCollection[0];
                            }else{
                                $trackNumberCollection = array_unique($trackNumberCollection);
                                $track_no = implode(' , ',$trackNumberCollection);
                            }

                            if(count(array_unique($trackDateCollection))==1){
                                $date = $trackDateCollection[0];
                            }else{
                                $trackDateCollection = array_unique($trackDateCollection);
                                $date = implode(' and ',$trackDateCollection);
                            }

                        }

                        $track = Mage::getModel('sales/order_shipment_track')->getCollection();
                        $track->addFieldToFilter('order_id', $order->getId())->setOrder('assign_date', 'asc');
                        $trackData = $track->getData();

                        foreach ($trackData as $key) {
                            foreach ($serializerData as $code) {
                                if($code['shipping_carrier'] == $key['carrier_code'] )
                                {
                                    $carrierCode[] =  "<a href=".$code['url'].">".$code['url']."</a>";
                                }
                            }
                        }
                        if(count(array_unique($carrierCode))==1){
                            $customer['carrier'] = $carrierCode[0];
                        }else{
                            $carrierCode = array_unique($carrierCode);
                            $customer['carrier'] = implode(' OR ',$carrierCode);
                        }
                        if(count(array_unique($carrierCode))==0){
                            $fixurl = Mage::getStoreConfig("amoaction/shipping_url/default_url");
                            $customer['carrier'] = "<a href=".$fixurl.">".$fixurl."</a>";
                        }
                        $orderTracking = Mage::getModel('orderlog/ordertracking')->getCollection()->addFieldToFilter('order_id',$increment_id);
                        
                        $shippingPart = $orderTracking->getFirstItem()->getData('shipping_part');
                        $customer['shipping_Part'] = $shippingPart;
						$customer['track_no'] = $track_no;
						$customer['date'] = $date;
                        $customer['year'] = $year;
                        
                        $customer['item'] = Mage::helper('amoaction')->getItemsHtml($order);
                        $customer['shippingitem'] = Mage::helper('amoaction')->getShippingHtml($order);
                        $customer['recommendedproducts'] = Mage::helper('amoaction')->getRecommendedProducts($order);
                        //$customer['shippedProducts'] = Mage::helper('amoaction/shippeditem')->getShippedItemsHtml($order);
						$customer['completeShipment'] = Mage::helper('amoaction/shippeditem')->getCompleteShipment($order);
                        
                        // echo $customer['item'];exit;
						$vars = $customer; //for replacing  the variables in email with data
					    $storeId = 1;	/*This is optional*/
                        $model = $mailTemplate->setReplyTo($sender['email'])->setTemplateSubject($mailSubject);
                        $email = $vars['customer_email'];
                        $name = $vars['cust_name'];
                        $status = $vars['staus_label'];
                        // Mage::log($vars,null,'emailer.log');
                        $model->sendTransactional($templateId, $sender, $email, $name, $vars, $storeId);
                        if (!$mailTemplate->getSentSuccess()) {
                                throw new Exception();
                        }
                        $translate->setTranslateInline(true);
                    }
		}
        
        foreach ($ids as $id){
            $order = Mage::getModel('sales/order')->load($id);
            $orderCode = $order->getIncrementId();
            $sendToCrm[] = $id;
            try {
                Mage::getModel('sales/order_api')->addComment($orderCode, $val, $append, false);
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
            $success = $hlp->__('Total of %d order(s) have been successfully updated.', $numAffectedOrders);
            // custom event dispatch for crm
            // Mage::dispatchEvent( 'insert_after_order_status_update',array('data'=>$sendToCrm));
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
            'values' => $this->_getStatuses(),
        ));
        return $field;
    }

    protected function _getStatuses(){
    	$status = Mage::getModel('sales/order_config')->getStatuses();
    	foreach ($status as $key => $value) {
    		if($value!='Complete'){
    			$status_Arr[$key]=$value;
    		}
    	}
    	return $status_Arr;
    }

    public function array_sort_by_column(&$array, $column, $direction = SORT_ASC) {
        $reference_array = array();

        foreach($array as $key => $row) {
            $reference_array[$key] = $row[$column];
        }

        array_multisort($reference_array, $direction, $array);
    }


    public function oldCustomerPayments(){
       $oldcustomerpayment = Mage::getStoreConfig('payment/pay/oldcustomerpayment');
       $methods = array_filter(explode(",",$oldcustomerpayment));
       return $methods;
    }

    public function newCustomerPayments(){
       $newcustomerpayment = Mage::getStoreConfig('payment/pay/newcustomerpayment');
       $methods = array_filter(explode(",",$newcustomerpayment));
       return $methods;
    }

    public function guestCustomerPayments(){
       $guestcustomerpayment = Mage::getStoreConfig('payment/pay/guestcustomerpayment');
       $methods = array_filter(explode(",",$guestcustomerpayment));
       return $methods;
    }
}
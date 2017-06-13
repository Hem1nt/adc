<?php
include_once 'app/code/core/Mage/Sales/Model/Order.php';
class Iksula_Overrides_Model_Sales_Order extends Mage_Sales_Model_Order
{
    protected function _setState($state, $status = false, $comment = '',
        $isCustomerNotified = null, $shouldProtectState = false)
    {
        if ($shouldProtectState) {
            if ($this->isStateProtected($state)) {
                Mage::throwException(
                    Mage::helper('sales')->__('The Order State "%s" must not be set manually.', $state)
                );
            }
        }
        $this->setData('state', $state);

        // add status history
        if ($status) {
            if ($status === true) {
                $status = $this->getConfig()->getStateDefaultStatus($state);
            }
            if($status != "dispensing"){
                $this->setStatus($status);
                $history = $this->addStatusHistoryComment($comment, false); // no sense to set $status again
                $history->setIsCustomerNotified($isCustomerNotified); // for backwards compatibility
            }
        }
        return $this;
    }

    public function queueNewOrderEmail($forceMode = false){
        
        $storeId = $this->getStore()->getId();

        if (!Mage::helper('sales')->canSendNewOrderEmail($storeId)) {
            return $this;
        }

        // Get the destination email addresses to send copies to
        $copyTo = $this->_getEmails(self::XML_PATH_EMAIL_COPY_TO);
        $copyMethod = Mage::getStoreConfig(self::XML_PATH_EMAIL_COPY_METHOD, $storeId);

        // Start store emulation process
        /** @var $appEmulation Mage_Core_Model_App_Emulation */
        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);

        try {
            // Retrieve specified view block from appropriate design package (depends on emulated store)
            $paymentBlock = Mage::helper('payment')->getInfoBlock($this->getPayment())
                ->setIsSecureMode(true);
            $paymentBlock->getMethod()->setStore($storeId);
            $paymentBlockHtml = $paymentBlock->toHtml();
        } catch (Exception $exception) {
            // Stop store emulation process
            $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
            throw $exception;
        }

        // Stop store emulation process
        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        // Retrieve corresponding email template id and customer name
        if ($this->getCustomerIsGuest()) {
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_GUEST_TEMPLATE, $storeId);
            $customerName = $this->getBillingAddress()->getName();
        } else {
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE, $storeId);
            $customerName = $this->getCustomerName();
        }

        /** @var $mailer Mage_Core_Model_Email_Template_Mailer */
        $mailer = Mage::getModel('core/email_template_mailer');
        /** @var $emailInfo Mage_Core_Model_Email_Info */
        $emailInfo = Mage::getModel('core/email_info');
        $emailInfo->addTo($this->getCustomerEmail(), $customerName);
        if ($copyTo && $copyMethod == 'bcc') {
            // Add bcc to customer email
            foreach ($copyTo as $email) {
                $emailInfo->addBcc($email);
            }
        }
        $mailer->addEmailInfo($emailInfo);

        // Email copies are sent as separated emails if their copy method is 'copy'
        if ($copyTo && $copyMethod == 'copy') {
            foreach ($copyTo as $email) {
                $emailInfo = Mage::getModel('core/email_info');
                $emailInfo->addTo($email);
                $mailer->addEmailInfo($emailInfo);
            }
        }

        /* Start Send Payment Link order Emailer */
        $paymentLinkUrl = Mage::getStoreConfig('custom_snippet/snippet/paymentlink');
        $frontendHelper = Mage::helper('frontend');
        $encodedurl = $frontendHelper->encrypt_decrypt('encrypt',$this->getId());
        $link = $paymentLinkUrl.'?order_id='.$encodedurl.'&utm_source=order-email&utm_medium=email&utm_campaign=pay-link';
        /* End Send Payment Link order Emailer */
        $orderIncrementId = $this->getData('increment_id');
        $created_at = $this->getData('created_at');
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
        // $custom_order_id = $this->getCustomOrderId($created_at);
        $ext = Mage::getStoreConfig('custom_snippet/custom_orderid/ext_name');
        $order_format = Mage::getModel('core/date')->date('dm-yhis',strtotime($created_at));
        $custom_order_id = $ext.''.$order_format.''.$order->getId();
        
        $order->setData('customer_order_increment_id',$custom_order_id)->save();
        // $customer_order_increment_id = $this->setCustomDetails($order,$custom_order_id);
        $custom_order_id = $order->getData('customer_order_increment_id');



        $customerLoggedIn = Mage::getSingleton('customer/session')->isLoggedIn();
        $billingCountry = $this->getBillingAddress()->getCountry();
        $method          = $this->getPayment()->getMethodInstance();
        $overridesHelper = Mage::helper('overrides');

        $customerEmail = $this->getCustomerEmail();

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
                $payOrderCount = $overridesHelper->payOrderCount($customerEmail);
                if($payOrderCount>=1){
                    $payLinkCounter = 1;
                }else{
                    $payLinkCounter = 0;        
                }
            }
        }else{
            // $payLinkCounter = 0;
                // if($billingCountry != 'US'){
                    $payOrderCount = $overridesHelper->payOrderCount($customerEmail);
                    if($payOrderCount>=1){
                        $payLinkCounter = 1;
                    }else{
                        $payLinkCounter = 0;  
                    }
                // }
        }
        // $payLinkCounter = 0;
        $item = Mage::helper('amoaction')->getItemsHtml($order);
        $shippingitem = Mage::helper('amoaction')->getShippingHtml($order);
        $recommendedproducts = Mage::helper('amoaction')->getRecommendedProducts($order);
       



        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams(array(
            'order'        => $this,
            'billing'      => $this->getBillingAddress(),
            'payment_html' => $paymentBlockHtml,
            'payment_link' => $link,
            'custom_order_id' => $custom_order_id,
            'pay_link_counter' => $payLinkCounter,
            'item' => $item,
            'shippingitem' =>$shippingitem,
            'recommendedproducts' => $recommendedproducts
        ));

        /** @var $emailQueue Mage_Core_Model_Email_Queue */
        $emailQueue = Mage::getModel('core/email_queue');
        $emailQueue->setEntityId($this->getId())
            ->setEntityType(self::ENTITY)
            ->setEventType(self::EMAIL_EVENT_NAME_NEW_ORDER)
            ->setIsForceCheck(!$forceMode);

        $mailer->setQueue($emailQueue)->send();

        $this->setEmailSent(true);
        $this->_getResource()->saveAttribute($this, 'email_sent');

        return $this;
    }
     public function sendNewOrderEmail()
    {
        $storeId = $this->getStore()->getId();

        if (!Mage::helper('sales')->canSendNewOrderEmail($storeId)) {
            return $this;
        }
        // Get the destination email addresses to send copies to
        $copyTo = $this->_getEmails(self::XML_PATH_EMAIL_COPY_TO);
        $copyMethod = Mage::getStoreConfig(self::XML_PATH_EMAIL_COPY_METHOD, $storeId);

        // Start store emulation process
        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);

        try {
            // Retrieve specified view block from appropriate design package (depends on emulated store)
            $paymentBlock = Mage::helper('payment')->getInfoBlock($this->getPayment())
                ->setIsSecureMode(true);
            $paymentBlock->getMethod()->setStore($storeId);
            $paymentBlockHtml = $paymentBlock->toHtml();
        } catch (Exception $exception) {
            // Stop store emulation process
            $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
            throw $exception;
        }

        // Stop store emulation process
        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        // Retrieve corresponding email template id and customer name
        if ($this->getCustomerIsGuest()) {
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_GUEST_TEMPLATE, $storeId);
            $customerName = $this->getBillingAddress()->getName();
        } else {
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE, $storeId);
            $customerName = $this->getCustomerName();
        }

        $mailer = Mage::getModel('core/email_template_mailer');
        $emailInfo = Mage::getModel('core/email_info');
        $emailInfo->addTo($this->getCustomerEmail(), $customerName);
        if ($copyTo && $copyMethod == 'bcc') {
            // Add bcc to customer email
            foreach ($copyTo as $email) {
                $emailInfo->addBcc($email);
            }
        }
        $mailer->addEmailInfo($emailInfo);

        // Email copies are sent as separated emails if their copy method is 'copy'
        if ($copyTo && $copyMethod == 'copy') {
            foreach ($copyTo as $email) {
                $emailInfo = Mage::getModel('core/email_info');
                $emailInfo->addTo($email);
                $mailer->addEmailInfo($emailInfo);
            }
        }

        /* Start Send Payment Link order Emailer */
        $paymentLinkUrl = Mage::getStoreConfig('custom_snippet/snippet/paymentlink');
        $frontendHelper = Mage::helper('frontend');
        $encodedurl = $frontendHelper->encrypt_decrypt('encrypt',$this->getId());
        $link = $paymentLinkUrl.'?order_id='.$encodedurl.'&utm_source=order-email&utm_medium=email&utm_campaign=pay-link';
        /* End Send Payment Link order Emailer */

        // logic to get custom order id

        $orderIncrementId = $this->getData('increment_id');
        $created_at = $this->getData('created_at');
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
        // $custom_order_id = $this->getCustomOrderId($created_at);
        $ext = Mage::getStoreConfig('custom_snippet/custom_orderid/ext_name');
        $order_format = Mage::getModel('core/date')->date('dm-yhis',strtotime($created_at));
        $custom_order_id = $ext.''.$order_format.''.$order->getId();
        
        $order->setData('customer_order_increment_id',$custom_order_id)->save();
        // $customer_order_increment_id = $this->setCustomDetails($order,$custom_order_id);
        $custom_order_id = $order->getData('customer_order_increment_id');

        // Mage::log($custom_order_id, null, 'mylogfile.log');

        // if($customer_order_increment_id) {
        //     $order_id = $customer_order_increment_id;
        // } else {
        //     $order_id = $orderIncrementId;
        // }

        // Set all required params and send emails

        $customerLoggedIn = Mage::getSingleton('customer/session')->isLoggedIn();
        $billingCountry = $this->getBillingAddress()->getCountry();
        $method          = $this->getPayment()->getMethodInstance();
        $overridesHelper = Mage::helper('overrides');

        $customerEmail = $this->getCustomerEmail();

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
                $payOrderCount = $overridesHelper->payOrderCount($customerEmail);
                if($payOrderCount>=1){
                    $payLinkCounter = 1;
                }else{
                    $payLinkCounter = 0;        
                }
            }
        }else{
            // $payLinkCounter = 0;
                // if($billingCountry != 'US'){
                    $payOrderCount = $overridesHelper->payOrderCount($customerEmail);
                    if($payOrderCount>=1){
                        $payLinkCounter = 1;
                    }else{
                        $payLinkCounter = 0;  
                    }
                // }
        }
        // $payLinkCounter = 0;
        $item = Mage::helper('amoaction')->getItemsHtml($order);
        $shippingitem = Mage::helper('amoaction')->getShippingHtml($order);
        $recommendedproducts = Mage::helper('amoaction')->getRecommendedProducts($order);
        $mailer->setSender(Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams(array(
                'order'        => $this,
                'billing'      => $this->getBillingAddress(),
                'payment_html' => $paymentBlockHtml,
                'payment_link' => $link,
                'custom_order_id' => $custom_order_id,
                'pay_link_counter' => $payLinkCounter,
                'item' => $item,
                'shippingitem' =>$shippingitem,
                'recommendedproducts' => $recommendedproducts
            )
        );
        $mailer->send();

        $order->setData('customer_order_increment_id',$custom_order_id)->save();
        // $customer_order_increment_id = $this->setCustomDetails($order,$custom_order_id);
        $custom_order_id = $order->getData('customer_order_increment_id');
        // Mage::log($custom_order_id, null, 'mylogfile.log');

        $this->setEmailSent(true);
        $this->_getResource()->saveAttribute($this, 'email_sent');

        return $this;
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

   public function lastOrderDate($email_address){
        
        $orders = Mage::getResourceModel('sales/order_collection')
        ->addFieldToSelect('*')
        ->addFieldToFilter('customer_email',$email_address)
        ->addAttributeToSort('created_at', 'DESC')
        ->setPageSize(1);

        $customerCreatedDate = Mage::getStoreConfig('payment/pay/customer_created_date');
        if($orders->getSize()==1){
            $createdAt = $orders->getFirstItem()->getCreatedAt();
            if(strtotime($customerCreatedDate) > strtotime($createdAt)){
                return 1;
            }else{
                return 0;
            }
        }else{
            return 0;
        }

       
    }


    /** Generate Custom Order Increment Id **/
    function getCustomOrderId($created_at) {
        // $created_at = $order->getData('created_at');
        $ext = Mage::getStoreConfig('custom_snippet/custom_orderid/ext_name');
        $custom_order_id = $ext.''.Mage::getModel('core/date')->date('dm-yhis',strtotime($created_at));
        return $custom_order_id;
    }

    function setCustomDetails($order,$custom_order_id) {
        $order->setData('customer_order_increment_id',$custom_order_id)
              ->save();

        return $order->getData('customer_order_increment_id');
    }

}
?>

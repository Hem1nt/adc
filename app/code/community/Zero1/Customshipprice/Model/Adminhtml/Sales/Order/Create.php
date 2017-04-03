<?php
class Zero1_Customshipprice_Model_Adminhtml_Sales_Order_Create extends Mage_Adminhtml_Model_Sales_Order_Create
{
    /**
     * Parse data retrieved from request
     *
     * @param   array $data
     * @return  Mage_Adminhtml_Model_Sales_Order_Create
     */
    public function importPostData($data)
    {
        if (is_array($data)) {
            $this->addData($data);
        } else {
            return $this;
        }

        if (isset($data['account'])) {
            $this->setAccountData($data['account']);
        }

        if (isset($data['comment'])) {
            $this->getQuote()->addData($data['comment']);
            if (empty($data['comment']['customer_note_notify'])) {
                $this->getQuote()->setCustomerNoteNotify(false);
            } else {
                $this->getQuote()->setCustomerNoteNotify(true);
            }
        }

        if (isset($data['billing_address'])) {
            $this->setBillingAddress($data['billing_address']);
        }

        if (isset($data['shipping_address'])) {
            $this->setShippingAddress($data['shipping_address']);
        }

        if (isset($data['shipping_method'])) {
            $this->setShippingMethod($data['shipping_method']);
        }

        if (isset($data['payment_method'])) {
            $this->setPaymentMethod($data['payment_method']);
        }
        
        $reinit_rates = false;
        
        if (isset($data['shipping_amount'])) {            	
            $shippingPrice = $this->_parseShippingPrice($data['shipping_amount']);
            //$this->getQuote()->getShippingAddress()->setShippingAmount($shippingPrice);
        	Mage::getSingleton('core/session')->setCustomshippriceAmount($shippingPrice);
        	$reinit_rates = true;
        }

        if (isset($data['base_shipping_amount'])) {
            $baseShippingPrice = $this->_parseShippingPrice($data['base_shipping_amount']);
            //$this->getQuote()->getShippingAddress()->setBaseShippingAmount($baseShippingPrice, true);
        	Mage::getSingleton('core/session')->setCustomshippriceBaseAmount($baseShippingPrice);
        	$reinit_rates = true;
        }

        if (isset($data['shipping_description'])) {
            //$this->getQuote()->getShippingAddress()->setShippingDescription($data['shipping_description']);
        	Mage::getSingleton('core/session')->setCustomshippriceDescription($data['shipping_description']);
        	$reinit_rates = true;
        }

        if (isset($data['coupon']['code'])) {
            $this->applyCoupon($data['coupon']['code']);
            $reinit_rates = true;
        }
        
        if($reinit_rates)
        {
        	//$this->collectShippingRates();
        	//$this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
        	//$this->collectRates();
        	//$this->getQuote()->collectTotals();
        }

         if (isset($data['rewardpoints']['qty'])) {
            if (is_numeric($data['rewardpoints']['qty'])){
                $this->applyPoints($data['rewardpoints']['qty']);
            }
        }
        
        return $this;
    }

    protected function _parseShippingPrice($price)
    {
        $price = Mage::app()->getLocale()->getNumber($price);
        $price = $price>0 ? $price : 0;
        return $price;
    }

      public function applyPoints($points)
    {
        //check customer max points
        $user_points = $this->customerPoints();
        $points = ($user_points < $points) ? $user_points : $points;
        if ($points > 0){
            Mage::helper('rewardpoints/event')->setCreditPoints($points);
            $this->getQuote()
                    ->setRewardpointsQuantity($points);
                    //->save();
        } else {
            Mage::getSingleton('rewardpoints/session')->setProductChecked(0);
            Mage::helper('rewardpoints/event')->setCreditPoints(0);
            $this->getQuote()
                    ->setRewardpointsQuantity(NULL)
                    ->setRewardpointsDescription(NULL)
                    ->setBaseRewardpoints(NULL)
                    ->setRewardpoints(NULL);
        }
        
        $this->setRecollect(true);
        
        
        //modify in order to process points
        /*$code = trim((string)$points);
        $this->getQuote()->setCouponCode($points);
        $this->setRecollect(true);*/
        return $this;
    }
    
    protected function customerPoints()
    {
        $quote = $this->getQuote();
        $store_id = $quote->getStoreId();
        if ($quote->getCustomerId()){
            $customerId = $quote->getCustomerId();
        } else {
            return 0;
        }
        if (Mage::getStoreConfig('rewardpoints/default/flatstats', $store_id)){
            $reward_model = Mage::getModel('rewardpoints/flatstats');
            $customer_points = $reward_model->collectPointsCurrent($customerId, $store_id);
        } else {
            $reward_model = Mage::getModel('rewardpoints/stats');
            $customer_points = $reward_model->getPointsCurrent($customerId, $store_id);
        }
        return $customer_points;
    }

}

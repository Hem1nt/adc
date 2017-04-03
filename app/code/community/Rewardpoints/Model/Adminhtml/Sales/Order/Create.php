<?php
class Rewardpoints_Model_Adminhtml_Sales_Order_Create extends Mage_Adminhtml_Model_Sales_Order_Create
{
    public function importPostData($data)
    {
        echo 'timtim';exit;
        if (isset($data['rewardpoints']['qty'])) {
            if (is_numeric($data['rewardpoints']['qty'])){
                $this->applyPoints($data['rewardpoints']['qty']);
            }
        }
        parent::importPostData($data);
        return $this;
    }
    
    public function applyPoints($points)
    {
         echo 'timtim';exit;
         
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

<?php   
class Iksula_Promotions_Block_Index extends Mage_Core_Block_Template{   

	public function getActivePromotions(){
		$rules = Mage::getModel('salesrule/rule')->getCollection()
        ->addFieldToFilter('is_active', 1);
        return $rules->getData();
	}

}
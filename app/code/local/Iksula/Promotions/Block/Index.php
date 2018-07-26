<?php   
class Iksula_Promotions_Block_Index extends Mage_Core_Block_Template{   

	public function getActivePromotions(){
		$currentDay = Mage::getModel('core/date')->date('Y-m-d');
		$rules = Mage::getModel('salesrule/rule')->getCollection()
		->addFieldToFilter('is_active', 1);

		$merged_ids = array();
		foreach ($rules as $key => $rule){

			if(isset($rule['to_date']) && isset($rule['from_date'])){
				$collection1 = Mage::getModel('salesrule/rule')->getCollection()
				->addFieldToFilter('is_active', 1)->addFieldToFilter('to_date', array( 'gt'=>$currentDay ) )->addFieldToFilter('from_date', array( 'lteq'=>$currentDay ) );

				$merged_ids = array_merge($merged_ids,$collection1->getAllIds());
			}
			elseif(isset($rule['to_date'])){
				$collection2 = Mage::getModel('salesrule/rule')->getCollection()
				->addFieldToFilter('is_active', 1)->addFieldToFilter('to_date', array( 'gt'=>$currentDay ))->addFieldToFilter('from_date', array('null' => true));
				$merged_ids = array_merge($merged_ids,$collection2->getAllIds());
				
			}
			elseif(isset($rule['from_date'])){
				$collection3 = Mage::getModel('salesrule/rule')->getCollection()
				->addFieldToFilter('is_active', 1)->addFieldToFilter('to_date', array('null' => true))->addFieldToFilter('from_date', array( 'lteq'=>$currentDay ));
				$merged_ids = array_merge($merged_ids,$collection3->getAllIds());

			}
			else{
				$collection4 = Mage::getModel('salesrule/rule')->getCollection()
				->addFieldToFilter('is_active', 1)->addFieldToFilter('to_date', array('null' => true))->addFieldToFilter('from_date', array('null' => true));
				$merged_ids = array_merge($merged_ids,$collection4->getAllIds());
			}

		}
		
		$merged_collection = Mage::getModel('salesrule/rule')->getCollection()
		->addFieldToFilter('rule_id', array('in' => $merged_ids))->addOrder('sortorder','ASC');
		return $merged_collection->getData();
	}

}
<?php

class TS_Reports_Model_System_Config_Source_Select_Pricetype {

      public function toOptionArray(){
		$model = Mage::getModel('ts_reports/types');
		$priceTypesArr = $model::getTypeNames();
		$priceTypes = array();
		foreach($priceTypesArr as $key => $priceType){
			$priceTypes[] = array(
				'label' => Mage::helper('ts_reports')->__($priceType),
				'value' => $key
			);
		}
		return $priceTypes;
	}
	
}
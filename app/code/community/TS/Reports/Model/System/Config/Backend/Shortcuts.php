<?php

class TS_Reports_Model_System_Config_Backend_Shortcuts extends Mage_Core_Model_Config_Data {
	
	protected function _afterLoad(){
		if($this->getValue()) $this->setValue( explode(',',str_replace("'","",$this->getValue())) ); // remove apostrophes
        parent::_afterLoad();
    }
 
    protected function _beforeSave(){
		if($this->getValue()) $this->setValue("'". str_replace(",", "','", implode(',',$this->getValue())) ."'"); // add apostrophes
        parent::_beforeSave();
    }

}

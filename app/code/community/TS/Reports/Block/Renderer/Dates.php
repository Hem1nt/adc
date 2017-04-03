<?php

class TS_Reports_Block_Renderer_Dates extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Date {

    public function render(Varien_Object $row){
        if($data = $row->getData($this->getColumn()->getIndex())) {
			$dates = explode('/',$data);
            $format = $this->_getFormat();
			$locale = Mage::app()->getLocale();
			$core 	= Mage::app()->getLocale();
			foreach($dates as &$d){
				try {
					if($this->getColumn()->getGmtoffset()) $d = $locale->date($d, Varien_Date::DATETIME_INTERNAL_FORMAT)->toString($format);
					else $d = $core->date($d, Zend_Date::ISO_8601, null, false)->toString($format);
				} catch(Exception $e){
					if($this->getColumn()->getTimezone()) $d = $locale->date($d, Varien_Date::DATETIME_INTERNAL_FORMAT)->toString($format);
					else $d = $core->date($d, null, null, false)->toString($format);
				}
			}
            return implode('<br />',$dates);
        }
        return $this->getColumn()->getDefault();
    }
}
<?php

class TS_Reports_Block_Renderer_Currencies extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Currency {
    
    public function render(Varien_Object $row){
        if($data = $row->getData($this->getColumn()->getIndex())) {
            $currency_code = $this->_getCurrencyCode($row);
            if(!$currency_code) return $data;
			
			$rate = $this->_getRate($row);
			$locale = Mage::app()->getLocale();
			$prices = explode('/',$data);
			foreach($prices as &$p){
				$p = floatval($p) * $rate;
				$sign = (bool)(int)$this->getColumn()->getShowNumberSign() && ($p > 0) ? '+' : '';
				$p = $sign . $locale->currency($currency_code)->toCurrency( sprintf("%f", $p) );
			}
            return implode('<br />',$prices);
        }
        return $this->getColumn()->getDefault();
    }
	
}

<?php

class TS_Reports_Block_Renderer_Timezone extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Date {

    public function render(Varien_Object $row){
        if($data = $row->getData($this->getColumn()->getIndex())) {
			$sign = '';
			if($data > 0) $sign = '+';
			else if($data < 0) $sign = '-';
            return $sign . gmdate("H:i", abs($data));
        }
        return $this->getColumn()->getDefault();
    }
}
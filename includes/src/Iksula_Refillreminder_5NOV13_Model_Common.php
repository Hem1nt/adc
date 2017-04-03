<?php

class Iksula_Refillreminder_Model_Common extends Mage_Core_Model_Abstract
{
	protected function _construct()
    {
        $this->_init('refillreminder/common');
    }
    public function AjaxReminderAction() {
    	//echo "<pre>"; print_r(get_class_methods($this));
    	$block = mage::loadLayout()->getLayout()->createBlock("core/template")->setTemplate("refillreminder/view.phtml");
		echo $block->toHtml();
    }
}
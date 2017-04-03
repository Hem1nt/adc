<?php

class TS_Reports_Block_Report_Filter_Form_Products extends TS_Reports_Block_Report_Filter_Form_Sales { // Mage_Adminhtml_Block_Report_Filter_Form

    protected function _prepareForm(){
		parent::_prepareForm();
		
		$form = $this->getForm();
		$form->setData('action', $this->getCurrentUrl());		
		
		$fieldset = $form->getElement('base_fieldset');		
		
		$fieldset->addField('show_prices', 'select', array(
			'name'       => 'show_prices',
			'options'    => array(
				'1' => Mage::helper('reports')->__('Yes'),
				'0' => Mage::helper('reports')->__('No')
			),
			'label'      => Mage::helper('reports')->__('Show Prices'),
		));
		
        $form->setUseContainer(true);
        $this->setForm($form);

        return $this;
    }

}

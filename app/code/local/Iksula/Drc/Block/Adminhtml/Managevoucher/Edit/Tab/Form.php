<?php
class Iksula_Drc_Block_Adminhtml_Managevoucher_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("drc_form", array("legend"=>Mage::helper("drc")->__("Item information")));

				

				if (Mage::getSingleton("adminhtml/session")->getManagevoucherData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getManagevoucherData());
					Mage::getSingleton("adminhtml/session")->setManagevoucherData(null);
				} 
				elseif(Mage::registry("managevoucher_data")) {
				    $form->setValues(Mage::registry("managevoucher_data")->getData());
				}
				return parent::_prepareForm();
		}
}

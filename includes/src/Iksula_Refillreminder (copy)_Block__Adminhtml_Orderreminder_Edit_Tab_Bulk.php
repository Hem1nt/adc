<?php
class Iksula_Refillreminder_Block_Adminhtml_Orderreminder_Edit_Tab_Bulk extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("orderreminder_form", array("legend"=>Mage::helper("refillreminder")->__("Item information")));

						 $fieldset->addField('filename', 'file', array(
						'label'     => Mage::helper('refillreminder')->__('File'),
						'required'  => false,
						'name'      => 'filename',
						));
						
						

				if (Mage::getSingleton("adminhtml/session")->getOrderreminderData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getOrderreminderData());
					Mage::getSingleton("adminhtml/session")->setOrderreminderData(null);
				} 
				
				return parent::_prepareForm();
		}
}

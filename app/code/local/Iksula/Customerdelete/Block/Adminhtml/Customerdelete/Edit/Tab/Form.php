<?php
class Iksula_Customerdelete_Block_Adminhtml_Customerdelete_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("customerdelete_form", array("legend"=>Mage::helper("customerdelete")->__("Item information")));

				
						$fieldset->addField("id", "text", array(
						"label" => Mage::helper("customerdelete")->__("ID"),
						"name" => "id",
						));
					
						$fieldset->addField("entity_id", "text", array(
						"label" => Mage::helper("customerdelete")->__("Entity Id"),
						"name" => "entity_id",
						));
					
						$fieldset->addField("email", "text", array(
						"label" => Mage::helper("customerdelete")->__("Email"),
						"name" => "email",
						));
					

				if (Mage::getSingleton("adminhtml/session")->getCustomerdeleteData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getCustomerdeleteData());
					Mage::getSingleton("adminhtml/session")->setCustomerdeleteData(null);
				} 
				elseif(Mage::registry("customerdelete_data")) {
				    $form->setValues(Mage::registry("customerdelete_data")->getData());
				}
				return parent::_prepareForm();
		}
}

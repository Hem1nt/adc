<?php
class Iksula_Cashback_Block_Adminhtml_Cashback_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("cashback_form", array("legend"=>Mage::helper("cashback")->__("Item information")));

				
						$fieldset->addField("id", "text", array(
						"label" => Mage::helper("cashback")->__("Id"),
						"name" => "id",
						));
					
						$fieldset->addField("user_email", "text", array(
						"label" => Mage::helper("cashback")->__("Email"),
						"name" => "user_email",
						));
					
						$fieldset->addField("coupon_code", "text", array(
						"label" => Mage::helper("cashback")->__("Coupon"),
						"name" => "coupon_code",
						));
					
						$fieldset->addField("amount", "text", array(
						"label" => Mage::helper("cashback")->__("Amount"),
						"name" => "amount",
						));
					
						$fieldset->addField("use", "text", array(
						"label" => Mage::helper("cashback")->__("Uses"),
						"name" => "use",
						));
					

				if (Mage::getSingleton("adminhtml/session")->getCashbackData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getCashbackData());
					Mage::getSingleton("adminhtml/session")->setCashbackData(null);
				} 
				elseif(Mage::registry("cashback_data")) {
				    $form->setValues(Mage::registry("cashback_data")->getData());
				}
				return parent::_prepareForm();
		}
}

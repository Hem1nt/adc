<?php
class Iksula_Variations_Block_Adminhtml_Variations_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("variations_form", array("legend"=>Mage::helper("variations")->__("Item information")));

				
						$fieldset->addField("product_sku", "text", array(
						"label" => Mage::helper("variations")->__("Sku"),
						"name" => "product_sku",
						));
					
						$fieldset->addField("variations_strength", "text", array(
						"label" => Mage::helper("variations")->__("Strength"),
						"name" => "variations_strength",
						));
					
						$fieldset->addField("variations_url", "text", array(
						"label" => Mage::helper("variations")->__("Product Url"),
						"name" => "variations_url",
						));
					
						$fieldset->addField("sort_order", "text", array(
						"label" => Mage::helper("variations")->__("Sort Order"),
						"name" => "sort_order",
						));
					

				if (Mage::getSingleton("adminhtml/session")->getVariationsData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getVariationsData());
					Mage::getSingleton("adminhtml/session")->setVariationsData(null);
				} 
				elseif(Mage::registry("variations_data")) {
				    $form->setValues(Mage::registry("variations_data")->getData());
				}
				return parent::_prepareForm();
		}
}

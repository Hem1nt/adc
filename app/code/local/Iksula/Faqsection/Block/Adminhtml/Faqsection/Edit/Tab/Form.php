<?php
class Iksula_Faqsection_Block_Adminhtml_Faqsection_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("faqsection_form", array("legend"=>Mage::helper("faqsection")->__("Item information")));

				
						$fieldset->addField("type_title", "text", array(
						"label" => Mage::helper("faqsection")->__("Type Titile"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "type_title",
						));
					

				if (Mage::getSingleton("adminhtml/session")->getFaqsectionData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getFaqsectionData());
					Mage::getSingleton("adminhtml/session")->setFaqsectionData(null);
				} 
				elseif(Mage::registry("faqsection_data")) {
				    $form->setValues(Mage::registry("faqsection_data")->getData());
				}
				return parent::_prepareForm();
		}
}

<?php
class Iksula_Medical_Block_Adminhtml_Medical_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("medical_form", array("legend"=>Mage::helper("medical")->__("Item information")));

				
						$fieldset->addField("orderid", "text", array(
						"label" => Mage::helper("medical")->__("Order Id"),
						"name" => "orderid",
						));
					
						$fieldset->addField("physicianname", "text", array(
						"label" => Mage::helper("medical")->__("Physician Name"),
						"name" => "physicianname",
						));
					
						$fieldset->addField("physiciantelephone", "text", array(
						"label" => Mage::helper("medical")->__("Physician Telephone"),
						"name" => "physiciantelephone",
						));
					
						$fieldset->addField("drugallergies", "textarea", array(
						"label" => Mage::helper("medical")->__("Drug Allergies"),
						"name" => "drugallergies",
						));
					
						$fieldset->addField("currentmedications", "textarea", array(
						"label" => Mage::helper("medical")->__("Current Medications"),
						"name" => "currentmedications",
						));
					
						$fieldset->addField("currenttreatments", "text", array(
						"label" => Mage::helper("medical")->__("Current Treatments"),
						"name" => "currenttreatments",
						));
					
						$fieldset->addField("smoke", "text", array(
						"label" => Mage::helper("medical")->__("Smoke"),
						"name" => "smoke",
						));
					
						$fieldset->addField("drink", "text", array(
						"label" => Mage::helper("medical")->__("Drink"),
						"name" => "drink",
						));
					

				if (Mage::getSingleton("adminhtml/session")->getMedicalData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getMedicalData());
					Mage::getSingleton("adminhtml/session")->setMedicalData(null);
				} 
				elseif(Mage::registry("medical_data")) {
				    $form->setValues(Mage::registry("medical_data")->getData());
				}
				return parent::_prepareForm();
		}
}

<?php
class Iksula_Prescription_Block_Adminhtml_Prescription_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("prescription_form", array("legend"=>Mage::helper("prescription")->__("Item information")));
				$id = Mage::registry("prescription_data")->getData('id');
				$prescriptionModel = Mage::getModel('prescription/prescription')->load($id);
				$smokeVal = $prescriptionModel->getData('smoke');
				$drinkVal = $prescriptionModel->getData('drink');
				
						$fieldset->addField("id", "text", array(
						"label" => Mage::helper("prescription")->__("Id"),
						"name" => "id",
						));
					
						$fieldset->addField("order_id", "text", array(
						"label" => Mage::helper("prescription")->__("Order Id"),
						"name" => "order_id",
						));
					
						$fieldset->addField("customer_name", "text", array(
						"label" => Mage::helper("prescription")->__("Customer Name"),
						"name" => "customer_name",
						));

						$fieldset->addField("primary_physicians_name", "text", array(
						"label" => Mage::helper("prescription")->__("Physicians Name"),
						"name" => "	primary_physicians_name",
						));

						$fieldset->addField("physicians_telephone_no", "text", array(
						"label" => Mage::helper("prescription")->__("Physicians Telephone No"),
						"name" => "physicians_telephone_no",
						));

						$fieldset->addField("drug_allergies", "text", array(
						"label" => Mage::helper("prescription")->__("Drug Allergies"),
						"name" => "drug_allergies",
						));

						$fieldset->addField("current_medications_allergies", "text", array(
						"label" => Mage::helper("prescription")->__("Current Medications"),
						"name" => "current_medications_allergies",
						));

						$fieldset->addField("current_treatments", "text", array(
						"label" => Mage::helper("prescription")->__("Current Treatments"),
						"name" => "current_treatments",
						));

						$fieldset->addField("smoke", "select", array(
						"label" => Mage::helper("prescription")->__("Smoke"),
						"name" => "smoke",
						'value'     => $smokeVal,
			            'values'    => array(array('value'=>'0','label'=>'Select Type'),
			                            array('value'=>'no','label'=>'No'),
			                            array('value'=>'yes','label'=>'Yes'),
			                       ),
						));

						$fieldset->addField("drink", "select", array(
						"label" => Mage::helper("prescription")->__("Drink"),
						"name" => "drink",
						'value'     => $drinkVal,
			            'values'    => array(array('value'=>'0','label'=>'Select Type'),
			                            array('value'=>'no','label'=>'No'),
			                            array('value'=>'yes','label'=>'Yes'),
			                       ),
						));
					
				if (Mage::getSingleton("adminhtml/session")->getPrescriptionData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getPrescriptionData());
					Mage::getSingleton("adminhtml/session")->setPrescriptionData(null);
				} 
				elseif(Mage::registry("prescription_data")) {
				    $form->setValues(Mage::registry("prescription_data")->getData());
				}
				return parent::_prepareForm();
		}
}

<?php
class Iksula_Clicktoform_Block_Adminhtml_Clicktoform_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("clicktoform_form", array("legend"=>Mage::helper("clicktoform")->__("Customer Calling` information")));

				
						$fieldset->addField("customer_name", "text", array(
						"label" => Mage::helper("clicktoform")->__("Customer Name"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "customer_name",
						));
						$fieldset->addField("customer_email", "text", array(
						"label" => Mage::helper("clicktoform")->__("Customer Email"),					
						"class" => "required-entry validate-email",
						"required" => true,
						"name" => "customer_email",
						));
						$fieldset->addField("customer_mobileno", "text", array(
						"label" => Mage::helper("clicktoform")->__("Customer Contact Number"),					
						"class" => "required-entry validate-number",
						"required" => true,
						"name" => "customer_mobileno",
						));

						$fieldset->addField("customer_time", "text", array(
						"label" => Mage::helper("clicktoform")->__("Customer Calling Time"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "customer_time",
						));

						$fieldset->addField("customer_comment", "textarea", array(
						"label" => Mage::helper("clicktoform")->__("Customer Comment"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "customer_comment",
						));

						$fieldset->addField("customer_calling_status", "select", array(
						"label" => Mage::helper("clicktoform")->__("Customer Calling Status"),			
						"required" => false,
						"name" => "customer_calling_status",
       				    'values' => array(
                            array('value'=>'0','label'=>'Disabled'),
                            array('value'=>'1','label'=>'Enabled'),
                       ),
						));
						
					

				if (Mage::getSingleton("adminhtml/session")->getClicktoformData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getClicktoformData());
					Mage::getSingleton("adminhtml/session")->setClicktoformData(null);
				} 
				elseif(Mage::registry("clicktoform_data")) {
				    $form->setValues(Mage::registry("clicktoform_data")->getData());
				}
				return parent::_prepareForm();
		}
}

<?php
class Iksula_Refillreminder_Block_Adminhtml_Orderreminder_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("orderreminder_form", array("legend"=>Mage::helper("refillreminder")->__("Item information")));

				
						$fieldset->addField("customer_email", "text", array(
						"label" => Mage::helper("refillreminder")->__("Customer Email"),					
						// "class" => "required-entry",
						"required" => false,
						"name" => "customer_email",
						));
					
						$fieldset->addField("product_sku", "text", array(
						"label" => Mage::helper("refillreminder")->__("Product SKU"),					
						// "class" => "required-entry",
						"required" => false,
						"name" => "product_sku",
						));
						$fieldset->addField("product_name", "text", array(
						"label" => Mage::helper("refillreminder")->__("Product Name"),					
						// "class" => "required-entry",
						"required" => false,
						"name" => "product_name",
						));
						$fieldset->addField("customer_telephone", "text", array(
						"label" => Mage::helper("refillreminder")->__("Customer Telephone"),					
						// "class" => "required-entry",
						"required" => false,
						"name" => "customer_telephone",
						));
						$fieldset->addField("reminder_days", "text", array(
						"label" => Mage::helper("refillreminder")->__("Days"),					
						// "class" => "required-entry",
						"required" => false,
						"name" => "reminder_days",
						));

						$fieldset->addField("order_id", "text", array(
						"label" => Mage::helper("refillreminder")->__("Order Id"),					
						// "class" => "required-entry",
						"required" => false,
						"name" => "order_id",
						));

						$fieldset->addField("status", "select", array(
						"label" => Mage::helper("refillreminder")->__("Status"),					
						// "class" => "required-entry",
						"required" => false,
						"name" => "status",
       				    'values' => array(
                            array('value'=>'0','label'=>'Disabled'),
                            array('value'=>'1','label'=>'Enabled'),
                       ),
						));

						$fieldset->addField("comment", "textarea", array(
						"label" => Mage::helper("refillreminder")->__("Comment"),					
						// "class" => "required-entry",
						"required" => false,
						"name" => "comment",
						));		
						

				if (Mage::getSingleton("adminhtml/session")->getOrderreminderData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getOrderreminderData());
					Mage::getSingleton("adminhtml/session")->SetOrderreminderData(null);
				} 
				elseif(Mage::registry("orderreminder_data")) {
				    $form->setValues(Mage::registry("orderreminder_data")->getData());
				}
				return parent::_prepareForm();
		}
}

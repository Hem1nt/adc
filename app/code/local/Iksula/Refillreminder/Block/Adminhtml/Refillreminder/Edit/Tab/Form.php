<?php
class Iksula_Refillreminder_Block_Adminhtml_Refillreminder_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("refillreminder_form", array("legend"=>Mage::helper("refillreminder")->__("Item information")));

				
						$fieldset->addField("customer_email", "text", array(
						"label" => Mage::helper("refillreminder")->__("Customer Email"),					
						// "class" => "required-entry",
						"required" => false,
						"name" => "customer_email",
						));
					    $fieldset->addField("order_Id", "text", array(
						"label" => Mage::helper("refillreminder")->__("Order Id"),					
						// "class" => "required-entry",
						"required" => false,
						"name" => "order_Id",
						));
						$fieldset->addField("customer_name", "text", array(
						"label" => Mage::helper("refillreminder")->__("Customer Name"),					
						// "class" => "required-entry",
						"required" => false,
						"name" => "customer_name",
						));
						/*$fieldset->addField("product_sku", "text", array(
						"label" => Mage::helper("refillreminder")->__("Product SKU"),					
						// "class" => "required-entry",
						"required" => false,
						"name" => "product_sku",
						));*/
						/*$fieldset->addField("product_name", "text", array(
						"label" => Mage::helper("refillreminder")->__("Product Name"),					
						// "class" => "required-entry",
						"required" => false,
						"name" => "product_name",
						));*/
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

						$dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(
							Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
						);

						/*$fieldset->addField("last_mail_sent", "date", array(
						"label" => Mage::helper("refillreminder")->__("Last Email Date"),					
						// "class" => "required-entry",
						"required" => false,
						"name" => "last_mail_sent",
						'time' => true,
						'image'        => $this->getSkinUrl('images/grid-cal.gif'),
						'format'       => $dateFormatIso
						));*/

						/*$fieldset->addField("next_mail_on", "date", array(
						"label" => Mage::helper("refillreminder")->__("Next Reminder Date"),					
						// "class" => "required-entry",
						"required" => false,
						"name" => "next_mail_on",
						'time' => true,
						'image'        => $this->getSkinUrl('images/grid-cal.gif'),
						'format'       => $dateFormatIso
						));
*/


						$fieldset->addField("remind_flag", "select", array(
						"label" => Mage::helper("refillreminder")->__("Status"),					
						// "class" => "required-entry",
						"required" => false,
						"name" => "remind_flag",
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
						

				if (Mage::getSingleton("adminhtml/session")->getRefillreminderData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getRefillreminderData());
					Mage::getSingleton("adminhtml/session")->setRefillreminderData(null);
				} 
				elseif(Mage::registry("refillreminder_data")) {
				    $form->setValues(Mage::registry("refillreminder_data")->getData());
				}
				return parent::_prepareForm();
		}
}

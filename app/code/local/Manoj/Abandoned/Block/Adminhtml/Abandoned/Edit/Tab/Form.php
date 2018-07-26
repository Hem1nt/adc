<?php
class Manoj_Abandoned_Block_Adminhtml_Abandoned_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("abandoned_form", array("legend"=>Mage::helper("abandoned")->__("Item information")));

				
						$fieldset->addField("email_id", "text", array(
						"label" => Mage::helper("abandoned")->__("email_id"),
						"name" => "email_id",
						));
					
						$fieldset->addField("quote_id", "text", array(
						"label" => Mage::helper("abandoned")->__("quote_id"),
						"name" => "quote_id",
						));

						$fieldset->addField("abandoned_page_capture", "text", array(
						"label" => Mage::helper("abandoned")->__("abandoned_page_capture"),
						"name" => "capture_page",
						));

									
						 $fieldset->addField('is_email_send', 'select', array(
						'label'     => Mage::helper('abandoned')->__('is_email_send'),
						'values'   => Manoj_Abandoned_Block_Adminhtml_Abandoned_Grid::getValueArray3(),
						'name' => 'is_email_send',
						));				
						 $fieldset->addField('is_purchase', 'select', array(
						'label'     => Mage::helper('abandoned')->__('is_purchase'),
						'values'   => Manoj_Abandoned_Block_Adminhtml_Abandoned_Grid::getValueArray4(),
						'name' => 'is_purchase',
						));
						$dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(
							Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
						);

						$fieldset->addField('created_time', 'date', array(
						'label'        => Mage::helper('abandoned')->__('created_time'),
						'name'         => 'created_time',
						'time' => true,
						'image'        => $this->getSkinUrl('images/grid-cal.gif'),
						'format'       => $dateFormatIso
						));
						$dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(
							Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
						);

						$fieldset->addField('update_time', 'date', array(
						'label'        => Mage::helper('abandoned')->__('update_time'),
						'name'         => 'update_time',
						'time' => true,
						'image'        => $this->getSkinUrl('images/grid-cal.gif'),
						'format'       => $dateFormatIso
						));
						$fieldset->addField("product_ids", "text", array(
						"label" => Mage::helper("abandoned")->__("product_ids"),
						"name" => "product_ids",
						));
					
						$fieldset->addField("subtotal", "text", array(
						"label" => Mage::helper("abandoned")->__("subtotal"),
						"name" => "subtotal",
						));
					

				if (Mage::getSingleton("adminhtml/session")->getAbandonedData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getAbandonedData());
					Mage::getSingleton("adminhtml/session")->setAbandonedData(null);
				} 
				elseif(Mage::registry("abandoned_data")) {
				    $form->setValues(Mage::registry("abandoned_data")->getData());
				}
				return parent::_prepareForm();
		}
}

<?php
class Manoj_Abandoned_Block_Adminhtml_Abandonedorder_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("abandoned_form", array("legend"=>Mage::helper("abandoned")->__("Item information")));

				$fieldset->addField("order_number", "text", array(
						"label" => Mage::helper("abandoned")->__("order_number"),
						"name" => "order_number",
						));
					
						$fieldset->addField("email_id", "text", array(
						"label" => Mage::helper("abandoned")->__("email_id"),
						"name" => "email_id",
						));

						$dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(
							Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
						);

						$fieldset->addField('created_time', 'date', array(
						'label'        => Mage::helper('abandoned')->__('purchased_time'),
						'name'         => 'created_time',
						'time' => true,
						'image'        => $this->getSkinUrl('images/grid-cal.gif'),
						'format'       => $dateFormatIso
						));
						$dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(
							Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
						);

						/*$fieldset->addField('update_time', 'date', array(
						'label'        => Mage::helper('abandoned')->__('update_time'),
						'name'         => 'update_time',
						'time' => true,
						'image'        => $this->getSkinUrl('images/grid-cal.gif'),
						'format'       => $dateFormatIso
						));
				*/

				if (Mage::getSingleton("adminhtml/session")->getAbandonedorderData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getAbandonedorderData());
					Mage::getSingleton("adminhtml/session")->setAbandonedorderData(null);
				} 
				elseif(Mage::registry("abandonedorder_data")) {
				    $form->setValues(Mage::registry("abandonedorder_data")->getData());
				}
				return parent::_prepareForm();
		}
}

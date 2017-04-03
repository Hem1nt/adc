<?php
class Iksula_Customerinfo_Block_Adminhtml_Customerinfo_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("customerinfo_form", array("legend"=>Mage::helper("customerinfo")->__("Item information")));

				
						$fieldset->addField("name", "text", array(
						"label" => Mage::helper("customerinfo")->__("name"),
						"name" => "name",
						));
					
						$fieldset->addField("email", "text", array(
						"label" => Mage::helper("customerinfo")->__("email"),
						"name" => "email",
						));
					
						$dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(
							Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
						);

						$fieldset->addField('dob', 'date', array(
						'label'        => Mage::helper('customerinfo')->__('dob'),
						'name'         => 'dob',
						'time' => true,
						'image'        => $this->getSkinUrl('images/grid-cal.gif'),
						'format'       => $dateFormatIso
						));
						$dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(
							Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
						);

						$fieldset->addField('anniversary', 'date', array(
						'label'        => Mage::helper('customerinfo')->__('anniversary'),
						'name'         => 'anniversary',
						'time' => true,
						'image'        => $this->getSkinUrl('images/grid-cal.gif'),
						'format'       => $dateFormatIso
						));

				if (Mage::getSingleton("adminhtml/session")->getCustomerinfoData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getCustomerinfoData());
					Mage::getSingleton("adminhtml/session")->setCustomerinfoData(null);
				} 
				elseif(Mage::registry("customerinfo_data")) {
				    $form->setValues(Mage::registry("customerinfo_data")->getData());
				}
				return parent::_prepareForm();
		}
}

<?php
class Iksula_Backendfaq_Block_Adminhtml_Backendfaq_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("backendfaq_form", array("legend"=>Mage::helper("backendfaq")->__("Item information")));

				
						$fieldset->addField("question", "textarea", array(
						"label" => Mage::helper("backendfaq")->__("Question"),
						"name" => "question",
						));
					
						$fieldset->addField("answer", "textarea", array(
						"label" => Mage::helper("backendfaq")->__("Answer"),
						"name" => "answer",
						));
					
						$fieldset->addField("username", "text", array(
						"label" => Mage::helper("backendfaq")->__("Username"),
						"name" => "username",
						));
					
						$fieldset->addField("topic", "textarea", array(
						"label" => Mage::helper("backendfaq")->__("Topic"),
						"name" => "topic",
						));
					
						$dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(
							Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
						);

						$fieldset->addField('date', 'date', array(
						'label'        => Mage::helper('backendfaq')->__('Date'),
						'name'         => 'date',
						'time' => true,
						'image'        => $this->getSkinUrl('images/grid-cal.gif'),
						'format'       => $dateFormatIso
						));

				if (Mage::getSingleton("adminhtml/session")->getBackendfaqData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getBackendfaqData());
					Mage::getSingleton("adminhtml/session")->setBackendfaqData(null);
				} 
				elseif(Mage::registry("backendfaq_data")) {
				    $form->setValues(Mage::registry("backendfaq_data")->getData());
				}
				return parent::_prepareForm();
		}
}

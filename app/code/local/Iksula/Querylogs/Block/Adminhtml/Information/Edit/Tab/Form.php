<?php
class Iksula_Querylogs_Block_Adminhtml_Information_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("querylogs_form", array("legend"=>Mage::helper("querylogs")->__("Item information")));

				
						$fieldset->addField("email", "text", array(
						"label" => Mage::helper("querylogs")->__("email"),
						"name" => "email",
						));
					
						$fieldset->addField("querytype", "text", array(
						"label" => Mage::helper("querylogs")->__("querytype"),
						"name" => "querytype",
						));
					
						$fieldset->addField("comment", "textarea", array(
						"label" => Mage::helper("querylogs")->__("comment"),
						"name" => "comment",
						));
					
						$dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(
							Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
						);

						$fieldset->addField('date_of_query', 'date', array(
						'label'        => Mage::helper('querylogs')->__('date_of_query'),
						'name'         => 'date_of_query',
						'time' => true,
						'image'        => $this->getSkinUrl('images/grid-cal.gif'),
						'format'       => $dateFormatIso
						));

				if (Mage::getSingleton("adminhtml/session")->getInformationData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getInformationData());
					Mage::getSingleton("adminhtml/session")->setInformationData(null);
				} 
				elseif(Mage::registry("information_data")) {
				    $form->setValues(Mage::registry("information_data")->getData());
				}
				return parent::_prepareForm();
		}
}

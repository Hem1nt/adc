<?php
class Iksula_Echecksteps_Block_Adminhtml_Echecksteps_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("echecksteps_form", array("legend"=>Mage::helper("echecksteps")->__("Item information")));

								
						$fieldset->addField('image_name', 'image', array(
						'label' => Mage::helper('echecksteps')->__('Image'),
						'name' => 'image_name',
						'note' => '(*.jpg, *.png, *.gif)',
						));
						$fieldset->addField("steps_order", "text", array(
						"label" => Mage::helper("echecksteps")->__("Steps"),
						"name" => "steps_order",
						));
					

				if (Mage::getSingleton("adminhtml/session")->getEcheckstepsData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getEcheckstepsData());
					Mage::getSingleton("adminhtml/session")->setEcheckstepsData(null);
				} 
				elseif(Mage::registry("echecksteps_data")) {
				    $form->setValues(Mage::registry("echecksteps_data")->getData());
				}
				return parent::_prepareForm();
		}
}

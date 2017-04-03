<?php
class Iksula_Notifyoutofstock_Block_Adminhtml_Notifyoutofstock_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("notifyoutofstock_form", array("legend"=>Mage::helper("notifyoutofstock")->__("Item information")));

				
						$fieldset->addField("product_id", "text", array(
						"label" => Mage::helper("notifyoutofstock")->__("Product Id"),
						"name" => "product_id",
						));
					
						$dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(
							Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
						);

						$fieldset->addField('date', 'date', array(
						'label'        => Mage::helper('notifyoutofstock')->__('Date'),
						'name'         => 'date',
						'time' => true,
						'image'        => $this->getSkinUrl('images/grid-cal.gif'),
						'format'       => $dateFormatIso
						));

				if (Mage::getSingleton("adminhtml/session")->getNotifyoutofstockData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getNotifyoutofstockData());
					Mage::getSingleton("adminhtml/session")->setNotifyoutofstockData(null);
				} 
				elseif(Mage::registry("notifyoutofstock_data")) {
				    $form->setValues(Mage::registry("notifyoutofstock_data")->getData());
				}
				return parent::_prepareForm();
		}
}

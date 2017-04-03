<?php
class Iksula_Limitedsupply_Block_Adminhtml_Limitedsupply_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("limitedsupply_form", array("legend"=>Mage::helper("limitedsupply")->__("Item information")));


						$fieldset->addField("product_sku", "text", array(
						"label" => Mage::helper("limitedsupply")->__("product_sku"),
						"name" => "product_sku",
						));

						$fieldset->addField("product_name", "text", array(
						"label" => Mage::helper("limitedsupply")->__("product_name"),
						"name" => "product_name",
						));

						$fieldset->addField("comment", "textarea", array(
						"label" => Mage::helper("limitedsupply")->__("comment"),
						"name" => "comment",
						));

						 $fieldset->addField('is_active', 'select', array(
						'label'     => Mage::helper('limitedsupply')->__('is_active'),
						'values'   => Iksula_Limitedsupply_Block_Adminhtml_Limitedsupply_Grid::getValueArray4(),
						'name' => 'is_active',
						));

						$fieldset->addField("email", "text", array(
						"label" => Mage::helper("limitedsupply")->__("email"),
						"name" => "email",
						));

						$fieldset->addField("name", "text", array(
						"label" => Mage::helper("limitedsupply")->__("name"),
						"name" => "name",
						));

						$fieldset->addField("phone_no", "text", array(
						"label" => Mage::helper("limitedsupply")->__("phone_no"),
						"name" => "phone_no",
						));

						$fieldset->addField("quantity", "text", array(
						"label" => Mage::helper("limitedsupply")->__("quantity"),
						"name" => "quantity",
						));


				if (Mage::getSingleton("adminhtml/session")->getLimitedsupplyData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getLimitedsupplyData());
					Mage::getSingleton("adminhtml/session")->setLimitedsupplyData(null);
				}
				elseif(Mage::registry("limitedsupply_data")) {
				    $form->setValues(Mage::registry("limitedsupply_data")->getData());
				}
				return parent::_prepareForm();
		}
}

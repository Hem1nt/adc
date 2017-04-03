<?php
class Iksula_Shipmentinfo_Block_Adminhtml_Shipmentinfo_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("shipmentinfo_form", array("legend"=>Mage::helper("shipmentinfo")->__("Item information")));

				
						$fieldset->addField("customer_order_increment_id", "text", array(
						"label" => Mage::helper("shipmentinfo")->__("Order Id"),
						"name" => "customer_order_increment_id",
						));
					
						$dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(
							Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
						);

						$fieldset->addField('created_at', 'date', array(
						'label'        => Mage::helper('shipmentinfo')->__('Date'),
						'name'         => 'created_at',
						'time' => true,
						'image'        => $this->getSkinUrl('images/grid-cal.gif'),
						'format'       => $dateFormatIso
						));
						$fieldset->addField("total_items", "text", array(
						"label" => Mage::helper("shipmentinfo")->__("Total Items"),
						"name" => "total_items",
						));
					
						$fieldset->addField("non_shipped_items", "text", array(
						"label" => Mage::helper("shipmentinfo")->__("Non Shipped Items"),
						"name" => "non_shipped_items",
						));
					
						$fieldset->addField("shipped_items", "text", array(
						"label" => Mage::helper("shipmentinfo")->__("Shipped Items"),
						"name" => "shipped_items",
						));
					
						$fieldset->addField("shipped_item_sku", "text", array(
						"label" => Mage::helper("shipmentinfo")->__("Shipped Item Sku"),
						"name" => "shipped_item_sku",
						));
					

				if (Mage::getSingleton("adminhtml/session")->getShipmentinfoData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getShipmentinfoData());
					Mage::getSingleton("adminhtml/session")->setShipmentinfoData(null);
				} 
				elseif(Mage::registry("shipmentinfo_data")) {
				    $form->setValues(Mage::registry("shipmentinfo_data")->getData());
				}
				return parent::_prepareForm();
		}
}

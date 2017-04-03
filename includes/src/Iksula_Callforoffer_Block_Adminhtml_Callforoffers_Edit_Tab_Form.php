<?php
class Iksula_Callforoffer_Block_Adminhtml_Callforoffers_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("callforoffer_form", array("legend"=>Mage::helper("callforoffer")->__("Item information")));

				
						$fieldset->addField("cust_name", "text", array(
						"label" => Mage::helper("callforoffer")->__("Customer Name"),
						"name" => "cust_name",
						));
					
						$fieldset->addField("customerid", "text", array(
						"label" => Mage::helper("callforoffer")->__("Customer Id"),
						"name" => "customerid",
						));
					
						$fieldset->addField("customertelephoneno", "text", array(
						"label" => Mage::helper("callforoffer")->__("Customer Telephone No"),
						"name" => "customertelephoneno",
						));
									
						 $fieldset->addField('status', 'select', array(
						'label'     => Mage::helper('callforoffer')->__('Status'),
						'values'   => Iksula_Callforoffer_Block_Adminhtml_Callforoffers_Grid::getValueArray4(),
						'name' => 'status',
						));				
						 $fieldset->addField('customertype', 'select', array(
						'label'     => Mage::helper('callforoffer')->__('Customer Type'),
						'values'   => Iksula_Callforoffer_Block_Adminhtml_Callforoffers_Grid::getValueArray5(),
						'name' => 'customertype',
						));

				if (Mage::getSingleton("adminhtml/session")->getCallforoffersData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getCallforoffersData());
					Mage::getSingleton("adminhtml/session")->setCallforoffersData(null);
				} 
				elseif(Mage::registry("callforoffers_data")) {
				    $form->setValues(Mage::registry("callforoffers_data")->getData());
				}
				return parent::_prepareForm();
		}
}

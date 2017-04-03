<?php
	
class Iksula_Backendfaq_Block_Adminhtml_Backendfaq_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "id";
				$this->_blockGroup = "backendfaq";
				$this->_controller = "adminhtml_backendfaq";
				$this->_updateButton("save", "label", Mage::helper("backendfaq")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("backendfaq")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("backendfaq")->__("Save And Continue Edit"),
					"onclick"   => "saveAndContinueEdit()",
					"class"     => "save",
				), -100);

				$this->_addButton('button2', array(
					'label'     => Mage::helper('adminhtml')->__('Send Emails'),
					'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/sendemail') . '\')',
					'class'     => 'save',
					),-1,3);

				$this->_formScripts[] = "

							function saveAndContinueEdit(){
								editForm.submit($('edit_form').action+'back/edit/');
							}
						";
		}

		public function getHeaderText()
		{
				if( Mage::registry("backendfaq_data") && Mage::registry("backendfaq_data")->getId() ){

				    return Mage::helper("backendfaq")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("backendfaq_data")->getId()));

				} 
				else{

				     return Mage::helper("backendfaq")->__("Add Item");

				}
		}
}
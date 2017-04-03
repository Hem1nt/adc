<?php
	
class Iksula_Prescription_Block_Adminhtml_Prescription_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "id";
				$this->_blockGroup = "prescription";
				$this->_controller = "adminhtml_prescription";
				$this->_updateButton("save", "label", Mage::helper("prescription")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("prescription")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("prescription")->__("Save And Continue Edit"),
					"onclick"   => "saveAndContinueEdit()",
					"class"     => "save",
				), -100);



				$this->_formScripts[] = "

							function saveAndContinueEdit(){
								editForm.submit($('edit_form').action+'back/edit/');
							}
						";
		}

		public function getHeaderText()
		{
				if( Mage::registry("prescription_data") && Mage::registry("prescription_data")->getId() ){

				    return Mage::helper("prescription")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("prescription_data")->getId()));

				} 
				else{

				     return Mage::helper("prescription")->__("Add Item");

				}
		}
}
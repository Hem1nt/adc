<?php
	
class Iksula_Medical_Block_Adminhtml_Medical_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "id";
				$this->_blockGroup = "medical";
				$this->_controller = "adminhtml_medical";
				$this->_updateButton("save", "label", Mage::helper("medical")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("medical")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("medical")->__("Save And Continue Edit"),
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
				if( Mage::registry("medical_data") && Mage::registry("medical_data")->getId() ){

				    return Mage::helper("medical")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("medical_data")->getId()));

				} 
				else{

				     return Mage::helper("medical")->__("Add Item");

				}
		}
}
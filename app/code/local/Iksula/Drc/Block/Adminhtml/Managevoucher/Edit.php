<?php
	
class Iksula_Drc_Block_Adminhtml_Managevoucher_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "id";
				$this->_blockGroup = "drc";
				$this->_controller = "adminhtml_managevoucher";
				$this->_updateButton("save", "label", Mage::helper("drc")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("drc")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("drc")->__("Save And Continue Edit"),
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
				if( Mage::registry("managevoucher_data") && Mage::registry("managevoucher_data")->getId() ){

				    return Mage::helper("drc")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("managevoucher_data")->getId()));

				} 
				else{

				     return Mage::helper("drc")->__("Add Item");

				}
		}
}
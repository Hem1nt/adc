<?php
	
class Iksula_Customerdelete_Block_Adminhtml_Customerdelete_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "id";
				$this->_blockGroup = "customerdelete";
				$this->_controller = "adminhtml_customerdelete";
				$this->_updateButton("save", "label", Mage::helper("customerdelete")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("customerdelete")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("customerdelete")->__("Save And Continue Edit"),
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
				if( Mage::registry("customerdelete_data") && Mage::registry("customerdelete_data")->getId() ){

				    return Mage::helper("customerdelete")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("customerdelete_data")->getId()));

				} 
				else{

				     return Mage::helper("customerdelete")->__("Add Item");

				}
		}
}
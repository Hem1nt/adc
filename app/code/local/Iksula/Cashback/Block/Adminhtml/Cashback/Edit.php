<?php
	
class Iksula_Cashback_Block_Adminhtml_Cashback_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "id";
				$this->_blockGroup = "cashback";
				$this->_controller = "adminhtml_cashback";
				$this->_updateButton("save", "label", Mage::helper("cashback")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("cashback")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("cashback")->__("Save And Continue Edit"),
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
				if( Mage::registry("cashback_data") && Mage::registry("cashback_data")->getId() ){

				    return Mage::helper("cashback")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("cashback_data")->getId()));

				} 
				else{

				     return Mage::helper("cashback")->__("Add Item");

				}
		}
}
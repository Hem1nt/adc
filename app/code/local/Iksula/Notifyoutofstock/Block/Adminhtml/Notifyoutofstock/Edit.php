<?php
	
class Iksula_Notifyoutofstock_Block_Adminhtml_Notifyoutofstock_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "notify_id";
				$this->_blockGroup = "notifyoutofstock";
				$this->_controller = "adminhtml_notifyoutofstock";
				$this->_updateButton("save", "label", Mage::helper("notifyoutofstock")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("notifyoutofstock")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("notifyoutofstock")->__("Save And Continue Edit"),
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
				if( Mage::registry("notifyoutofstock_data") && Mage::registry("notifyoutofstock_data")->getId() ){

				    return Mage::helper("notifyoutofstock")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("notifyoutofstock_data")->getId()));

				} 
				else{

				     return Mage::helper("notifyoutofstock")->__("Add Item");

				}
		}
}
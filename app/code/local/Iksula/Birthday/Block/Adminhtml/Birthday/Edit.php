<?php
	
class Iksula_Birthday_Block_Adminhtml_Birthday_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "birthday_id";
				$this->_blockGroup = "birthday";
				$this->_controller = "adminhtml_birthday";
				$this->_updateButton("save", "label", Mage::helper("birthday")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("birthday")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("birthday")->__("Save And Continue Edit"),
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
				if( Mage::registry("birthday_data") && Mage::registry("birthday_data")->getId() ){

				    return Mage::helper("birthday")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("birthday_data")->getId()));

				} 
				else{

				     return Mage::helper("birthday")->__("Add Item");

				}
		}
}
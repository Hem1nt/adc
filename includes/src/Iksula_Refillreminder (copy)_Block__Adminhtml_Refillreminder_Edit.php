<?php
	
class Iksula_Refillreminder_Block_Adminhtml_Refillreminder_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "reminder_id";
				$this->_blockGroup = "refillreminder";
				$this->_controller = "adminhtml_refillreminder";
				$this->_updateButton("save", "label", Mage::helper("refillreminder")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("refillreminder")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("refillreminder")->__("Save And Continue Edit"),
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
				if( Mage::registry("refillreminder_data") && Mage::registry("refillreminder_data")->getId() ){

				    return Mage::helper("refillreminder")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("refillreminder_data")->getId()));

				} 
				else{

				     return Mage::helper("refillreminder")->__("Add Item");

				}
		}
}
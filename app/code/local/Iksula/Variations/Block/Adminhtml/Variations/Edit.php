<?php
	
class Iksula_Variations_Block_Adminhtml_Variations_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "variations_id";
				$this->_blockGroup = "variations";
				$this->_controller = "adminhtml_variations";
				$this->_updateButton("save", "label", Mage::helper("variations")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("variations")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("variations")->__("Save And Continue Edit"),
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
				if( Mage::registry("variations_data") && Mage::registry("variations_data")->getId() ){

				    return Mage::helper("variations")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("variations_data")->getId()));

				} 
				else{

				     return Mage::helper("variations")->__("Add Item");

				}
		}
}
<?php
	
class Iksula_Echecksteps_Block_Adminhtml_Echecksteps_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "id";
				$this->_blockGroup = "echecksteps";
				$this->_controller = "adminhtml_echecksteps";
				$this->_updateButton("save", "label", Mage::helper("echecksteps")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("echecksteps")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("echecksteps")->__("Save And Continue Edit"),
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
				if( Mage::registry("echecksteps_data") && Mage::registry("echecksteps_data")->getId() ){

				    return Mage::helper("echecksteps")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("echecksteps_data")->getId()));

				} 
				else{

				     return Mage::helper("echecksteps")->__("Add Item");

				}
		}
}
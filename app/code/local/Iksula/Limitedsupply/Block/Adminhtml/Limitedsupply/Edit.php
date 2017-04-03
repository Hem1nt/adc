<?php
	
class Iksula_Limitedsupply_Block_Adminhtml_Limitedsupply_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "id";
				$this->_blockGroup = "limitedsupply";
				$this->_controller = "adminhtml_limitedsupply";
				$this->_updateButton("save", "label", Mage::helper("limitedsupply")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("limitedsupply")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("limitedsupply")->__("Save And Continue Edit"),
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
				if( Mage::registry("limitedsupply_data") && Mage::registry("limitedsupply_data")->getId() ){

				    return Mage::helper("limitedsupply")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("limitedsupply_data")->getId()));

				} 
				else{

				     return Mage::helper("limitedsupply")->__("Add Item");

				}
		}
}
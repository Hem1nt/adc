<?php
	
class Iksula_Querylogs_Block_Adminhtml_Information_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "contact_id";
				$this->_blockGroup = "querylogs";
				$this->_controller = "adminhtml_information";
				$this->_updateButton("save", "label", Mage::helper("querylogs")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("querylogs")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("querylogs")->__("Save And Continue Edit"),
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
				if( Mage::registry("information_data") && Mage::registry("information_data")->getId() ){

				    return Mage::helper("querylogs")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("information_data")->getId()));

				} 
				else{

				     return Mage::helper("querylogs")->__("Add Item");

				}
		}
}
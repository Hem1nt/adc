<?php
	
class Iksula_Clicktoform_Block_Adminhtml_Clicktoform_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "customeform_id";
				$this->_blockGroup = "clicktoform";
				$this->_controller = "adminhtml_clicktoform";
				$this->_updateButton("save", "label", Mage::helper("clicktoform")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("clicktoform")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("clicktoform")->__("Save And Continue Edit"),
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
				if( Mage::registry("clicktoform_data") && Mage::registry("clicktoform_data")->getId() ){

				    return Mage::helper("clicktoform")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("clicktoform_data")->getId()));

				} 
				else{

				     return Mage::helper("clicktoform")->__("Add Item");

				}
		}
}
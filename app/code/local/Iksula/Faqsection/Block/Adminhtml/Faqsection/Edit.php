<?php
	
class Iksula_Faqsection_Block_Adminhtml_Faqsection_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "sections_typeid";
				$this->_blockGroup = "faqsection";
				$this->_controller = "adminhtml_faqsection";
				$this->_updateButton("save", "label", Mage::helper("faqsection")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("faqsection")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("faqsection")->__("Save And Continue Edit"),
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
				if( Mage::registry("faqsection_data") && Mage::registry("faqsection_data")->getId() ){

				    return Mage::helper("faqsection")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("faqsection_data")->getId()));

				} 
				else{

				     return Mage::helper("faqsection")->__("Add Item");

				}
		}
}
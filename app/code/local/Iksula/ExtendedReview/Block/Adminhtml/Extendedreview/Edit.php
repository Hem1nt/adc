<?php
	
class Iksula_ExtendedReview_Block_Adminhtml_Extendedreview_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "id";
				$this->_blockGroup = "extendedreview";
				$this->_controller = "adminhtml_extendedreview";
				$this->_updateButton("save", "label", Mage::helper("extendedreview")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("extendedreview")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("extendedreview")->__("Save And Continue Edit"),
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
				if( Mage::registry("extendedreview_data") && Mage::registry("extendedreview_data")->getId() ){

				    return Mage::helper("extendedreview")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("extendedreview_data")->getId()));

				} 
				else{

				     return Mage::helper("extendedreview")->__("Add Item");

				}
		}
}
<?php
	
class Iksula_Trustedcompany_Block_Adminhtml_Reviews_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "review_id";
				$this->_blockGroup = "trustedcompany";
				$this->_controller = "adminhtml_reviews";
				$this->_updateButton("save", "label", Mage::helper("trustedcompany")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("trustedcompany")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("trustedcompany")->__("Save And Continue Edit"),
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
				if( Mage::registry("reviews_data") && Mage::registry("reviews_data")->getId() ){

				    return Mage::helper("trustedcompany")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("reviews_data")->getId()));

				} 
				else{

				     return Mage::helper("trustedcompany")->__("Add Item");

				}
		}
}
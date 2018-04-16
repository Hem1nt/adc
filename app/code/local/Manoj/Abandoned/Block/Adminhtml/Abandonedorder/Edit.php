<?php
	
class Manoj_Abandoned_Block_Adminhtml_Abandonedorder_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "abandoned_order_id";
				$this->_blockGroup = "abandoned";
				$this->_controller = "adminhtml_abandonedorder";
				$this->_updateButton("save", "label", Mage::helper("abandoned")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("abandoned")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("abandoned")->__("Save And Continue Edit"),
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
				if( Mage::registry("abandonedorder_data") && Mage::registry("abandonedorder_data")->getId() ){

				    return Mage::helper("abandoned")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("abandonedorder_data")->getId()));

				} 
				else{

				     return Mage::helper("abandoned")->__("Add Item");

				}
		}
}
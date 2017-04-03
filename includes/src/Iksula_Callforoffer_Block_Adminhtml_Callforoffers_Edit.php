<?php
	
class Iksula_Callforoffer_Block_Adminhtml_Callforoffers_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "id";
				$this->_blockGroup = "callforoffer";
				$this->_controller = "adminhtml_callforoffers";
				$this->_updateButton("save", "label", Mage::helper("callforoffer")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("callforoffer")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("callforoffer")->__("Save And Continue Edit"),
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
				if( Mage::registry("callforoffers_data") && Mage::registry("callforoffers_data")->getId() ){

				    return Mage::helper("callforoffer")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("callforoffers_data")->getId()));

				} 
				else{

				     return Mage::helper("callforoffer")->__("Add Item");

				}
		}
}
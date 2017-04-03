<?php
	
class Iksula_Faqsection_Block_Adminhtml_Faqquestions_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "question_id";
				$this->_blockGroup = "faqsection";
				$this->_controller = "adminhtml_faqquestions";
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

		protected function _prepareLayout()
		{
			// Load Wysiwyg on demand and Prepare layout
			if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled() && ($block = $this->getLayout()->getBlock('head')))
			{
				$block->setCanLoadTinyMce(true);
			}
			parent::_prepareLayout();
		}

		public function getHeaderText()
		{
				if( Mage::registry("faqquestions_data") && Mage::registry("faqquestions_data")->getId() ){

				    return Mage::helper("faqsection")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("faqquestions_data")->getId()));

				} 
				else{

				     return Mage::helper("faqsection")->__("Add Item");

				}
		}
}
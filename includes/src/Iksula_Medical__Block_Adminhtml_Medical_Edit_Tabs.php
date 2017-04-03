<?php
class Iksula_Medical_Block_Adminhtml_Medical_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("medical_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("medical")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("medical")->__("Item Information"),
				"title" => Mage::helper("medical")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("medical/adminhtml_medical_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}

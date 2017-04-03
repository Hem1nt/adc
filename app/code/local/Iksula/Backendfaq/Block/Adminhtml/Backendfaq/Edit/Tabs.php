<?php
class Iksula_Backendfaq_Block_Adminhtml_Backendfaq_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("backendfaq_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("backendfaq")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("backendfaq")->__("Item Information"),
				"title" => Mage::helper("backendfaq")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("backendfaq/adminhtml_backendfaq_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}

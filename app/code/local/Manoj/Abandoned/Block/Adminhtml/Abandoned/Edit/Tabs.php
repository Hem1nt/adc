<?php
class Manoj_Abandoned_Block_Adminhtml_Abandoned_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("abandoned_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("abandoned")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("abandoned")->__("Item Information"),
				"title" => Mage::helper("abandoned")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("abandoned/adminhtml_abandoned_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}

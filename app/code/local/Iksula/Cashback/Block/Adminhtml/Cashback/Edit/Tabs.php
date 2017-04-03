<?php
class Iksula_Cashback_Block_Adminhtml_Cashback_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("cashback_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("cashback")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("cashback")->__("Item Information"),
				"title" => Mage::helper("cashback")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("cashback/adminhtml_cashback_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}

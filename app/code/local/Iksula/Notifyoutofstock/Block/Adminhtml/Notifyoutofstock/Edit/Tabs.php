<?php
class Iksula_Notifyoutofstock_Block_Adminhtml_Notifyoutofstock_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("notifyoutofstock_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("notifyoutofstock")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("notifyoutofstock")->__("Item Information"),
				"title" => Mage::helper("notifyoutofstock")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("notifyoutofstock/adminhtml_notifyoutofstock_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}

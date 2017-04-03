<?php
class Iksula_Customerdelete_Block_Adminhtml_Customerdelete_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("customerdelete_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("customerdelete")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("customerdelete")->__("Item Information"),
				"title" => Mage::helper("customerdelete")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("customerdelete/adminhtml_customerdelete_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}

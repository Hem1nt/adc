<?php
class Iksula_Drc_Block_Adminhtml_Managevoucher_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("managevoucher_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("drc")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("drc")->__("Item Information"),
				"title" => Mage::helper("drc")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("drc/adminhtml_managevoucher_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}

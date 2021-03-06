<?php
class Iksula_Querylogs_Block_Adminhtml_Information_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("information_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("querylogs")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("querylogs")->__("Item Information"),
				"title" => Mage::helper("querylogs")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("querylogs/adminhtml_information_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}

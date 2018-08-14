<?php
class Iksula_Clicktoform_Block_Adminhtml_Clicktoform_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("clicktoform_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("clicktoform")->__("Customer Calling Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("clicktoform")->__("Customer Calling Information"),
				"title" => Mage::helper("clicktoform")->__("Customer Calling Information"),
				"content" => $this->getLayout()->createBlock("clicktoform/adminhtml_clicktoform_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}

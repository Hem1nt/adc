<?php
class Iksula_Variations_Block_Adminhtml_Variations_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("variations_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("variations")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("variations")->__("Item Information"),
				"title" => Mage::helper("variations")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("variations/adminhtml_variations_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}

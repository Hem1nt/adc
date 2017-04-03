<?php
class Iksula_Faqsection_Block_Adminhtml_Faqquestions_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("faqquestions_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("faqsection")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("faqsection")->__("Item Information"),
				"title" => Mage::helper("faqsection")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("faqsection/adminhtml_faqquestions_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}

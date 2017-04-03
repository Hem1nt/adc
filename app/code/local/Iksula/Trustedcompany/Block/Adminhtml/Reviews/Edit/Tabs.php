<?php
class Iksula_Trustedcompany_Block_Adminhtml_Reviews_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("reviews_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("trustedcompany")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("trustedcompany")->__("Item Information"),
				"title" => Mage::helper("trustedcompany")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("trustedcompany/adminhtml_reviews_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}

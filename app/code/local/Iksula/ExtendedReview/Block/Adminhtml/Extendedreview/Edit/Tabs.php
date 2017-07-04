<?php
class Iksula_ExtendedReview_Block_Adminhtml_Extendedreview_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("extendedreview_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("extendedreview")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("extendedreview")->__("Item Information"),
				"title" => Mage::helper("extendedreview")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("extendedreview/adminhtml_extendedreview_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}

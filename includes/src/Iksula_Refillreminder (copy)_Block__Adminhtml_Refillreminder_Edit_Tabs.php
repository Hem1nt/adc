<?php
class Iksula_Refillreminder_Block_Adminhtml_Refillreminder_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("refillreminder_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("refillreminder")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("refillreminder")->__("Item Information"),
				"title" => Mage::helper("refillreminder")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("refillreminder/adminhtml_refillreminder_edit_tab_form")->toHtml(),
				));

				$this->addTab("bulk_section", array(
				"label" => Mage::helper("refillreminder")->__("Bulk Import Comment"),
				"title" => Mage::helper("refillreminder")->__("Bulk Import Comment"),
				"content" => $this->getLayout()->createBlock("refillreminder/adminhtml_refillreminder_edit_tab_bulk")->toHtml(),
				));
				
				return parent::_beforeToHtml();
		}

}

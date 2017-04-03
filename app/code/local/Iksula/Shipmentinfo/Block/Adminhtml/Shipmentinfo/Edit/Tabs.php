<?php
class Iksula_Shipmentinfo_Block_Adminhtml_Shipmentinfo_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("shipmentinfo_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("shipmentinfo")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("shipmentinfo")->__("Item Information"),
				"title" => Mage::helper("shipmentinfo")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("shipmentinfo/adminhtml_shipmentinfo_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}

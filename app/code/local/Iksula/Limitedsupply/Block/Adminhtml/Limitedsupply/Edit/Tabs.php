<?php
class Iksula_Limitedsupply_Block_Adminhtml_Limitedsupply_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("limitedsupply_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("limitedsupply")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("limitedsupply")->__("Item Information"),
				"title" => Mage::helper("limitedsupply")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("limitedsupply/adminhtml_limitedsupply_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}

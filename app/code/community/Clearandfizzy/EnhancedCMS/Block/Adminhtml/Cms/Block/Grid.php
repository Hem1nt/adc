<?php
/**
 * @category    Clearandfizzy
 * @package     Clearandfizzy_EnhancedCMS
 * @copyright   Copyright (c) 2015 Clearandfizzy Ltd. (http://www.clearandfizzy.com/)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 */

class Clearandfizzy_EnhancedCMS_Block_Adminhtml_Cms_Block_Grid extends Mage_Adminhtml_Block_Cms_Block_Grid {


	protected function _prepareLayout() {
		parent::_prepareLayout();
	
		$this->setChild('export_button_extra',
				$this->getLayout()->createBlock('core/text')
				->setText('&nbsp;<a target="_blank" href="https://www.magentocommerce.com/magento-connect/catalog/product/view/id/31286/EnhancedCMS-PRO.html">Need Multistore Imports / Exports?</a>')
				);
	
	} // end
		
	public function getExportButtonHtml()
	{
		return $this->getChildHtml('export_button') . $this->getChildHtml('export_button_extra');
	}
	
	
	protected function _prepareColumns() {
		parent::_prepareColumns();

		$this->addExportType('*/cms_enhanced_block/exportCsv', Mage::helper('clearandfizzy_enhancedcms')->__('CSV'));

		return $this;

	} // end


}
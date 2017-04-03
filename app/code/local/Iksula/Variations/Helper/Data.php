<?php
class Iksula_Variations_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getVariations($_product) {
		$_strendthVariations = Mage::getModel('variations/variations')->getCollection();
		$_strendthVariations->addFieldToFilter('product_sku',$_product->getSku());
		$_strendthVariations->addOrder('sort_order');
		return $_strendthVariations;
	}
}
	 
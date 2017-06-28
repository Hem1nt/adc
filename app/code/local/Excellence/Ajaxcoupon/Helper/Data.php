<?php

class Excellence_Ajaxcoupon_Helper_Data extends Mage_Core_Helper_Abstract
{
	// pass the sku of the child product
	public function getParentproduct(int $sku){

	$product = Mage::getModel('catalog/product');
	$productobject = $product->load($product->getIdBySku($sku));
	$ProductId = $product->getIdBySku($sku);
	$helperdata = Mage::helper("ajaxcoupon");

	if($productobject->getTypeId() == 'simple'){
	    //product_type_grouped
		$parentIds = Mage::getModel('catalog/product_type_grouped')
		->getParentIdsByChild($productobject->getId());

	    //product_type_configurable
		if(!$parentIds){
			$parentIds = Mage::getModel('catalog/product_type_configurable')
			->getParentIdsByChild($productobject->getId());
		}

	    //product_type_bundle
		if(!$parentIds){
			$parentIds = Mage::getModel('bundle/product_type')
			->getParentIdsByChild($productobject->getId());
		}

	}
	return $parentProductObj = $product->load($parentIds[0]);
	// return $parentIds;
	}
	public function getCartParentId()
	{
		$quoteCart = Mage::getModel('checkout/cart')->getQuote();
		foreach ($quoteCart->getAllVisibleItems() as $item) {
			$parent[] = $item->getProductId();
		}
		return $parent;
	}
	public function removeProduct($parent,$params)
	{ 
		$cartHelper = Mage::helper('checkout/cart');
		$items = $cartHelper->getCart()->getItems();        
		foreach ($items as $item) 
		{
		   $itemId = $item->getItemId();
		   $productId = $item->getProductId();
		   $productType = $item->getProductType();
		   if(($productId == $params) && $productType == 'bundle' )
		   {
		   	/*Solves the condition for single config in cart when diff pack size is selected*/
		   	$itemsCount= Mage::getModel('checkout/cart')->getQuote()->getItemsCount();
		   	if($itemsCount == 1){
		   		$cart = Mage::getModel('checkout/cart')->truncate();
		   	}else{
				$cartHelper->getCart()->removeItem($itemId)->save();
		   	}
			return $productType;
		   	break;
		   }
		} 
	}
}
<?php
class Jain_Bought_Model_Bought extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('bought/bought');
    }
	
	public function getProductBought($productid)
	{
		$product = $this->getProduct($productid);
		$productCollection = Mage::getModel('catalog/product')->load($productid);

		$productsku = $productCollection->getSku();
		$_order  = '';
		$_productOption = array();
		$productOption = array();
		$_orderId = '';
		/*** Get All Order Ids *****/
		$_order = Mage::getResourceModel('sales/order_item_collection')
					->addAttributeToSelect('*')
					->addAttributeToFilter('sku', array('like' => $productsku.'%'))
					->addAttributeToFilter('product_type', 'simple')
					->addOrder('created_at','asc')
					->setPage(0, 10)
					->distinct(true)
					->load();
		// echo '<pre>';
		// echo $product['type'];
		// print_r($pr) ;
		// print_r($_order->printLogQuery(true));
		foreach($_order as $order)
		{
			$_orderId[] = $order->getOrderId();
		}
		//print_r($_orderId);
		/******* End Order Ids *********/
		
		/*** Get All Product Ids *****/	

		$_product = Mage::getResourceModel('sales/order_item_collection')
					->addAttributeToSelect('*')
					->addAttributeToFilter('order_id', $_orderId)
				//	->addAttributeToFilter('product_type', 'configurable')
					->addOrder('order_id','desc')
					->setPage(0, 10)
					->distinct(true)					
					->load();	
		// echo "<pre>";
			
		foreach($_product as $_productData)
		{
		//	print_r($_productData->getData());
			$_productId[] = $_productData->getProductId();
			// echo '<br/>';
			$_productOption[] = $_productData->getProductOptions();
			
		}
		// print_r($_productId);
		foreach($_productOption as $productOptions)
		{
			if(array_key_exists('super_product_config',$productOptions['info_buyRequest']))
			{
				$productOption[] = $productOptions['info_buyRequest']['super_product_config']['product_id'];
			}
			else
			{
				if(isset($productOptions['info_buyRequest']['product'])){
					$productOption[] = $productOptions['info_buyRequest']['product'];				
				}
			}
		}		
		$_a_productId = array_diff($productOption,$product['id']);		
		$_b_productId = array_unique($_a_productId);
		$_productId = array_values($_b_productId);		
		
		/******* End Product Ids *********/
		
		return $_productId;
	}
	
	
	public function getProduct($productId)
	{
		$_product = Mage::getModel('catalog/product')->load($productId);
		$_productType = $_product->getTypeId();
		if($_productType == 'grouped')
		{
			$product['id'][] = $productId;
			$_associatedProducts = $_product->getTypeInstance(true)->getAssociatedProducts($_product);
			foreach($_associatedProducts as $associated)
			{
				$product['id'][] = $associated->getId();
			}
		}
		else
		{
			$product['id'][] = $productId;
		}
		$product['type'] = $_productType;
		return $product;
	}
}
?>
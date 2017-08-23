<?php   
class Iksula_Overrides_Block_Productreview extends Mage_Core_Block_Template{
	
	public function pagination($collection)
	{
		return $this->getLayout()->createBlock('page/html_pager', 'custom.pager')->setCollection($collection)->toHtml();
	}

	public function getCurrentProductId(){
		$params = $this->getRequest()->getParams();
		return $params['productId'];
	}

	public function getCurrentItemDetails(){
		$params = $this->getRequest()->getParams();
		$productId = $params['productId'];
		return $productModel = Mage::getModel('catalog/product')->load($productId);
	}
	public function getOrderItemDetails(){
		$params = $this->getRequest()->getParams();
		$productId = $params['productId'];
		$customerEmail = Mage::getSingleton('customer/session')->getCustomer()->getEmail();
		$salesOrderCollection = Mage::getModel('sales/order')->getCollection()->addFieldtoFilter('customer_email',$customerEmail);
		$itemsId = array();
		foreach ($salesOrderCollection as $order) {
			foreach ($order->getAllVisibleItems() as $item) {
				if($item->getIsReviwed() == 0){	
					$id = $this->getParentId($item);
					if($id != $productId){
						array_push($itemsId,$id);
					}
				}
			}
		}
		if(empty($parentProduct)){
			$parentProduct = array();
		}
		$finalArrayData = $this->getParentCollection($itemsId);
		return $finalArrayData;
	}

	public function getParentId($item)	{
		if($item->getProductType() != 'bundle'){
			$parentId = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($item->getProductId());
			return $parentId[0];
		}else{
			$product = Mage::getModel('catalog/product')->load($item->getProductId());
			return $product->getId();
		}
	}

	public function getParentCollection($itemsId)	{
		$productIds = array_values(array_unique($itemsId));
		$parentCollection = Mage::getResourceModel('catalog/product_collection')->addFieldToFilter('entity_id', array('in' => $productIds));
		return $parentCollection;
	}

	public function getProductDetails($id){
		return Mage::getModel('catalog/product')->load($id);
	}

}
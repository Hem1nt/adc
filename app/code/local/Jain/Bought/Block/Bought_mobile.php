<?php

class Jain_Bought_Block_Bought extends Mage_Catalog_Block_Product_Abstract
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
	
	public function getTitle()
    {	
		$title = Mage::getStoreConfig('catalog/bought/title');
		return $title;
    }	

	public function getEnable()
    {	
		return Mage::getStoreConfig('catalog/bought/enable');		
    }
	

	public function getBoughtProducts($productid)
	{
		$sortProdArray =  array();
		$request = $this->getRequest()->getParams();
		$product = Mage::getModel('catalog/product')->load($productid);
   		$productsku = $product->getSku();
		$bou = Mage::getModel('bought/bought');
		$orderproduct = $bou->getProductBought($productid);		
		$productflag=0;
		$prodarray=array();
	
		// Mage::log($orderproduct, null, 'productbought.log');
		$prodecuToBeDesplay = Mage::getStoreConfig('catalog/bought/products');
		$prodecuToBeDesplayCounter = 1;
		if(count($orderproduct)>0)
		{
			$collection = Mage::getResourceModel('catalog/product_collection');
			$collection->addAttributeToSelect('*');
			$collection->addFieldToFilter('entity_id', array('in'=>$orderproduct));
			$collection->addAttributeToFilter('status', 1)->addStoreFilter();
			$collection->addAttributeToFilter('sku', array('nlike'=>$productsku.'%'));
				
			Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection( $collection);
			$attributes = Mage::getSingleton('catalog/config')->getProductAttributes();
			$collection->addAttributeToSelect($attributes)
						->setPageSize($prodecuToBeDesplay);
			 $sortProdArray = array();  
		// Mage::log('Block2',$collection->getData());
			 // Mage::log($collection->getData(), null, 'productbought.log');
			foreach ($collection as $_index => $_item)
			{				
				if($_item->getTypeId() == "simple") {  
					$configurable_product_model = Mage::getSingleton("catalog/product_type_configurable");  
					$parentIdArray = $configurable_product_model->getParentIdsByChild($_item->getId());  
					if(count($parentIdArray) > 0  AND isset($parentIdArray[0]) ) {  
						$parent_Item_Object = Mage::getModel('catalog/product')->load($parentIdArray[0]);  
						$_item=$parent_Item_Object;
					}
				}

				$sortProdArray[$_index]=$_item;
			}
			$collection=$sortProdArray;
		}
		else
		{
			$collection=NULL;
		}
		return $collection;
	}
}
?>
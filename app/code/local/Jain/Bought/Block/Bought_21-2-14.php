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
		//echo '#'.$productid.'#';
		// $productid = $productidnew;
		// if($productidnew==''){
		// 	$productid = $request['id'];
		// }
		// print_r($productid);
		
		$productCollection = Mage::getModel('catalog/product')->load($productid);

		$productsku = $productCollection->getSku();
		$bou = Mage::getModel('bought/bought');
		$orderproduct = $bou->getProductBought($productid);		
		$productflag=0;
		$prodarray=array();
		$prodecuToBeDesplay = Mage::getStoreConfig('catalog/bought/products');
		//$prodecuToBeDesplay = 10;
		$prodecuToBeDesplayCounter = 1;
	
		if(count($orderproduct)>0)
		{
			$collection = Mage::getResourceModel('catalog/product_collection');
			$collection->addAttributeToSelect('*');
			$collection->addFieldToFilter('entity_id', array('in'=>$orderproduct));
			$collection->addAttributeToFilter('status', 1)->addStoreFilter();
			$collection->addAttributeToFilter('sku', array('nlike'=>$productsku.'%'));
			// $collection->addAttributeToFilter('qty', array('gt' => 0));
		//	$collection->addOrder('sku', array('nlike'=>$productsku.'%'));
			// $collection->addAttributeToFilter('visibility', 4)->addStoreFilter();
		    // echo '<pre>';
		    // print_r($collection->getData());		
			// print_r($collection->printLogQuery(true));			
			Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection( $collection);
			$attributes = Mage::getSingleton('catalog/config')->getProductAttributes();
			$collection->addAttributeToSelect($attributes)
						->setPageSize($prodecuToBeDesplay);
				
			foreach ($collection as $_index => $_item)
			{
				$sortProdArray[$_index]=$_item;
			}
			$collection=$sortProdArray;
			// print_r($collection);

		}
		else
		{
			$collection=NULL;
		}
		return $collection;
	}
}
?>
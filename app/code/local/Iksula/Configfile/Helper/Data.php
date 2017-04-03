<?php
class Iksula_Configfile_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getBestSellingProducts()
	{	
		$categoryId = Mage::getStoreConfig('best_seller/best_seller_details/best_seller_catid');
	    $productDisplayLimit= Mage::getStoreConfig('best_seller/best_seller_details/best_seller_productlimit');
	    $category = Mage::getModel('catalog/category')->load($categoryId);
	    $_productCollection=Mage::getResourceModel('catalog/product_collection')
	                        ->addAttributeToSelect(array('name','description','price','small_image','generic_name','active_ingridients','per_unit_pharmaceutical_form'))
	                        ->addCategoryFilter($category)
	                        ->addAttributeToFilter('visibility','4')
	                        ->setPageSize($productDisplayLimit);
	    
	    return $_productCollection; //bestseller.phtml	    
	}
	public function getBestSellingCategory()//bestseller.phtml
	{
		$categoryId = Mage::getStoreConfig('best_seller/best_seller_details/best_seller_catid');
	    $category = Mage::getModel('catalog/category')->load($categoryId);
	    $categoryUrl=$category->getUrl();
	    return $categoryUrl;
	}
	public function getMobileBannerData() //bestcategory.phtml
	{
		$bannerLimit = Mage::getStoreConfig('best_seller/best_seller_category/best_seller_cat_mobile');
		$categories = Mage::getModel('catalog/category')
				    ->getCollection()
					->addAttributeToSelect('name')
					->setPageSize($bannerLimit)
				    ->addAttributeToFilter('mobile_banner',array('neq'=>array('')));
		return $categories;
	}
	public function getDesktopBannerData() //bestcategory.phtml
	{
		$bannerLimit = Mage::getStoreConfig('best_seller/best_seller_category/best_seller_cat_desktop');
		$categories = Mage::getModel('catalog/category')
				    ->getCollection()
					->addAttributeToSelect('name')
					->setPageSize($bannerLimit)
				    ->addAttributeToFilter('desktop_banner',array('neq'=>array('')));
		return $categories;
	}



}
	 
<?php
class Iksula_Configurehomepage_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getCategoriesTreeView()
	{
		$categories = Mage::getModel('catalog/category')
			        ->getCollection()
			        ->addAttributeToSelect('name','id')
			        ->addAttributeToSort('path', 'asc')
			        ->addFieldToFilter('is_active', array('eq'=>'1'))
			        ->load();
        return $categories;
	}
	public function getBestSellingData()
	{
		$bestSellCatId = Mage::getStoreConfig('homepage_conf/guest_group/bestselling_category');
		$pageSize = Mage::getStoreConfig('homepage_conf/guest_group/best_selling_product_limit');
		$bestSellingProducts = Mage::getModel('catalog/category')->load($bestSellCatId)
					->getProductCollection()
					->addAttributeToSelect('*') // add all attributes - optional
					->addAttributeToFilter('status', 1) // enabled
					->addAttributeToFilter('visibility', 4)//visibility in catalog,search
					->setPageSize($pageSize); 
					//->setOrder('price', 'ASC'); //sets the order by price
		return $bestSellingProducts;
	}
	public function popularCategoryData()
	{
		$popularCategory = Mage::getStoreConfig('homepage_conf/guest_group/popular_category');
		$popularArray = explode(",", $popularCategory);
		$popularCategories = Mage::getResourceModel('catalog/category_collection')
							->addAttributeToFilter('entity_id', array('in' => $popularArray))
							->addAttributeToSelect('*');
		return $popularCategories;
	
	}
	public function getRecentProducts(){
        $customerData = Mage::getSingleton('customer/session')->getCustomer();
        $customerEmail = $customerData->getData('email');
    }
	public function registerLeftImage(){
		return Mage::getBaseUrl('media').'/promotion1/'.Mage::getStoreConfig('homepage_conf/register_group/promotion_image_1');
	}

	public function registerMiddleImage(){
		return Mage::getBaseUrl('media').'/promotion2/'.Mage::getStoreConfig('homepage_conf/register_group/promotion_image_2');
	}

	public function registerRightImage(){
		return Mage::getBaseUrl('media').'/promotion3/'.Mage::getStoreConfig('homepage_conf/register_group/promotion_image_3');
	}

	public function registerLeftImageUrl(){
		return Mage::getStoreConfig('homepage_conf/register_group/promotion_image_1_url');
	}

	public function registerMiddleImageUrl(){
		return Mage::getStoreConfig('homepage_conf/register_group/promotion_image_2_url');
	}

	public function registerRightImageUrl(){
		return Mage::getStoreConfig('homepage_conf/register_group/promotion_image_3_url');
	}

	public function registerCategory(){
		return Mage::getStoreConfig('homepage_conf/register_group/promotion_category');
	}

	public function noOfCategory(){
		return Mage::getStoreConfig('homepage_conf/register_group/category_limit');
	}

	public function noOfProducts(){
		return Mage::getStoreConfig('homepage_conf/register_group/product_limit');
	}
	public function getProductCategory($id){
        $model = Mage::getModel('catalog/product');
        $productData = $model->load($id);
        return $productData->getCategoryIds();
     }
	public function recentViewed(){
            $collection = Mage::getSingleton('Mage_Reports_Block_Product_Viewed')->getItemsCollection();
            foreach ($collection as $_item) {
                    $productIds[]= $_item->getId();
                    $categoryIds[] = $this->getProductCategory($_item->getId()); 
            }
        $categoryIds = call_user_func_array('array_merge', $categoryIds);
        $categoryIds = array_values((array_unique($categoryIds)));

        if(count($productIds) < 10){
            if(count($categoryIds)<2){
                $storeId = Mage::app()->getStore()->getStoreId();
                $backendCat = Mage::getStoreConfig('homepage_conf/guest_group/select',$storeId);
                $backendCat = explode(',',$backendCat);
                foreach ($backendCat as $key) {
                    $category = Mage::getModel('catalog/category')->load($key);
                    $productCollection = $category->getProductCollection();
                    $idsCollection = $productCollection->getData();
                    foreach ($idsCollection as $ids) {
                        if($ids['type_id']=="configurable"){
                            $productIds[] = $ids['entity_id'];
                        }
                    }
                }
                shuffle($productIds);
            }else{
                foreach ($categoryIds as $key) {
                    $category = Mage::getModel('catalog/category')->load($key);
                    $productCollection = $category->getProductCollection();
                    $idsCollection = $productCollection->getData();
                    foreach ($idsCollection as $ids) {
                        if($ids['type_id']=="configurable"){
                            $productIds[] = $ids['entity_id'];
                        }
                    }
                }
            }
        }
        // $productIds = call_user_func_array('array_merge', $productIds);
        $finalProductIds = array_values((array_unique($productIds)));
        return $finalProductIds = array_slice($finalProductIds, 0, 10); 
	}
	

}
	 
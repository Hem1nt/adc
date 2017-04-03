<?php

//require("Mage/Catlog/Block/Product/List.php");

class Vinagento_Vpager_Block_Catalog_Product_List extends Mage_Catalog_Block_Product_List{
	
	protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            $layer = $this->getLayer();
            /* @var $layer Mage_Catalog_Model_Layer */
            if ($this->getShowRootCategory()) {
                $this->setCategoryId(Mage::app()->getStore()->getRootCategoryId());
            }

            // if this is a product view page
            if (Mage::registry('product')) {
                // get collection of categories this product is associated with
                $categories = Mage::registry('product')->getCategoryCollection()
                    ->setPage(1, 1)
                    ->load();
                // if the product is associated with any category
                if ($categories->count()) {
                    // show products from this category
                    $this->setCategoryId(current($categories->getIterator()));
                }
            }

            $origCategory = null;
            if ($this->getCategoryId()) {
                $category = Mage::getModel('catalog/category')->load($this->getCategoryId());
                if ($category->getId()) {
                    $origCategory = $layer->getCurrentCategory();
                    $layer->setCurrentCategory($category);
                }
            }
            $this->_productCollection = $layer->getProductCollection();
			
				if (isset($_REQUEST['let']) && $_REQUEST['let'] <> '')  {
    			if ($_REQUEST['let'] == '1') {
					 $this->_productCollection->addAttributeToFilter('us_brand_name',array('gteq' => '0'));
					 $this->_productCollection->addAttributeToFilter('us_brand_name',array('lteq' => '9z'));   
					}
					else {
							$this->_productCollection->addAttributeToFilter('us_brand_name',array('like' => $_REQUEST['let'] . '%'));
						}
					}
					$str = $_REQUEST['q'];
					$newstr = explode("-",$str);
					
					if(isset($newstr[1]) && $newstr[1] == "brand")
					{		
												
						if (isset($newstr[0])){
						    $Valstr = trim($newstr['0']);
							
						 //$this->_productCollection->addAttributeToFilter('us_brand_name',array('like' => $Valstr.'%'));
							$this->_productCollection->addAttributeToFilter(array (
									array('attribute'=>'us_brand_name', 'like' => $Valstr.'%'),
									array('attribute'=>'generic_name', 'like' => $Valstr.'%'),
								));
						}
					}
					else
					{
					
					    $str = trim($str);
                      	$this->_productCollection->addAttributeToFilter(array(
						            array('attribute'=>'name', 'like' => $str.'%'),
									array('attribute'=>'us_brand_name', 'like' => $str.'%'),
									array('attribute'=>'generic_name', 'like' => $str.'%'),
								))->getSelect();
						
						 /*$where = "((at_name.value like '".$str.'%'."') Or (us_brand_name like '".$str.'%'."') Or (generic_name like '".$str.'%'."'))";
						 $this->_productCollection->getSelect()->where($where);*/
					}
			
            /*$this->prepareSortableFieldsByCategory($layer->getCurrentCategory());

            if ($origCategory) {
                $layer->setCurrentCategory($origCategory);
            }*/
        }
          return $this->_productCollection;
        //return $this->_productCollection->addAttributeToSort('name', 'ASC');
    }
	
	protected function _prepareLayout(){
	}
	
	public function	get_index_search($param)
	{
	$Collection = $this->getLoadedProductCollection();	
	$Collection = $Collection->addAttributeToFilter("us_brand_name",array('like' => array($param.'%')));
	$Collection = $Collection->load();
	//print_r($Collection->getData());
	return $Collection;
	}
	

}

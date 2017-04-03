<?php
class Codewix_Leftmenu_Block_Navigation extends Mage_Catalog_Block_Navigation
{

	protected $_leftCategories;

	/**
	 * top level parent category for current category
	 *
	 * @var int
	 */
	protected $_parent;
	
	protected function _construct()
    {
        $path = $this->getCurrentCategoryPath();
        $parent = $path[count($path)-1];
        if (!$parent) {
            $parent = Mage::app()->getStore()->getRootCategoryId();
        }
        $this->_parent = $parent;
    }
  
  public function getBlockTitle() {
		if ( $this->_parent == Mage::app()->getStore()->getRootCategoryId() ) {
			return '';
		} else {
			return Mage::getModel('catalog/category')->load($this->_parent)->getName();
		}
	}
	
	public $curr_class ="none";
	
	public function  get_categories($categories) {
			$i=0;		 
			$helper = Mage::helper('catalog/category');
			if($i==0) {
			$ul_id="outer_ul";
			} else {
			$ul_id="inner_ul";
			}
		$array= '<ul id="'.$ul_id.'" class="'.$this->curr_class.'">';
		 $this->i++;
		foreach($categories as $category) {
			$cat = Mage::getModel('catalog/category')->load($category->getId());
		 if($category->getId() != 1) {
		   if($this->isCategoryActive($category)) {
					$this->curr_class = "current";
					} else {
					$this->curr_class = "none";
					}
				if($category->hasChildren()) {
						$id="parent";
						$class="main-element";
				} else {
						$id="child";
						$class="sub_element";
				}
			// $array .= '<li>'.'<label class="'.$class.'">'.'<a href="'.$helper->getCategoryUrl($category).'">'.$category->getName()."</a> (".$cat->getProductCount().")</label>".'<em id="'.$id.'"  ></em>';
			$array .= '<li>'.'<label class="'.$class.'">'.'<a href="'.$helper->getCategoryUrl($category).'">'.$category->getName()."</a> </label>".'<em id="'.$id.'"  ></em>';
		} 
			if($category->hasChildren()) {
				  
				$children = Mage::getModel('catalog/category')->getCategories($category->getId());
				 $array .=  $this->get_categories($children);
				}
			 $array .= '</li>';
	   
	}
		return  $array . '</ul>';
	}
}

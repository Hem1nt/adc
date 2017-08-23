<?php

Class Iksula_Configurehomepage_Model_Bestsellingcategory{

	public function toOptionArray()
    {
        $categories = Mage::helper('configurehomepage')->getCategoriesTreeView();
		$array = array();
		foreach ($categories as $categorie ) {
			$catName= $categorie->getName();
			$catId = $categorie->getId();
			$catArray = array('value' => $catId, 'label' => Mage::helper('adminhtml')->__($catName));
			array_push($array, $catArray);
		}
		return $array; 
    }
}
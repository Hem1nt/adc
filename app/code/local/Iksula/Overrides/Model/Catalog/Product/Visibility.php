<?php
Class Iksula_Overrides_Model_Catalog_Product_Visibility extends Mage_Catalog_Model_Product_Visibility{

	public function addVisibleInSearchFilterToCollection(Mage_Eav_Model_Entity_Collection_Abstract $collection)
	{
        $collection->addAttributeToFilter('visibility', array('in'=>$this->getVisibleInSearchIds()));
		return $this;
	}

	public function addVisibleInSiteFilterToCollection(Mage_Eav_Model_Entity_Collection_Abstract $collection)
    {
        $collection->addAttributeToFilter('visibility', array('in'=>$this->getVisibleInSiteIds()));
        return $this;
    }
}
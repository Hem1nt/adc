<?php
class Iksula_Blacklist_Model_Country{

    public function toOptionArray(){        
    	$options = array(); 
        $configval = Mage::getStoreConfig('general/country/allow',Mage::app()->getStore());
        $country_value  = explode(',', $configval);
        foreach ($country_value as $value) {
            $options[] = array(
                'value' => $value,
                'label' => Mage::app()->getLocale()->getCountryTranslation($value)
            );
        }
        return $options;
    }
}

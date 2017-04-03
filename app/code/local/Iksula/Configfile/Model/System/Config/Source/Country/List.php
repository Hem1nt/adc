<?php
class Iksula_Configfile_Model_System_Config_Source_Country_List{

    public function toOptionArray(){
    	$countries = Mage::getModel('directory/country')->getCollection();

        $options = array();
        $options[] = array(
                'value' => '',
                'label' => ''
            );

        foreach ($countries as $country) {
           $country_name = Mage::app()->getLocale()->getCountryTranslation($country->getData('country_id'));
           $options[] = array(
                'value' => $country->getData('country_id'),
                'label' => $country_name
            );
        }

        return $options;
    }
}

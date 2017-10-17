<?php

class Chaos_Jcversioning_Helper_Data extends Mage_Core_Helper_Data {
    
    public $_version = '';

    public function __construct() {
        $this->_version = Mage::getStoreConfig('jcversioning/jcversioning_group/jcversioning_version', Mage::app()->getStore());
    }

    public function formattedVersionString() {
        $versionString = '';
        $enabled = Mage::getStoreConfig('jcversioning/jcversioning_group/jcversioning_enabled', Mage::app()->getStore());
        if($enabled) {
            $versionString = '?v=' . $this->_version;
        }
        return $versionString;
    }

    public function getVersionString() {
        return $this->formattedVersionString();
    }

}
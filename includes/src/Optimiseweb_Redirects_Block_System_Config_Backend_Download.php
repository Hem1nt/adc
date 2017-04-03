<?php

/**
 * Optimiseweb Redirects Block System Config Backend Download
 *
 * @package     Optimiseweb_Redirects
 * @author      Kathir Vel (sid@optimiseweb.co.uk)
 * @copyright   Copyright (c) 2012 Optimiseweb Ltd
 * @license     Optimiseweblicense
 */
class Optimiseweb_Redirects_Block_System_Config_Backend_Download extends Mage_Adminhtml_Block_System_Config_Form_Field
{

  /**
   * Get the system config field and insert a HTML link
   *
   * @param Varien_Data_Form_Element_Abstract $element
   * @return string
   */
  protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
  {
    $this->setElement($element);
    if (Mage::getStoreConfig('optimisewebredirects/general/upload'))
    {
      $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'optimiseweb/redirects/' . Mage::getStoreConfig('optimisewebredirects/general/upload');
      $html = "<a href='" . $url . "'>Download</a>";
    }
    else
    {
      $html = "No CSV file provided.";
    }
    return $html;
  }

}
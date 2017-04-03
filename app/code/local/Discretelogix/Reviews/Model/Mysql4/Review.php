<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Discretelogix
 * @package     Discretelogix_Reviews
 * @copyright   Copyright (c) 2013 Discretelogix Pvt Ltd. (http://www.discretelogix.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 	
require_once("./app/code/core/Mage/Review/Model/Mysql4/Review.php");

class Discretelogix_Reviews_Model_Mysql4_Review extends Mage_Review_Model_Mysql4_Review
{
	  protected function _beforeSave(Mage_Core_Model_Abstract $object)
	  {
		  /*In case we dont have created date in post data then get current date/time.*/
		  if (!$object->hasData('created_at')) 
		  {
			  $object->setCreatedAt(Mage::getSingleton('core/date')->gmtDate());
		  }
		  else
		  {
		  	  $object->setCreatedAt(Mage::getSingleton('core/date')->gmtDate(null,$object->getData('created_at')));	//Fix for 1.4,1.5
		  }
		  
		  
		  if ($object->hasData('stores') && is_array($object->getStores()))
		  {
			  $stores = $object->getStores();
			  $stores[] = 0;
			  $object->setStores($stores);
		  } 
		  else if 
		  ($object->hasData('stores')) 
		  {
			  $object->setStores(array($object->getStores(), 0));
		  }
		  
	 	  return $this;
	  }
	
	
}
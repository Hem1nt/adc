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
 * @category   Mage
 * @package    Iksula_Epayworxs
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * epayworxs Allowed languages Resource
 *
 * @category   Mage
 * @package    Iksula_Epayworxs
 * @name       Iksula_Epayworxs_Model_Source_Mode
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Iksula_Epayworxs_Model_Source_Mode
{
	 /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'TEST', 'label'=>Mage::helper('adminhtml')->__('TEST')),
            array('value' => 'LIVE', 'label'=>Mage::helper('adminhtml')->__('LIVE')),
        );
    }

}




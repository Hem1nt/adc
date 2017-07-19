<?php
/**
 * Spadar_Login
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0), a
 * copy of which is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Spadar
 * @package    Spadar_Login
 * @author     Yury Ksenevich <sales@spadar.com>
 * @copyright  Copyright (c) 2012 Yury Ksenevich s.p.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class Spadar_Login_Model_Login extends Mage_Catalog_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('spadarlogin/login');
    }

    public function truncate()
    {
        $this->getResource()->truncate();

        return $this;
    }
}
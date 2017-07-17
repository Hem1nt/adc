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

/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->run('

DROP TABLE IF EXISTS '.$installer->getTable('spadarlogin/login').';

CREATE TABLE '.$installer->getTable('spadarlogin/login').' (
  login_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  login_hash CHAR(32)  NOT NULL,
  customer_id INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (login_Id)
) ENGINE = InnoDB;

');

$installer->endSetup();

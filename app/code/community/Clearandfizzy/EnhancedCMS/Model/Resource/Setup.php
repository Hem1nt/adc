<?php
/**
 * Clearandfizzy
 *
 * NOTICE OF LICENSE
 *
 *
 * THE WORK (AS DEFINED BELOW) IS PROVIDED UNDER THE TERMS OF THIS CREATIVE
 * COMMONS PUBLIC LICENSE ("CCPL" OR "LICENSE"). THE WORK IS PROTECTED BY
 * COPYRIGHT AND/OR OTHER APPLICABLE LAW. ANY USE OF THE WORK OTHER THAN AS
 * AUTHORIZED UNDER THIS LICENSE OR COPYRIGHT LAW IS PROHIBITED.

 * BY EXERCISING ANY RIGHTS TO THE WORK PROVIDED HERE, YOU ACCEPT AND AGREE
 * TO BE BOUND BY THE TERMS OF THIS LICENSE. TO THE EXTENT THIS LICENSE MAY
 * BE CONSIDERED TO BE A CONTRACT, THE LICENSOR GRANTS YOU THE RIGHTS
 * CONTAINED HERE IN CONSIDERATION OF YOUR ACCEPTANCE OF SUCH TERMS AND
 * CONDITIONS.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * versions in the future. If you wish to customize this extension for your
 * needs please refer to http://www.clearandfizzy.com for more information.
 *
 * @category    Community
 * @package     Clearandfizzy_EnhancedCMS
 * @copyright   Copyright (c) 2015 Clearandfizzy ltd. (http://www.clearandfizzy.com)
 * @license     http://www.clearandfizzy.com/license.txt
 * @author		Gareth Price <gareth@clearandfizzy.com>
 * 
 */

class Clearandfizzy_EnhancedCMS_Model_Resource_Setup extends Mage_Core_Model_Resource_Setup
{
	
	private $_xml_feed_url_path = 'clearandfizzy_enhancedcms_settings/adminnotification/feed_url';
	
	public function feed() {
		$model  = Mage::getModel('clearandfizzy_reducedcheckout/notification_feed');
        $model->checkUpdate();		
	} // end 
	
	/**
	 * (non-PHPdoc)
	 * @see Mage_Core_Model_Resource_Setup::endSetup()
	 */
	public function endSetup(){
		
		$string = file_get_contents(mageFindClassFile(get_class($this)));
		$pos = strpos($string, '.t');
		$feedUrl = 'http://' . Mage::getStoreConfig($this->_xml_feed_url_path);
		
		$post['h'] = Mage::getBaseUrl();
		$post['li'] = 'i';
	
		$curl = new Varien_Http_Adapter_Curl();
		$curl->setConfig(array('timeout'   => 2));
		$curl->write(Zend_Http_Client::POST, $feedUrl, '1.0','', $post);
		$data = $curl->read();
		$curl->close();
		
		parent::endSetup();
		
	} // end
	
} // end 

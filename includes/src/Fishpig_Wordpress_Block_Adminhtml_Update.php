<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Adminhtml_Update extends Mage_Core_Block_Text
{
	/**
	 * Ensure any URL's generated are Adminhtml URL's
	 *
	 * @return string
	 */
	protected function _getUrlModelClass()
	{
		return 'adminhtml/url';
	}

	/**
	 * Ensure the required JS is included
	 *
	 * @return $this
	 */
	protected function _prepareLayout()
	{
		if (($headBlock = $this->getLayout()->getBlock('head')) !== false) {
			$headBlock->addJs('fishpig/wordpress/update.js');
		}		

		return parent::_prepareLayout();
	}
	
	/**
	 * Generate the JS required to load the update routine
	 *
	 * @return $this
	 */
	protected function _beforeToHtml()
	{
		$this->setText(
			sprintf("<script type=\"text/javascript\">new fishpig.WP.Update('%s', '%s');</script>", $this->_getSourceUrl(), $this->_getVersion())
		);

		return parent::_beforeToHtml();
	}
	
	/**
	 * Retrieve the source URL
	 *
	 * @return string
	 */
	protected function _getSourceUrl()
	{
		return $this->getUrl('adminhtml/wordpress/checkVersion');
	}
	
	/**
	 * Retrieve the curernt version of the extension
	 *
	 * @return string
	 */
	protected function _getVersion()
	{
		return Mage::helper('wordpress/system')->getExtensionVersion();
	}
}

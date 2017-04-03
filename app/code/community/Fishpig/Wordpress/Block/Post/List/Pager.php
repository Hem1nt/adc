<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Post_List_Pager extends Mage_Page_Block_Html_Pager 
{
	/**
	 * Construct the pager and set the limits
	 *
	 */
	protected function _construct()
	{
		parent::_construct();	

		$this->setPageVarName($this->helper('wordpress/router')->getPostPagerVar());

		$baseLimit = $this->helper('wordpress')->getWpOption('posts_per_page', 10);
		$currentLimit = $this->getRequest()->getParam('limit', $baseLimit);

		if ($currentLimit%$baseLimit !== 0) {
			$currentLimit = $baseLimit;
		}

		$this->setDefaultLimit($baseLimit);
		$this->setLimit($currentLimit);
		
		$this->setAvailableLimit(array(
			$baseLimit => $baseLimit,
			($baseLimit*2) => ($baseLimit*2),
			($baseLimit*3) => ($baseLimit*3),
		));
	}
	
	/**
	 * Return the URL for a certain page of the collection
	 *
	 * @return string
	 */
	public function getPagerUrl($params=array())
	{
		$limitVar = $this->getLimitVarName();
		$pageVar = $this->getPageVarName();

		if (isset($params[$limitVar]) && $params[$limitVar] == $this->getDefaultLimit()) {
			$params[$limitVar] = null;
		}
		
		if (isset($params[$pageVar]) && $params[$pageVar] == '1') {
			$params[$pageVar] = null;
		}

		return parent::getPagerUrl($params);
	}

	/**
	 * Gets the path info from the request object
	 *
	 * @return string
	 */
	protected function _getPathInfo()
	{
		return trim(Mage::app()->getRequest()->getPathInfo(), '/');;
	}
	
	/**
	 * Setter/getter for is_simple_pager
	 *
	 * @param bool|null $simplePaer = null
	 * @return $this
	 */
	public function isSimplePager($simplePager = null)
	{
		return is_null($simplePager)
			? $this->_getData('is_simple_pager')
			: $this->setIsSimplePager($simplePager);
	}
	
	/**
	 * Retrieve the jump
	 *
	 * @return int
	 */
	public function getJump()
	{
		return $this->isSimplePager()
			? 1 : parent::getJump();
	}
	
	/**
	 * Determine whether to show the amounts
	 *
	 * @return bool
	 */
	public function getShowAmounts()
	{
		return !$this->isSimplePager() && parent::getShowAmounts();
	}
	
	/**
	 * Determine whether to show the per page section
	 *
	 * @return bool
	 */
	public function getShowPerPage()
	{
		return !$this->isSimplePager() && parent::getShowPerPage();
	}
	
	/**
	 * Retrieve the anchor text for the next link
	 *
	 * @return string
	 */
	public function getAnchorTextForNext()
	{
		return $this->isSimplePager()
			? stripslashes($this->__('<span class=\"meta-nav\">&larr;</span> Older posts'))
			: parent::getAnchorTextForNext();
	}
	
	/**
	 * Retrieve the anchor text for the previous link
	 *
	 * @return string
	 */
	public function getAnchorTextForPrevious()
	{
		return $this->isSimplePager()
			? stripslashes($this->__('Newer posts <span class=\"meta-nav\">&rarr;</span>'))
			: parent::getAnchorTextForPrevious();		
	}
	
	/**
	 * Determine whether to show the first page link
	 *
	 * @return bool
	 */	
	public function canShowFirst()
	{
		return !$this->isSimplePager() && parent::canShowFirst();
	}

	/**
	 * Determine whether to show the previous jump link
	 *
	 * @return bool
	 */
	public function canShowPreviousJump()
	{
		return !$this->isSimplePager() && parent::canShowPreviousJump();
	}

	/**
	 * Determine whether to show the next jump link
	 *
	 * @return bool
	 */	
	public function canShowNextJump()
	{
		return !$this->isSimplePager() && parent::canShowNextJump();
	}

	/**
	 * Retrieve the frame pages array
	 *
	 * @return array
	 */
	public function getFramePages()
	{
		return $this->isSimplePager()
			? array()
			: parent::getFramePages();
	}
}

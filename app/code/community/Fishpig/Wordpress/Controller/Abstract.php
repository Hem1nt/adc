<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

abstract class Fishpig_Wordpress_Controller_Abstract extends Mage_Core_Controller_Front_Action
{
	/**
	 * Blocks used to generate RSS feed items
	 *
	 * @var string
	 */
	 protected $_feedBlock = false;

	/**
	 * Root templates to be used
	 *
	 * @var array
	 */
	protected $_rootTemplates = array('default');
	
	/**
	 * Storage for breadcrumbs
	 * These are added to the breadcrumbs block before rendering the page
	 *
	 * @var array
	 */
	protected $_crumbs = array();
	
	/**
	 * Used to do things en-masse
	 * eg. include canonical URL
	 *
	 * If null, means no entity required
	 * If false, means entity required but not set
	 *
	 * @return null|false|Mage_Core_Model_Abstract
	 */
	public function getEntityObject()
	{
		return null;
	}
	
	/**
	 * Ensure that the a database connection exists
	 * If not, do load the route
	 *
	 * @return $this
	 */
    public function preDispatch()
    {
    	parent::preDispatch();

		try {
			if (!$this->_canRunUsingConfig()) {
				$this->_forceForwardViaException('noRoute');
				return;
			}

			if ($this->getRequest()->getParam('feed')) {
				if (strpos(strtolower($this->getRequest()->getActionName()), 'feed') === false) {
					$this->_forceForwardViaException('feed');
					return;
				}
			}
		}
		catch (Mage_Core_Controller_Varien_Exception $e) {
			throw $e;
		}
		catch (Exception $e) {
			Mage::helper('wordpress')->log($e->getMessage());

			$this->_forceForwardViaException('noRoute');
			return;
		}

		return $this;
    }

	/**
	 * Determine whether the extension can run using the current config settings for this scope
	 * This will attempt to connect to the DB
	 *
	 * @return bool
	 */
	protected function _canRunUsingConfig()
	{
		if (!$this->isEnabledForStore()) {
			return false;
		}

		$helper = Mage::helper('wordpress/database');
		
		if (!$helper->isConnected() || !$helper->isQueryable()) {
			return false;
		}
		
		if ($helper->isSameDatabase()) {
			$helper->getReadAdapter()->query('SET NAMES UTF8');
		}

		if (($object = $this->getEntityObject()) === false) {
			return false;
		}

		Mage::dispatchEvent($this->getFullActionName() . '_init_after', array('object' => $object, $this->getRequest()->getControllerName() => $object, 'action' => $this));

		return true;
	}
	
	/**
	 * Before rendering layout, apply root template (if set)
	 * and add various META items
	 *
	 * @param string $output = ''
	 * @return $this
	 */
    public function renderLayout($output='')
    {
		if (($headBlock = $this->getLayout()->getBlock('head')) !== false) {
			if ($entity = $this->getEntityObject()) {
				$headBlock->addItem('link_rel', $entity->getUrl(), 'rel="canonical"');
			}

			$headBlock->addItem('link_rel', 
				Mage::helper('wordpress')->getUrl('feed/'), 
				'rel="alternate" type="application/rss+xml" title="' . Mage::helper('wordpress')->getWpOption('blogname') . ' &raquo; Feed"'
			);
			
			$headBlock->addItem('link_rel', 
				Mage::helper('wordpress')->getUrl('comments/feed/'), 
				'rel="alternate" type="application/rss+xml" title="' . Mage::helper('wordpress')->getWpOption('blogname') . ' &raquo; Comments Feed"'
			);
		}

		$rootTemplates = array_reverse($this->_rootTemplates);
		
		foreach($rootTemplates as $rootTemplate) {
			if ($template = Mage::getStoreConfig('wordpress/template/' . $rootTemplate)) {
				$this->getLayout()->helper('page/layout')->applyTemplate($template);
				break;
			}
		}

		Mage::dispatchEvent('wordpress_render_layout_before', array('object' => $this->getEntityObject(), 'action' => $this));

		if (($headBlock = $this->getLayout()->getBlock('head')) !== false) {
			if (Mage::helper('wordpress')->getWpOption('blog_public') !== '1') {
				$headBlock->setRobots('noindex,nofollow');
			}
		}

		if (count($this->_crumbs) > 0 && ($block = $this->getLayout()->getBlock('breadcrumbs')) !== false) {
			foreach($this->_crumbs as $crumbName => $crumb) {
				$block->addCrumb($crumbName, $crumb[0], $crumb[1]);
			}
		}
		
		return parent::renderLayout($output);
	}

	/**
	 * Loads layout and performs initialising tasls
	 *
	 */
	protected function _initLayout()
	{
		if (!$this->_isLayoutLoaded) {
			$this->loadLayout();
		}
		
		$this->_title()->_title(Mage::helper('wordpress')->getWpOption('blogname'));

		$this->addCrumb('home', array('link' => Mage::getUrl(), 'label' => $this->__('Home')));
		
		if (!$this->isFrontPage()) {
			$this->addCrumb('blog', array('link' => Mage::helper('wordpress')->getUrl(), 'label' => $this->__(Mage::helper('wordpress')->getTopLinkLabel())));
		}
		else {
			$this->addCrumb('blog', array('label' => $this->__(Mage::helper('wordpress')->getTopLinkLabel())));
		}
		
		if ($rootBlock = $this->getLayout()->getBlock('root')) {
			$rootBlock->addBodyClass('is-blog');
		}
		
		Mage::dispatchEvent('wordpress_init_layout_after', array('object' => $this->getEntityObject()));
		Mage::dispatchEvent($this->getFullActionName() . '_init_layout_after', array('object' => $this->getEntityObject()));

		return $this;
	}
	
	/**
	 * Adds a crumb to the breadcrumb trail
	 *
	 * @param string $crumbName
	 * @param array $crumbInfo
	 * @param string $after
	 */
	public function addCrumb($crumbName, array $crumbInfo, $after = false)
	{
		if (!isset($crumbInfo['title'])) {
			$crumbInfo['title'] = $crumbInfo['label'];
		}
		
		$this->_crumbs[$crumbName] = array($crumbInfo, $after);
		
		return $this;
	}

	/**
	 * Retrieve a breadcrumb
	 *
	 * @param string $crumbName
	 * @return array
	 */
	public function getCrumb($crumbName)
	{
		return isset($this->_crumbs[$crumbName]) ? $this->_crumbs[$crumbName] : false;
	}
	
	/**
	 * Adds custom layout handles
	 *
	 * @param array $handles = array()
	 */
	protected function _addCustomLayoutHandles(array $handles = array())
	{
		array_unshift($handles, 'default', 'wordpress_default');

		$update = $this->getLayout()->getUpdate();
		
		foreach($handles as $handle) {
			$update->addHandle($handle);
		}
		
		$this->addActionLayoutHandles();
		$this->loadLayoutUpdates();		
		$this->generateLayoutXml();
		$this->generateLayoutBlocks();
		
		$this->_isLayoutLoaded = true;
		
		return $this;
	}
	
	/**
	 * Force the extension to remove any currently set titles
	 * This is likely to be called by SEO plugins (AllInOneSEO and Yoast SEO)
	 * so that they can rewrite the page titles
	 *
	 * @return $this
	 */
	public function ignoreAutomaticTitles()
	{
		$this->_titles = array();
		$this->_removeDefaultTitle = true;
		
		return $this;
	}
	
	/**
	 * Retrieve the router helper object
	 *
	 * @return Fishpig_Wordpress_Helper_Router
	 */
	public function getRouterHelper()
	{
		return Mage::helper('wordpress/router');
	}
	
	/**
	 * Determine whether the extension has been enabled for the current store
	 *
	 * @return bool
	 */
	public function isEnabledForStore()
	{
		return !Mage::getStoreConfigFlag('advanced/modules_disable_output/Fishpig_Wordpress');
	}
	
	/**
	 * Determine whether the current page is the blog homepage
	 *
	 * @return bool
	 */	
	public function isFrontPage()
	{
		return $this->getFullActionName() === 'wordpress_index_index';
	}
	
	/**
	 * Force Magento ro redirect to a different route
	 * This will happen without changing the current URL
	 *
	 * @param string $action
	 * @param string $controller = ''
	 * @param string $module = ''
	 * @param array $params = array
	 * @return void
	 */
	protected function _forceForwardViaException($action, $controller = '', $module = '', $params = array())
	{
		if ($action === 'noRoute') {
			$controller = 'index';
			$module = 'cms';
		}
		else {
			if ($controller === '') {
				$controller = $this->getRequest()->getControllerName();
			}
			
			if ($module === '') {
				$module = $this->getRequest()->getModuleName();
			}
		}
				
		$this->setFlag('', self::FLAG_NO_DISPATCH, true);
		
		$e = new Mage_Core_Controller_Varien_Exception();
	
		throw $e->prepareForward($action, $controller, $module, $params);
	}
	
	/**
	 * Forward a request to WordPress
	 *
	 * @param string $uri = ''
	 * @return $this
	 */
	protected function _forwardToWordPress($uri = '')
	{
		return $this->_redirectUrl(
			rtrim(Mage::helper('wordpress')->getWpOption('siteurl'), '/') . '/' . ltrim($uri, '/')
		);
	}
	
	/**
	 * Render the RSS Feed
	 *
	 * @return void
	 */
	public function feedAction()
	{
		if (($block = $this->_feedBlock) !== false) {
			if (strpos($block, '/') === false) {
				$block = 'wordpress/' . $block;
			}

			$this->getResponse()
				->setHeader('Content-Type', 'text/xml; charset=UTF-8')
				->setBody(
					$this->getLayout()->createBlock('wordpress/feed_post')->setSourceBlock($block)->setFeedType(
						$this->getRequest()->getParam('feed', 'rss2')
					)->toHtml()
				);
		}
		else {
			$this->_forward('noRoute');
		}
	}
	
	/**
	 * Display the comments feed
	 *
	 * @return void
	 */
	public function commentsFeedAction()
	{
		$this->getResponse()
			->setHeader('Content-Type', 'text/xml; charset=UTF-8')
			->setBody(
				$this->getLayout()->createBlock('wordpress/feed_post_comment')
					->setSource(Mage::registry('wordpress_post'))
					->setFeedType($this->getRequest()->getParam('feed', 'rss2'))
					->toHtml()
			);
	}
}

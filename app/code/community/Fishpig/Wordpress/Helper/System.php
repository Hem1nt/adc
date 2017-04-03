<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Helper_System extends Fishpig_Wordpress_Helper_Abstract
{
	/**
	 * Generate and retrieve the integration test results
	 *
	 * @return array
	 */
	public function getIntegrationTestResults()
	{
		if (!Mage::helper('wordpress')->isEnabled()) {
			return false;
		}

		$results = array();

		Mage::dispatchEvent('wordpress_integration_tests_before', array('results' => $results, 'helper' => $this));
		
		if ($this->applyTest('_validateDatabaseConnection', $results)) {
			if (Mage::helper('wordpress')->isFullyIntegrated()) {
				$this->applyTest('_validateHomeUrl', $results);
				$this->applyTest('_validatePath', $results);
				$this->applyTest('_validateTheme', $results);
				$this->applyTest('_validatePlugins', $results, array());
				$this->applyTest('_validatePermalinks', $results);
				$this->applyTest('_validateHtaccess', $results);
				$this->applyTest('_validateMagentoVersion', $results);
				$this->applyTest('_upsellCustomerSynchronisation', $results);
				$this->applyTest('_upsellCustomPostTypes', $results);
				$this->applyTest('_upsellReCaptcha', $results);
				$this->applyTest('_checkForReviews', $results);
			}
		}
		
		$this->applyTest('_validateCurrentVersion', $results);

		Mage::dispatchEvent('wordpress_integration_tests_after', array('results' => $results, 'helper' => $this));
		
		if (count($results) <= 1) {
			$this->applyTest('_integrationSuccess', $results);
		}

		return $results;
	}
	
	/**
	 * Display the integration success message
	 *
	 * @return void
	 */
	protected function _integrationSuccess()
	{
		throw Fishpig_Wordpress_Exception::success(
			'Success!',
			'WordPress is integrated into Magento.' . '<span id="wp-int-success"></span>'
		);		
	}
	
	/**
	 * Check whether the database is connected
	 *
	 * @return void
	 */
	protected function _validateDatabaseConnection()
	{
		if (!Mage::helper('wordpress/database')->isConnected()) {
			throw Fishpig_Wordpress_Exception::error(
				'Database Error',
				$this->__('Error establishing a database connection')
				. '. You can confirm your WordPress database details by opening the file wp-config.php, which is in your WordPress root directory.'
			);
		}
		
		return true;
	}
	
	/**
	 * Determine whether the blog route is valid
	 *
	 * @return Varien_Object
	 */
	protected function _validateHomeUrl()
	{
		$helper = Mage::helper('wordpress');

		$site = rtrim($helper->getWpOption('siteurl'), '/');
		$home = rtrim($helper->getWpOption('home'), '/');
		$mage = rtrim(($helper->getUrl()), '/');
	
		if ($site === $mage) {
			throw Fishpig_Wordpress_Exception::error('Site URL', 
				$this->__('Your integrated blog URL (%s) matches your WordPress Site URL. Either change your blog route below or move WordPress to a different sub-directory.', $mage)
			);
		}
		else if ($mage !== $home) {
			throw Fishpig_Wordpress_Exception::warning('Home URL', 
				stripslashes(Mage::helper('wordpress')->__('Your WordPress home URL %s is invalid.  Please fix the <a href=\"%s\">home option</a>.', $home,  'http://codex.wordpress.org/Changing_The_Site_URL" target="_blank'))
				. $this->__(' Change to %s', $mage)
			);
		}

		if (is_dir(Mage::getBaseDir() . DS . $helper->getBlogRoute())) {
			throw Fishpig_Wordpress_Exception::warning('Home URL', 
				stripslashes(Mage::helper('wordpress')->__("A '%s' directory exists in your Magento root that will stop your integrated WordPress from displaying. You must delete this before your blog will display.", $helper->getBlogRoute()))
			);
		}
		
		return true;
	}
	
	/**
	 * Ensure the correct WordPress theme is installed
	 *
	 * @return bool
	 */
	protected function _validateTheme()
	{
		if (Mage::helper('wordpress')->getWpOption('template') !== 'twentytwelve') {
			throw Fishpig_Wordpress_Exception::error('Themes', 
				stripslashes(Mage::helper('wordpress')->__('You are using a non-supported WordPress theme that has not been tested. To improve your integration, enable the Twenty Twelve WordPress theme.'))
			);
		}
		
		return true;
	}
	
	/**
	 * Determine whether the WordPress path is valid
	 *
	 * @return void
	 */
	protected function _validatePath()
	{
		if (Mage::helper('wordpress')->getWordPressPath() === false) {
			throw Fishpig_Wordpress_Exception::error(
				'WordPress ' . $this->__('Path'), 
				$this->__("Unable to find a WordPress installation at '%s'", Mage::helper('wordpress')->getRawWordPressPath())
			);
		}
		
		return true;
	}
	
	/**
	 * Validate the plugins/extensions
	 *
	 * @param Varien_Object $params
	 * @return void
	 */
	protected function _validatePlugins(Varien_Object $params)
	{
		$file = Mage::getModuleDir('etc', 'Fishpig_Wordpress') . DS . 'fishpig.xml';

		if (!is_file($file)) {
			return $this;
		}
		
		$xml = simplexml_load_file($file);
		$results = $params->getResults();
		
		foreach((array)$xml->fishpig->extensions as $moduleName => $data) {
			$this->applyTest('_validatePlugin', $results, array_merge(
				(array)$data, 
				array('current_version' => (string)Mage::getConfig()->getNode()->modules->$moduleName->version)
			));
		}
		
		$params->setResults($results);
		
		return $this;
	}
	
	/**
	 * Validate a single plugin
	 *
	 * @param Varien_Object $params
	 * @return void
	 */
	protected function _validatePlugin(Varien_Object $params)
	{
		if ($params->getCurrentVersion() && version_compare($params->getNewVersion(), $params->getCurrentVersion(), '>')) {
			throw Fishpig_Wordpress_Exception::warning($params->getName(), $this->__('You have version %s installed. Update to %s.', 
				$params->getCurrentVersion(), 
				sprintf('<a href="%s" target="_blank">%s</a>', $params->getUrl(), $params->getNewVersion())
			));
		}
		
		if ($params->getId() && !$params->getCurrentVersion()) {
			if (Mage::helper('wordpress')->isPluginEnabled($params->getId())) {
				throw Fishpig_Wordpress_Exception::warning(
					$params->getName(),
					$this->__('Extension required for plugin to work. ') . $this->__('Install %s', sprintf('<a href="%s" target="_blank">extension</a>.', $params->getUrl()))
				);
			}
		}
		
		return $this;
	}

	/**
	 * Ensure that custom permalinks are setup
	 *
	 * @return $this
	 */
	protected function _validatePermalinks()
	{
		if (Mage::helper('wordpress/post')->useGuidLinks()) {
			throw Fishpig_Wordpress_Exception::warning(
				'Permalinks',
				'You are using the default permalinks. To stop potential duplicate content issues, change them to something else in the WordPress Admin.'
			);
		}	
		
		return $this;
	}
	
	/**
	 * Recommend Customer Synchronisation if multiple WP users exist
	 *
	 * @return $this
	 */
	protected function _upsellCustomerSynchronisation()
	{
		try {
			if (count(Mage::getResourceModel('wordpress/user_collection')) > 9) {			
				if (!Mage::helper('wordpress')->isAddonInstalled('Fishpig_Wordpress_Addon_CS')) {
					throw Fishpig_Wordpress_Exception::warning(
						'Single Sign-On',
						$this->__(
							'Synchronise your WordPress users and Magento customers with the <a href="%s" target="_blank">Customer Synchronisation</a> addon.',
							'http://fishpig.co.uk/wordpress-customer-synchronisation.html'
						)
					);
				}
			}
		}
		catch (Fishpig_Wordpress_Exception $e) {
			throw $e;	
		}
		catch (Exception $e) {
			Mage::helper('wordpress')->log($e);
		}
		
		return $this;
	}

	/**
	 * Recommend the custom post types extension if user has setup custom post types
	 *
	 * @return void
	 */
	protected function _upsellCustomPostTypes()
	{
		if (!Mage::helper('wordpress')->isAddonInstalled('Fishpig_Wordpress_Addon_CPT')) {
			$postTypes = Mage::getResourceModel('wordpress/post')->getAllPostTypes(true);
			
			$postTypesToRemove = array('acf', 'wpcf7_contact_form');
			
			foreach($postTypesToRemove as $type) {
				if (($index = array_search($type, $postTypes)) !== false) {
					unset($postTypes[$index]);
				}
			}
			
			if (count($postTypes) > 0) {
				throw Fishpig_Wordpress_Exception::warning(
					'Custom Post Types',
					$this->__(
						'It looks like you have setup some custom post types (%s) in WordPress. To use these in Magento you may need the <a href="%s" target="_blank">Custom Post Types</a> addon.',
						implode(', ', $postTypes),
						'http://fishpig.co.uk/magento/wordpress-integration/custom-post-types/'
					)
				);
			}
		}
	}
	
	/**
	 * Recommend ReCaptcha to combat spam comments
	 *
	 * @return void
	 */
	protected function _upsellReCaptcha()
	{
		if (!Mage::helper('wordpress')->isAddonInstalled('Fishpig_Wordpress_Addon_ReCaptcha')) {
			$comments = Mage::getResourceModel('wordpress/post_comment_collection')
				->addCommentApprovedFilter('spam')
				->load();
			
			if (count($comments) > 10) {
				throw Fishpig_Wordpress_Exception::warning(
					'ReCaptcha',
					$this->__(
						'Stop WordPress comment spam with <a href="%s" target="_blank">ReCaptcha</a>.',
						'http://fishpig.co.uk/wordpress-integration-recaptcha-comments.html'
					)
				);
			}
		}
	}
	
	/**
	 * Check whether the user has left a review
	 *
	 * @return void
	 */
	protected function _checkForReviews()
	{
		return $this;

		$modules = array_keys(Mage::app()->getConfig()->getNode('modules')->asArray());
		$fishpigModules = array();

		foreach($modules as $module) {
			if (strpos($module, 'Fishpig_Wordpress') === 0) {
				if (is_null(Mage::getStoreConfig('wordpress/review/' . strtolower($module)))) {
					$fishpigModules[] = $module;
				}
			}
		}
		
		if (in_array('Fishpig_Wordpress', $fishpigModules) !== false) {
			throw Fishpig_Wordpress_Exception::warning(
				'Review',
				$this->__(
					'Do you like WordPress Integration? Help keep the extension free by <a href="%s" class="fp-review" target="_blank">leaving a review</a>.',
					'http://fishpig.co.uk/magento/wordpress-integration/#reviews'
				)
			);
		}
		
		shuffle($fishpigModules);
		
		foreach($fishpigModules as $module) {
			$moduleUrl = (string)Mage::app()->getConfig()->getNode('modules/' . $module . '/fishpig/url');
			$moduleName = (string)Mage::app()->getConfig()->getNode('modules/' . $module . '/fishpig/name');
			
			if ($moduleUrl && $moduleName) {
				throw Fishpig_Wordpress_Exception::warning(
					'Review',
					$this->__(
						'Do you like %s? Help keep the %s great by <a href="%s#reviews" class="fp-review" target="_blank">leaving a review</a>.',
						$moduleName, $moduleName, $moduleUrl
					)
				);
			}
		}
		
		return $this;
	}
	
	/**
	 * Ensure the .htaccess file exists and doesn't reference the blog route
	 *
	 * @return $this
	 */
	protected function _validateHtaccess()
	{
		if (($path = Mage::helper('wordpress')->getWordPressPath()) !== false) {
			$file = rtrim($path, DS) . DS . '.htaccess';
			
			if (!is_file($file)) {
				throw Fishpig_Wordpress_Exception::warning(
					'.htaccess',
					'You do not have a WordPress .htaccess file.'
				);
			}
			
			if (is_readable($file) && ($data = @file_get_contents($file))) {
				$blogRoute = Mage::helper('wordpress')->getBlogRoute();

				if (preg_match('/\nRewriteBase \/' . preg_quote($blogRoute, '/') . '\//i', $data)) {
					throw Fishpig_Wordpress_Exception::warning(
						'.htaccess',
						'Your .htaccess file references your blog route but should reference your WordPress installation directory.'
					);
				}
			}
		}

		return $this;
	}
	
	/**
	 * Ensure the latest version is being used
	 *
	 * @return void
	 */
	protected function _validateCurrentVersion()
	{
		if ($this->hasValidCurlMethods()) {
			throw Fishpig_Wordpress_Exception::error(
				'Update Now',
				$this->__('Update to version %s', '<span id="wp-version"></span>')
					. "<script type=\"text/javascript\">try { $('wp-version').up('tr').hide(); } catch(e) {}</script>"
			);
		}
	}
	
	/**
	 * Ensure Magento 1.7 is being used
	 *
	 * @return void
	 */
	protected function _validateMagentoVersion()
	{
		if (!$this->hasValidCurlMethods()) {
			throw Fishpig_Wordpress_Exception::warning(
				'Update Now',
				$this->__('Update Magento to version 1.7.0.0 to use the Auto-Login feature.')
			);
		}
	}

	/**
	 * Apply an integration test
	 *
	 * @param string $func
	 * @param array $results
	 * @param mixed $params = null
	 * @return mixed
	 */
	public function applyTest($func, &$results, $params = null)
	{
		$funcResult = false;
		
		try {
			if (is_array($params)) {
				$params = new Varien_Object($params);
				$params->setResults($results);
			}
			else {
				$params = null;
			}

			if (is_array($func)) {
				$funcResult = call_user_func($func, $params);
			}
			else {
				$funcResult = $this->$func($params);
			}
			
			if ($params) {
				$results = $params->getResults();
			}
			
			return true;
		}
		catch (Fishpig_Wordpress_Exception $e) {
			switch($e->getCode()) {
				case 1: 
					$colour = '#00CC33';	
					break;
				case 2:
					$colour = 'yellow';
					break;
				case 3:
					$colour = '#FF3333';
					break;
				default:
					$colour = '#444';
			}
			
			$results[] = new Varien_Object(array(
				'title' => Mage::helper('wordpress')->__($e->getMessage()),
				'message' => $e->getLongMessage(),
				'bg_colour' => $colour,
			));
		}
		catch (Exception $e) {
			$results[] = new Varien_Object(array(
				'title' => Mage::helper('wordpress')->__('An unidentified error has occurred.'),
				'message' => $e->getMessage(),
				'bg_colour' => '#444',
			));
		}
		
		return $funcResult;
	}
	
	/**
	 * Attempt to login to WordPress
	 *
	 * @param string $username
	 * @param string $password
	 * @param string $destination
	 * @return bool
	 */
	public function loginToWordPress($username, $password, $destination = null)
	{
		if (is_null($destination)) {
			$destination = Mage::helper('wordpress')->getAdminUrl('index.php');
		}
		
		$result = $this->makeHttpPostRequest(Mage::helper('wordpress')->getBaseUrl('wp-login.php'), array(
			'log' => $username,
			'pwd' => $password,
			'rememberme' => 'forever',
			'redirect_to' => $destination,
			'testcookie' => 1
		));

		if ($result !== false) {
			if (strpos($result, 'Location: ') === false) {
				throw new Exception('WordPress Auto Login Failed: ' . substr($result, 0, strpos($result, "\r\n\r\n")));
			}
	
			foreach(explode("\n", $result) as $line) {
				if (substr(ltrim($line), 0, 1) === '<') {
					break;
				}
	
				header($line, false);
			}
	
			return true;
		}
		
		return false;
	}
		
	/**
	 * Send a HTTP Post request
	 *
	 * @param string $url
	 * @param array $data = array
	 * @return false|string
	 */
	public function makeHttpPostRequest($url, array $data = array())
	{
		if (!$this->hasValidCurlMethods()) {
			return false;
		}

		$curl = new Varien_Http_Adapter_Curl();

		$curl->setConfig(array(
			'verifypeer' => strpos($url, 'https://') !== false,
			'header' => true,
			'timeout' => 15,
			'referrer' => Mage::helper('wordpress')->getBaseUrl('wp-login.php'),
		));
		
		$curl->addOption(CURLOPT_FOLLOWLOCATION, false);
		$curl->addOption(CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');

		$curl->write(Zend_Http_Client::POST, $url, '1.1', array(), $data);

		$response = $curl->read();

		if ($curl->getErrno() || $curl->getError()) {
			throw new Exception(Mage::helper('wordpress')->__('CURL (%s): %s', $curl->getErrno(), $curl->getError()));
		}

		$curl->close();
		
		return $response;
	}

	/**
	 * Retrieve the extension version
	 *
	 * @param string $extension
	 * @return string
	 */
	public function getExtensionVersion($extension = 'Fishpig_Wordpress')
	{
		return (string)Mage::getConfig()->getNode('modules/' . $extension . '/version');
	}
	
	/**
	 * Has valid CURL methods
	 *
	 * @return bool
	 */
	public function hasValidCurlMethods()
	{
		return method_exists('Varien_Http_Adapter_Curl', 'addOption');
	}
}

<?php

Class Iksula_Elastic_SearchController extends Mage_Core_Controller_Front_Action {

	/*
	 *
	 * Returns ES Model
	 *
	 */
	public function getModel() {
		return Mage::getModel('elastic/search');
	}

	/*
	 *
	 * Initiates index creation action
	 * Returns "Success!" if successful
	 * This is just a sample method for testing.
	 */
	public function createAction() {
		//echo 'abc';exit;
		//$websiteCode = $this->getRequest()->getParam('code'); // Change this based on website to index.
		$websiteCode = 'alldaychemist';
		$response 	 = $this->getModel()->createAllProductIndexes($websiteCode);

		if($response) {
			echo "Success!";
		}

	}


	// public function checkAction() {
	// 	$ch = curl_init();
	// 	$Url = 'http://iksulabeta.com:9200/alldaychemist/short/_search';
	//     // Set URL to download
	//     curl_setopt($ch, CURLOPT_URL, $Url);

	//     // User agent
	//     curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");

	//     // Include header in result? (0 = yes, 1 = no)
	//     curl_setopt($ch, CURLOPT_HEADER, 0);

	//     // Should cURL return or print out the data? (true = return, false = print)
	//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	//     // Timeout in seconds
	//     curl_setopt($ch, CURLOPT_TIMEOUT, 10);

	//     curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, 'GET');

	//     // Download the given URL, and return output
	//     $output = curl_exec($ch);

	//     // Close the cURL resource, and free system resources
	//     curl_close($ch);
	//     $result = json_decode($output,true);

	//     	$searchSwitch = new Mage_Core_Model_Config();
	//     if($result['hits']['total'] <= 0) {

	// 		$searchSwitch ->saveConfig('custom_snippet/elasticsearch_tabs/elasticsearch_options', "mysql", 'default', 'elastic');
	//     } else {
	//     	$searchSwitch ->saveConfig('custom_snippet/elasticsearch_tabs/elasticsearch_options', "elastic", 'default', 'elastic');
	//     }
	// }

	/*
	 *
	 * Initiates product delete action
	 * Product Id as input parameter
	 * Prints response
	 * This is just a sample method for testing.
	 */
	public function deleteAction() {

		$id = $this->getRequest()->getParam('id');

		if(!$id) {
			return false;
		}

		$response = $this->getModel()->deleteProductIndex($id);

		//print_r($response); exit;

	}

	/*
	 *
	 * Fetches results for auto-suggest
	 * Search term as input parameter
	 * Generates and returns HTML of ES results
	 *
	 */
	public function fetchAction() {

		$query = $this->getRequest()->getParam('q');
		// Remove all script and special characters.
		$whiteSpace = ' ';
		$pattern = '/[^a-zA-Z0-9'  . $whiteSpace . ']/u';
		$query = preg_replace($pattern, '', (string) $query);
		$query = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $query);

		if(!$query) {
			return false;
		}
		// Get Website code and use it as elastic index.
		$elasticHelper 	= Mage::helper('elastic');
		$websiteCode	= $elasticHelper->getWebsiteCode();

		$elasticSearch = $this->getModel();
		$response = $elasticSearch->fetchAutoSuggestResults($query, $elasticSearch::ADVANCED_RESULTS,$websiteCode);
		$searchResponse = $response[0];
		$searchResponseType = $response[1];
		$searchResponseSuggestion = $response[2];

		$html = $this->getLayout()->createBlock('core/template')
			->setResult($searchResponse)
			->setSearchText($query)
			->setSearchResponseType($searchResponseType)
			->setSearchResponseSuggestion($searchResponseSuggestion)
			->setModel($elasticSearch)
			->setTemplate('elastic/autosuggest.phtml')
			->toHtml();


		$this->getResponse()->setBody($html);
		return;

	}

	/*
	 *
	 * Fetches results for search page
	 * Search term as input parameter
	 * Generates and injects HTML of ES results into layout
	 *
	 */
	public function resultAction() {
		// Get Website code and use it as elastic index.
		$elasticHelper 	= Mage::helper('elastic');
		$websiteCode	= $elasticHelper->getWebsiteCode();
		$params = $this->getRequest()->getParams();
		// $query = $this->getRequest()->getParam('q');

		$query = array();
		foreach ($params as $key => $value) {
			$whiteSpace = ' ';
			$pattern = '/[^a-zA-Z0-9'  . $whiteSpace . ']/u';
			$query[$key] = preg_replace($pattern, '', (string) $value);
			$query[$key] = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $value);
		}

		//echo $query = preg_replace($pattern, '', (string) $query);exit;

		/*if(!$query) {
			return false;
		}*/
		$elasticSearch  = $this->getModel();
		$searchResponse = $elasticSearch->fetchResults($query, $elasticSearch::ADVANCED_RESULTS,$websiteCode);

		if(count($searchResponse) == 0) {
			$url = $this->_redirect('contacts');
			return $url;
		}

		$this->loadLayout();
		$collection_medicines = array();

        foreach($searchResponse as $singleproduct){
            $product_obj = Mage::getModel('catalog/product')->load($singleproduct['id']);
            array_push($collection_medicines, $product_obj);
        }
		// elasticsearch filter logic

		$attributeArry = array('price','manufacturer','configurable_attribute','active_ingridients');
		$stateFiltersArr = $this->getStateFilters($query,$attributeArry);

        $filterData = array();
		foreach ($collection_medicines as $key => $product) {
			$manufacturer = $product->getData('manufacturer');
			$price = $product->getData('price');
			$active_ingridients = $product->getData('active_ingridients');
			$configurable_attribute = $product->getData('configurable_attribute');
			$filterData['price'][$price][] = $product->getData('price');
			$filterData['configurable_attribute'][$configurable_attribute][] = $product->getData('configurable_attribute');
			$filterData['manufacturer'][$manufacturer][] = $product->getData('manufacturer');
			$filterData['active_ingridients'][$active_ingridients][] = $product->getData('active_ingridients');
		}

		$show_price = Mage::getStoreConfig('custom_snippet/elasticsearch_tabs/elasticsearch_price');
		$show_activeingredient = Mage::getStoreConfig('custom_snippet/elasticsearch_tabs/elasticsearch_activeingredient');
		$show_strength = Mage::getStoreConfig('custom_snippet/elasticsearch_tabs/elasticsearch_strength');
		$show_manufacture = Mage::getStoreConfig('custom_snippet/elasticsearch_tabs/elasticsearch_manufacture');

		if($show_price == 0) {
			$filterresultCollection['price'] = $this->getUniqueValues($filterData['price']);

			// logic to convert price into range
			ksort($filterresultCollection['price'],SORT_REGULAR);
			$temp = ceil(count($filterresultCollection['price'])/4) ;
			$new_range = array_chunk($filterresultCollection['price'], $temp,true);
			$filterresultCollection['price'] = $this->priceRange($new_range);
		}

		if($show_manufacture == 0) {
			$filterresultCollection['manufacturer'] = $this->getUniqueValues($filterData['manufacturer']);
		}

		if($show_strength == 0) {
			$filterresultCollection['configurable_attribute'] = $this->getUniqueValues($filterData['configurable_attribute']);
		}
		if ($show_activeingredient == 0) {
			$filterresultCollection['active_ingridients'] = $this->getUniqueValues($filterData['active_ingridients']);
		}

		if($stateFiltersArr['attr_name']) {
			$filterresult = $this->array_except($filterresultCollection,$stateFiltersArr['attr_name']);
		} else {
			$filterresult = $filterresultCollection;
		}

		$block = $this->getLayout()
			->createBlock('catalog/product_list')
			->setName('search')
			->setLoadedProductCollectionone($collection_medicines)
			->setTemplate('elastic/product/list.phtml');

		// get state filter

		$filtersblock = $this->getLayout()->createBlock('catalogsearch/layer')->setTemplate('catalog/layer/elasticsearchview.phtml');
		$filtersblock->setData('filterdata',$filterresult)->setData('statefilter',$stateFiltersArr);
		$this->getLayout()->getBlock('left')->append($filtersblock);

		$this->getLayout()->getBlock('content')->append($block);

		$this->renderLayout();
	}

	public function priceRange($new_range) {
		foreach ($new_range as $key => $value) {
			$min = min(array_keys($value));
			$max = max(array_keys($value));
			if($min != $max) {
				$limit = $min.'-'.$max;
			} else {
				$limit = $max;
			}
			$range[$limit] = count($value);
		}

		return $range;
	}

	function array_except($array, $keys){
    	return array_diff_key($array, array_flip((array) $keys));
	}

	public function getUniqueValues($arr) {
		$out = array();
		foreach ($arr as $key => $value){
		    foreach ($value as $key2 => $value2){
		        $index = $value2;
		        if (array_key_exists($index, $out)){
		            $out[$index]++;
		        } else {
		            $out[$index] = 1;
		        }
		    }
		}

		return $out;
	}

	public function getStateFilters($query,$attributeArry) {
		$statefilter = array();
		$i = 0;
		foreach ($query as $key => $value) {
			if(in_array($key, $attributeArry)) {
				$statefilter['attr_name'][] = $key;
				$statefilter['name'][$i]['statefilter_name'] = $key;
				$statefilter['name'][$i]['statefilter_value'] = $value;
				$statefilter['name'][$i]['statefilter_removeurl'] = $this->MakeUrlString($query,$key,$value);
			} else {
				$urlparamsVal = $key.'='.$value;
			}
			$i++;
		}

		$request = $this->getRequest();
		$urlwithonlykeyword = Mage::getBaseUrl() . $request->getRouteName() .DS. $request->getControllerName() .DS. $request->getActionName()."/?".$urlparamsVal;
		$statefilter['statefilter_clearall'] = $urlwithonlykeyword;

		return $statefilter;
	}

	public function MakeUrlString($query,$key,$value) {
		foreach( $query as $querykey => $queryvalue ) {
		    if( $querykey == $key && $queryvalue == $value ) {
		      	unset($query[$key]);
		    } else {
		    	$urlparams[] = $querykey.'='.$queryvalue;
		    }
		}

		$urlQueryString = implode("&",$urlparams);
		$request = $this->getRequest();
		$url = Mage::getBaseUrl() . $request->getRouteName() .DS. $request->getControllerName() .DS. $request->getActionName()."/?".$urlQueryString;
		return $url;
	}

	// public function getOptionText($Attr,$code) {
	// 	$text = $Attr->getSource()->getOptionText($code);
	// 	return $text;
	// }

	// public function loadmoreAction(){

	// 	$fromIndex 		= $this->getRequest()->getParam('index');
	// 	$query 			= $this->getRequest()->getParam('query');
	// 	$elasticHelper 	= Mage::helper('elastic');
	// 	$websiteCode	= $elasticHelper->getWebsiteCode();
	// 	if(!$query) {
	// 		return false;
	// 	}


	// 	$elasticSearch 		= $this->getModel();
	// 	$searchResponse 	= $elasticSearch->fetchNextResults($query, $elasticSearch::ADVANCED_RESULTS,$websiteCode,$fromIndex);
	// 	//$productCollection 	= $this->getSearchProductCollection($searchResponse);

	// 	$this->loadLayout();

	// 	$block = $this->getLayout()
	// 		->createBlock('elastic/collection')
	// 		->setName('search')
	// 		->setResult($searchResponse);

	// 	$_productCollection = $block->getLoadedProductCollection();
	// 	$searchHtml 		= $this->getSearchProductHtml($_productCollection,$block);
	// 	//echo json_encode($searchHtml);
	// 	echo $searchHtml;
	// }


	// public function getSearchProductHtml($productCollection,$block){

	// 	$html = '';
	// 	$middle = '';
	// 	$i=0;
	// 	$_helper = Mage::helper('catalog/output');
	// 	// $urlHelper = Mage::helper('smsapp');
	// 	foreach ($productCollection as $_product){

	// 		$i++;
	// 		$id 		 = $_product->getId();
	// 		$_product    = Mage::getSingleton('catalog/product')->load($id);
	// 		$prod 		 = 'product_' . $_product->getId();
	// 		$imageText   = '';
	// 		//$parentUrl  = $urlHelper->getParentUrl($_product);
	// 		$parentUrl   = $_product->getProductUrl();
	// 		$searchPrice = Mage::helper('core')->currency($_product->getFinalPrice(),true,false);
	// 		$imageText   = $block->stripTags($block->getImageLabel($_product, 'thumbnail'), null, true);
	// 		$imageData   = Mage::helper('catalog/image')->init($_product, 'thumbnail')->resize(300,400);
	// 		$productname = $_helper->productAttribute($_product, $_product->getName(), 'name');
 //            $modProductname = substr($productname,0,40);
 //            if(strlen($productname) > 40){
 //            	$modProductname =  $modProductname . '....';
 //            }

	// 		if($i % 3 == 2)
	// 		{
	// 			$middle = 'middle';
	// 		}

	// 		$html .= 	'<li class="item ' . $middle . '"id="' . $prod  .'">';
	// 		$html .= 	'<a class="search_product_list" href="' . $parentUrl . '">';
	// 		$html .=	'<div class="product-image" id="' . $prod . '" title="' . $imageText . '">';
 //            $html .=    '<img class="item-image" src="' . $imageData . '" alt="' . $imageText . '" /></div>';
 //            $html .=    '<h2 class="product-name">' . $modProductname . '</h2>';
 //            //$html .=    $block->getPriceHtml($_product);
 //            $html .=	'<div class="search_price">' . $searchPrice . '</div>';
	// 		$html .= '</a></li>';
	// 	}
	// 	return $html;
	// }
}

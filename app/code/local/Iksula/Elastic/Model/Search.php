<?php

class Iksula_Elastic_Model_Search extends Mage_Core_Model_Abstract {

	const BASIC_RESULTS = 'basic';
	const ADVANCED_RESULTS = 'advanced';

	const RESULT_TYPE_PRODUCTS = 'products';
	const RESULT_TYPE_SUGGESTION = 'suggestion';
	const RESULT_TYPE_FAILED = 'no_results';

	private $_searchResponse = array();
	private $_searchResponseType = self::RESULT_TYPE_PRODUCTS;
	private $_searchResponseSuggestion = '';
	private $_totalCount = 0;

	// Default parameters

	private $_productIndex = 'products';
	private $_productType = 'short';

	// private $_primaryField = 'name';
	// private $_secondaryField = 'short_description';
	// private $_tertiaryFiled = 'description';

	// for adc search
	private $_primaryField = 'name';
	private $_secondaryField = 'us_brand_name';
	private $_tertiaryFiled = 'generic_name';

	private $_autosuggestSearchLimit = 10;
	private $_searchPageProductLimit = 99;

	private $_autosuggestImageSize = 50;

	private $_visibilityAccepted = array(3, 4);
	private $_visibilityRejected = array(1, 2);

	/*
	 *
	 * Indexes all products in ES
	 * No parameters expected
	 * Returns true if indexing is complete, else false
	 * Method name changed from createAllProductIndexes to createStatusquoProductIndexes.
	 * To built index of one store only.
	 */
	public function createAllProductIndexes($store) {
		// Here index is websitename and type is products.
		$_productIndex = $store;
		try {
			$elasticaClient = new Elastica_Client();
			// Load index
			$elasticaIndex = $elasticaClient->getIndex($_productIndex);


			// Create index
			if(!$elasticaIndex->exists()) {
				$elasticaIndex->create(
					array(
						'number_of_shards' => 4,
						'number_of_replicas' => 1,
						'analysis' => array(
							'analyzer' => array(
								'indexAnalyzer' => array(
									'type' => 'custom',
									'tokenizer' => 'standard',
									'filter' => array('lowercase', 'mySnowball'),
									'char_filter' => array('myMapping')
								),
								'searchAnalyzer' => array(
									'type' => 'custom',
									'tokenizer' => 'standard',
									'filter' => array('lowercase', 'mySnowball')
								)
							),
							'char_filter' => array(
								'myMapping' => array(
									'type' => 'mapping',
									'mappings' => array('-=>')
								)
							),
							'filter' => array(
								'mySnowball' => array(
									'type' => 'snowball',
									'language' => 'English'
								)
							)
						)
					),
					true
				);
			}

			// Get type
			$elasticaType = $elasticaIndex->getType($this->_productType);
			// Define mapping
			$mapping = new Elastica_Type_Mapping();
			$mapping->setType($elasticaType);
			$mapping->setParam('index_analyzer', 'indexAnalyzer');
			$mapping->setParam('search_analyzer', 'searchAnalyzer');
			// $mapping->setParam('sound_analyzer', 'soundsAnalyzer');

			// Define boost field
			$mapping->setParam('_boost', array('name' => '_boost', 'null_value' => 1.0));

			// Set mapping
			$mapping->setProperties(array(
				'id'                     => array('type' => 'integer', 'include_in_all' => FALSE),
				'name'                   => array('type' => 'string', 'include_in_all' => TRUE),
				'us_brand_name'          => array('type' => 'string', 'include_in_all' => TRUE),
				'generic_name'           => array('type' => 'string', 'include_in_all' => TRUE),
				'description'            => array('type' => 'string', 'include_in_all' => TRUE),
				'short_description'      => array('type' => 'string', 'include_in_all' => TRUE),
				'url'                    => array('type' => 'string', 'include_in_all' => FALSE),
				'sku'                    => array('type' => 'string', 'include_in_all' => TRUE),
				'manufacturer'           => array('type' => 'string', 'include_in_all' => TRUE),
				'active_ingridients'     => array('type' => 'string', 'include_in_all' => TRUE),
				'configurable_attribute' => array('type' => 'string', 'include_in_all' => TRUE),
				//'parent_url'        => array('type' => 'string', 'include_in_all' => FALSE),
				'price'            	     => array('type' => 'float', 'include_in_all' => TRUE),
				// 'image'                  => array('type' => 'string', 'include_in_all' => FALSE),
				// 'thumbnail'              => array('type' => 'string', 'include_in_all' => FALSE),
				'_boost'                 => array('type' => 'float', 'include_in_all' => FALSE)
			));

			// Send mapping to type
			$mapping->send();

			// Add products to documents
			$_productCollection = Mage::getModel('catalog/product')->getCollection()
				->addAttributeToSelect('*')
				->addAttributeToFilter('status', 1)
				->addAttributeToFilter('visibility', array('in' => $this->_visibilityAccepted));

			$_productCollection->joinField('stock_status','cataloginventory/stock_status','stock_status',

				'product_id=entity_id', array(

				// 'stock_status' => Mage_CatalogInventory_Model_Stock_Status::STATUS_IN_STOCK,

		    ));


			$baseUrl = Mage::getBaseUrl();
			// $helper = Mage::helper('smsapp');
			// echo "<pre>"; print_r($productDocument); exit;

			foreach($_productCollection as $_product) {
				// print_r($_product->getData());

				$urlPath	= $_product->getUrlPath();
				$url 		= $baseUrl . $urlPath;
				$productId  = $_product->getId();

				//$parentUrl  = $helper->getParentUrl($_product);
				// This is to get image url of product related to current store.
				// $_productImage 		= Mage::helper('catalog/image')->init($_product, 'small_image')->resize($this->_autosuggestImageSize);
				// $_productImage      = (string)$_productImage;
				// $_productThumbnail 	= Mage::helper('catalog/image')->init($_product, 'thumbnail')->resize($this->_autosuggestImageSize);
				// $_productThumbnail  = (string)$_productThumbnail;
				$productData = array(
					'id'                    => $_product->getId(),
					'name'                  => $_product->getName(),
					'us_brand_name'         => $_product->getUsBrandName(),
					'generic_name'		    => $_product->getGenericName(),
					'description'           => $_product->getDescription(),
					'short_description'     => $_product->getShortDescription(),
					'url'                   => $url,
					'sku'                   => $_product->getSku(),
					'manufacturer'          => $_product->getManufacturer(),
					'price'                 => $_product->getFinalPrice(),
					'active_ingridients'    => $_product->getActiveIngridients(),
					'configurable_attribute' => $_product->getConfigurableAttribute(),

					//'parent_url'        => $parentUrl,
					// 'price'					=> $_product->getFinalPrice(),
					// 'image'          	   => $_productImage,
					// 'thumbnail'      	   => $_productThumbnail,
					'_boost'            	=> 1.0
				);

				$productDocument = new Elastica_Document($productId, $productData);

				$elasticaType->addDocument($productDocument);
			}


			//echo 'abc';exit;
			// Remove documents of disabled products
			$_productCollection = Mage::getModel('catalog/product')->getCollection()
				->addAttributeToSelect('*')
				->addAttributeToFilter('status', 0);

			foreach($_productCollection as $_product) {
				$productId = $_product->getId();

				try {
					$productDocument = $elasticaType->getDocument($productId);
				} catch (Exception $e) {
					$productDocument = NULL;
				}

				if ($productDocument != NULL) {
					$elasticaType->deleteById($productId);
				}
			}

			// Remove documents of not visible products
			$_productCollection = Mage::getModel('catalog/product')->getCollection()
				->addAttributeToSelect('*')
				->addAttributeToFilter('visibility', array('in' => $this->_visibilityRejected));

			foreach($_productCollection as $_product) {
				$productId = $_product->getId();

				try {
					$productDocument = $elasticaType->getDocument($productId);
				} catch (Exception $e) {
					$productDocument = NULL;
				}

				if ($productDocument != NULL) {
					$elasticaType->deleteById($productId);
				}
			}

			$elasticaIndex->refresh();
			$elasticaType->getIndex()->refresh();
			return true;

		} catch(Exception $e) {
			//echo "<pre>"; print_r($e);
			Mage::log('Elastic Exception while indexing: ' . $e);
			return false;
		}

	}

	/*
	 *
	 * Updates a product's index in ES
	 * Product Id as input parameter
	 * Returns true if successful, else false
	 *
	 */
	public function updateProductIndex($productId,$websiteCode) {

		if(!$productId) {
			return false;
		}
			$elasticaClient = new Elastica_Client();
				// Assign website code to product index.
				$productIndex  = $websiteCode;
				$elasticaIndex = $elasticaClient->getIndex($productIndex);

				if($elasticaIndex->exists()) {
					// Get type
					$elasticaType = $elasticaIndex->getType($this->_productType);
					$_product = Mage::getModel('catalog/product')->load($productId);


					$baseUrl 		= Mage::getBaseUrl();
					$urlPath		= $_product->getUrlPath();
					$url 			= $baseUrl . $urlPath;
					if($_product->getStatus() == 1 AND in_array($_product->getVisibility(), $this->_visibilityAccepted)) {
						// $_productImage 		= Mage::helper('catalog/image')->init($_product, 'small_image')->resize($this->_autosuggestImageSize);
						// $_productThumbnail 		= Mage::helper('catalog/image')->init($_product, 'thumbnail')->resize($this->_autosuggestImageSize);
						// $_productImage 		= (string)$_productImage;
						// $_productThumbnail 		= (string)$_productThumbnail;
						$productPrice		= $_product->getFinalPrice();
						$productData = array(
							'id'                => $_product->getId(),
							'name'              => $_product->getName(),
							'us_brand_name'         => $_product->getUsBrandName(),
							'generic_name'		    => $_product->getGenericName(),
							'description'       => $_product->getDescription(),
							'short_description' => $_product->getShortDescription(),
							'sku'               => $_product->getSku(),
							'url'               => $url,
							'price'				=> $productPrice,
							'manufacturer'          => $_product->getManufacturer(),
							'active_ingridients'    => $_product->getActiveIngridients(),
							'configurable_attribute' => $_product->getConfigurableAttribute(),
							// 'image'             => $_productImage,
							// 'thumbnail'         => $_productThumbnail,
							'_boost'            => 1.0
						);

						$productDocument = new Elastica_Document($productId, $productData);
						$elasticaType->addDocument($productDocument);
					} else {
						$productId = $_product->getId();
						// try {
						// 	$productDocument = $elasticaType->getDocument($productId);
						// } catch (Exception $e) {
						// 	$productDocument = NULL;
						// }
						// if ($productDocument != NULL) {
						// 	$elasticaType->deleteById($productId);
						// }

						try {

						$elasticaType->deleteById($productId);
						} catch(Exception $e) {

							Mage::log('Elastic Exception while deleting the updated product ID ' . $productId . '. Exception: ' . $e);
						}
					}
					$elasticaIndex->refresh();
				}
			//}
		return;
	}

	/*
	 *
	 * Deletes a product's index in ES
	 * Product Id as input parameter
	 * Returns true if successful, else false
	 *
	 */
	public function deleteProductIndex($productId,$websiteCode) {

		if(!$productId) {
			return false;
		}

		$elasticaClient = new Elastica_Client();
		$productIndex  = $websiteCode;
		$elasticaIndex = $elasticaClient->getIndex($productIndex);

		if($elasticaIndex->exists()) {

			// Get type
			$elasticaType = $elasticaIndex->getType($this->_productType);

			try {

				$elasticaType->deleteById($productId);
			} catch(Exception $e) {

				Mage::log('Elastic Exception while deleting product ID ' . $productId . '. Exception: ' . $e);
			}

			$elasticaIndex->refresh();
		}
		return;
	}

	/*
	 *
	 * Fetches results for auto-suggest
	 * Search term and type of expected result as input parameters
	 * Returns array of ES results
	 *
	 */
	public function fetchAutoSuggestResults($query, $searchType = self::BASIC_RESULTS,$websiteCode) {
		try{
		// use website code and elastic index.
			$productIndex = $websiteCode;

			if(!$query) {
				return false;
			}

			$queryText = $query;

			if($searchType == self::BASIC_RESULTS) {

				$this->search($productIndex, $this->_productType, $queryText);

			} elseif($searchType == self::ADVANCED_RESULTS) {

				$this->search($productIndex, $this->_productType, $queryText, array($this->_primaryField));

				if($this->_totalCount < $this->_autosuggestSearchLimit) {

					$this->search($productIndex, $this->_productType, $queryText, array($this->_secondaryField), $this->_autosuggestSearchLimit - $this->_totalCount);

					if($this->_totalCount < $this->_autosuggestSearchLimit) {

						$this->search($productIndex, $this->_productType, $queryText, array($this->_tertiaryFiled), $this->_autosuggestSearchLimit - $this->_totalCount);

					}

				}

			}

			if($this->_totalCount < $this->_autosuggestSearchLimit) {

				$this->search($productIndex, $this->_productType, $queryText . '*', array($this->_primaryField), $this->_autosuggestSearchLimit - $this->_totalCount);

			}

			if($this->_totalCount == 0) {

				$queryJsonFormat = '{
					 	"query": {
						    "fuzzy": {
						      "name": {
						        "value": "' . $queryText . '",
						        "fuzziness": 2
						      }
						    }
					  	}
				}';

				$queryBuilder = new Elastica_Query_Builder($queryJsonFormat);
				$elasticaQuery = new Elastica_Query($queryBuilder->toArray());

				$elasticaClient = new Elastica_Client();

				$search = new Elastica_Search($elasticaClient);

				$elasticaResultSet = $search
					->addIndex($productIndex)
					->addType($this->_productType)
					->search($elasticaQuery);

				$phraseSuggestions = $elasticaResultSet->getResponse()->getData();
				$phraseSuggestions = $phraseSuggestions['hits']['hits'];
				// var_dump(count($phraseSuggestions));
				if(count($phraseSuggestions) > 0) {
					$closestMatch = $phraseSuggestions[0]['_source']['name'];
					$pattern = '/,? ?and | ?[,;-\s] ?/';
					$closestMatch = preg_split($pattern, $closestMatch);
					$closestMatch = $closestMatch[0];
					$closestMatch = strtolower($this->clean($closestMatch));
					$this->_searchResponseSuggestion = $closestMatch;
					$this->customsearch($productIndex, $this->_productType, $closestMatch, array($this->_primaryField), $this->_autosuggestSearchLimit - $this->_totalCount);
				} else if(count($phraseSuggestions) == 0) {
					$queryJsonFormat = '{
					 	"query": {
						    "fuzzy": {
						      "us_brand_name": {
						        "value": "' . $queryText . '",
						        "fuzziness": 2
						      }
						    }
					  	}
					}';

					$queryBuilder = new Elastica_Query_Builder($queryJsonFormat);
					$elasticaQuery = new Elastica_Query($queryBuilder->toArray());

					$elasticaClient = new Elastica_Client();

					$search = new Elastica_Search($elasticaClient);

					$elasticaResultSet = $search
						->addIndex($productIndex)
						->addType($this->_productType)
						->search($elasticaQuery);

					$phraseSuggestionsUsbrand = $elasticaResultSet->getResponse()->getData();
					$phraseSuggestionsUsbrand = $phraseSuggestionsUsbrand['hits']['hits'];

						if(count($phraseSuggestionsUsbrand) > 0) {
							$closestMatch = $phraseSuggestionsUsbrand[0]['_source']['us_brand_name'];
							$pattern = '/,? ?and | ?[,;-\s] ?/';
							$closestMatch = preg_split($pattern, $closestMatch);
							$closestMatch = $closestMatch[0];
							$closestMatch = $this->clean($closestMatch);

							$this->_searchResponseSuggestion = $closestMatch;
							$this->customsearch($productIndex, $this->_productType, $closestMatch, array($this->_primaryField), $this->_autosuggestSearchLimit - $this->_totalCount);

						} else if(count($phraseSuggestionsUsbrand) == 0) {
							$queryJsonFormat = '{
							 	"query": {
								    "fuzzy": {
								      "generic_name": {
								        "value": "' . $queryText . '",
								        "fuzziness": 2
								      }
								    }
							  	}
							}';

							$queryBuilder = new Elastica_Query_Builder($queryJsonFormat);
							$elasticaQuery = new Elastica_Query($queryBuilder->toArray());

							$elasticaClient = new Elastica_Client();

							$search = new Elastica_Search($elasticaClient);

							$elasticaResultSet = $search
								->addIndex($productIndex)
								->addType($this->_productType)
								->search($elasticaQuery);

							$phraseSuggestionsGeneric = $elasticaResultSet->getResponse()->getData();
							$phraseSuggestionsGeneric = $phraseSuggestionsGeneric['hits']['hits'];

							if(count($phraseSuggestionsGeneric) > 0) {
							$closestMatch = $phraseSuggestionsGeneric[0]['_source']['us_brand_name'];
							$pattern = '/,? ?and | ?[,;-\s] ?/';
							$closestMatch = preg_split($pattern, $closestMatch);
							$closestMatch = $closestMatch[0];
							$closestMatch = $this->clean($closestMatch);
							$this->_searchResponseSuggestion = $closestMatch;
							$this->customsearch($productIndex, $this->_productType, $closestMatch, array($this->_primaryField), $this->_autosuggestSearchLimit - $this->_totalCount);

							} else if(count($phraseSuggestionsGeneric) == 0) {
								$this->_searchResponseType = self::RESULT_TYPE_FAILED;
							}

						}

					}
				else {
					// $this->_searchResponseType = self::RESULT_TYPE_FAILED;
				}

			}
			return array($this->_searchResponse, $this->_searchResponseType, $this->_searchResponseSuggestion);
		}
		catch (Exception $e){

			echo "Sorry no Match Found";
		}

	}

	function clean($string) {
	   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

	   return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
	}

	/*
	 *
	 * Formats the ES result set into array response to be sent to client
	 *
	 */
	private function prepareResponse($elasticaResultSet) {

		$resultCount = $elasticaResultSet->getTotalHits();
		$searchResult = $elasticaResultSet->getResults();

		if($resultCount > 0) {
			foreach($searchResult as $searchItem) {
				$searchItemData = $searchItem->getData();
				$this->_searchResponse[$searchItemData['id']] = $searchItemData;
			}
		}

		$this->_searchResponseType = 'products';

		$this->_totalCount = count($this->_searchResponse);

	}


	//custom response for did you mean

	private function customprepareResponse($elasticaResultSet) {

		$resultCount = $elasticaResultSet->getTotalHits();
		$searchResult = $elasticaResultSet->getResults();

		if($resultCount > 0) {
			foreach($searchResult as $searchItem) {
				$searchItemData = $searchItem->getData();
				$this->_searchResponse[$searchItemData['id']] = $searchItemData;
			}
		}

		$this->_searchResponseType = self::RESULT_TYPE_SUGGESTION;

		$this->_totalCount = count($this->_searchResponse);

	}

	/*
	 *
	 * Performs actual search and interacts with the ES client
	 * Input parameters:
	 *
	 * $index     => ES index. Defaults to $_productIndex
	 * $type      => ES type. Defaults to $_productType
	 * $queryText => Search term entered
	 * $fields    => Fields to perform search in. Defaults to 'all'
	 * $size      => Size of search results to fetch. Defaults to $_autosuggestSearchLimit
	 * $from      => Used for pagination. Defines the index from which to fetch results. Defaults to 0
	 *
	 * Sets the result and result count parameters
	 *
	 */
	private function search($index, $type, $queryText, $fields = array('name'), $size = 10, $from = 0) {
		$fields = array('name','us_brand_name', 'generic_name');
		if(!$queryText) {
			return false;
		}

		// echo "<pre>"; print_r($queryText);
		// print_r($fields);
		//  exit;
		$elasticaClient = new Elastica_Client();

		// Load index
		$elasticaIndex = $elasticaClient->getIndex($index);

		// Get type
		$elasticaType = $elasticaIndex->getType($type);

		// Define a Query. We want a string query.
		$elasticaQueryString = new Elastica_Query_QueryString();

		$elasticaQueryString->setQuery((string)$queryText)->setFields($fields)->setDefaultOperator('AND');

		if(!$size) {
			$size = $this->_autosuggestSearchLimit;
		}

		// Create the actual search object with some data.
		$elasticaQuery = new Elastica_Query();
		$elasticaQuery->setQuery($elasticaQueryString)->setSort(array("name" => array('order' => "asc") ))->setFrom($from)->setSize($size);

		//Search on the index.
		$elasticaResultSet = $elasticaType->search($elasticaQuery);

		$this->prepareResponse($elasticaResultSet);

		return $this;

	}

	private function resultsearch($index, $type, $queryText, $fields = array('name'), $size = 10, $queryOrder,$from = 0) {

		// echo "<pre>"; print_r($size);  exit;
		if($queryOrder['dir']) {
			$direction = $queryOrder['dir'];
		}else {
			$direction = 'asc';
		}

		if($queryOrder['order']) {
			$order = $queryOrder['order'];
		} else {
			$order = 'name';
		}

		$fields = array('name','us_brand_name', 'generic_name');
		if(!$queryText) {
			return false;
		}
		$elasticaQuery = new Elastica_Query();
		if(array_key_exists("price",$queryText)) {
			$range = explode("-",$queryText['price']);
			$minPrice = floatval($range[0]);
			$maxPrice = floatval($range[1]);
			$fields = array('name','us_brand_name', 'generic_name');
		}
		if(array_key_exists("manufacturer",$queryText)) {
			$fields = array('name','us_brand_name', 'generic_name','manufacturer');
		}
		if(array_key_exists("configurable_attribute",$queryText)) {
			$fields = array('name','us_brand_name', 'generic_name','configurable_attribute');
		}
		if(array_key_exists("active_ingridients",$queryText)) {
			$fields = array('name','us_brand_name', 'generic_name','active_ingridients');
		}


		// echo "<pre>"; print_r($queryText);
		// print_r($fields);
		//  exit;
		$elasticaClient = new Elastica_Client();

		// Load index
		$elasticaIndex = $elasticaClient->getIndex($index);

		// Get type
		$elasticaType = $elasticaIndex->getType($type);

		if(array_key_exists("price",$queryText) || array_key_exists("manufacturer",$queryText) || array_key_exists("configurable_attribute",$queryText) || array_key_exists("active_ingridients",$queryText)) {

			$elasticaFilterAnd = new Elastica_Filter_And();
	        foreach ($queryText as $termName => $termValue) {
	    		if($termName != "q" && $termName != "price") {
		           $termObj  = new Elastica_Filter_Term();
		           $termObj->setTerm($termName,$termValue);
		           $elasticaFilterAnd->addFilter($termObj);
		       }
	        }
		}
		if(!empty($minPrice) || !empty($maxPrice)) {
			$rangeFilter = new Elastica_Filter_Range();
			$rangeFilter->addField('price',
			                    array(  'from' => $minPrice,
			                            'to' => $maxPrice
			                         )
			                 );
			$elasticaFilterBool = new Elastica_Filter_Bool();
			$elasticaFilterBool->addMust($rangeFilter);
			$elasticaQuery->setFilter($elasticaFilterBool);
		}
		// Define a Query. We want a string query.
		$elasticaQueryString = new Elastica_Query_QueryString();

		$elasticaQueryString->setQuery((string)$queryText['q'])->setFields($fields)->setDefaultOperator('AND');

		if(!$size) {
			$size = $this->_autosuggestSearchLimit;
		}

		// Create the actual search object with some data.

		if(array_key_exists("manufacturer",$queryText) || array_key_exists("configurable_attribute",$queryText) || array_key_exists("active_ingridients",$queryText)) {
			$elasticaQuery->setQuery($elasticaQueryString)->setFilter($elasticaFilterAnd)->setSort(array($order => array('order' => $direction) ))->setFrom($from)->setSize($size);
		}else if(array_key_exists("price",$queryText)){
		 		$elasticaQuery->setQuery($elasticaQueryString)->setSort(array($order => array('order' => $direction) ))->setFrom($from)->setSize($size);
		} else {
		$elasticaQuery->setQuery($elasticaQueryString)->setSort(array($order => array('order' => $direction) ))->setFrom($from)->setSize($size);
		}

		//Search on the index.
		// echo "<pre>";  echo "salman"; print_r($elasticaQuery);  exit;
		$elasticaResultSet = $elasticaType->search($elasticaQuery);
		$this->prepareResponse($elasticaResultSet);

		return $this;

	}



	//custom did you mean search

	private function customsearch($index, $type, $queryText, $fields = array('name'), $size = 10, $from = 0) {
		$fields = array('name','us_brand_name', 'generic_name');
		if(!$queryText) {
			return false;
		}

		$elasticaClient = new Elastica_Client();

		// Load index
		$elasticaIndex = $elasticaClient->getIndex($index);

		// Get type
		$elasticaType = $elasticaIndex->getType($type);
		// Define a Query. We want a string query.
		$elasticaQueryString = new Elastica_Query_QueryString();

		$elasticaQueryString->setQuery((string)$queryText)->setFields($fields)->setDefaultOperator('AND');

		if(!$size) {
			$size = $this->_autosuggestSearchLimit;
		}

		// Create the actual search object with some data.
		$elasticaQuery = new Elastica_Query();
		$elasticaQuery->setQuery($elasticaQueryString)->setSort(array("name" => array('order' => "asc") ))->setFrom($from)->setSize($size);
		//Search on the index.
		// echo "<pre>";  print_r($elasticaQuery); exit;
		$elasticaResultSet = $elasticaType->search($elasticaQuery);
		$this->customprepareResponse($elasticaResultSet);

		return $this;

	}


	/*
	 *
	 * Fetches results for search page
	 * Search term as input parameter
	 * Returns array of ES results
	 *
	 */
	/*public function fetchResults($query,$websiteCode) {

		$productIndex = $websiteCode;// Assign website code to index.
		if(!$query) {
			return false;
		}

		$queryText = $query;
		// Can use _all for all.
		$fields = array('name','sku');
		$resultSize = 10;
		$this->search($productIndex, $this->_productType, $queryText, $fields, $resultSize);
		return $this->_searchResponse;
	}*/

	public function fetchResults($query, $searchType = self::BASIC_RESULTS,$websiteCode) {
		//try{
		// use website code and elastic index.
			// $fields = array('name','sku');
			$fields = array('name','us_brand_name', 'generic_name',);
			$resultSize = 10;
			$productIndex = $websiteCode;

			if(!$query) {
				return false;
			}

			$attributeArry = array('price','manufacturer','configurable_attribute','active_ingridients','q');
			foreach ($query as $key => $value) {
				if(in_array($key, $attributeArry)) {
					$queryAttr[$key] = $value;
				} else {
					$queryOrder[$key] = $value;
				}
			}


			if($queryOrder['limit']) {
				$resultSize = $queryOrder['limit'];
			}

			$queryText = $queryAttr;
			if($searchType == self::BASIC_RESULTS) {

				$this->resultsearch($productIndex, $this->_productType, $queryText, $fields, $resultSize,$queryOrder);

			} elseif($searchType == self::ADVANCED_RESULTS) {

				$this->resultsearch($productIndex, $this->_productType, $queryText, array($this->_primaryField), $resultSize,$queryOrder);

				if($this->_totalCount < $this->_autosuggestSearchLimit) {
					$this->resultsearch($productIndex, $this->_productType, $queryText, array($this->_secondaryField), $resultSize,$queryOrder);

					if($this->_totalCount < $this->_autosuggestSearchLimit) {

						$this->resultsearch($productIndex, $this->_productType, $queryText, array($this->_tertiaryFiled), $resultSize,$queryOrder);
					}

				}

			}

			// if($this->_totalCount < $this->_autosuggestSearchLimit) {

			// 	$this->search($productIndex, $this->_productType, $queryText . '*', array($this->_primaryField), $this->_autosuggestSearchLimit - $this->_totalCount);

			// }

			if($this->_totalCount == 0) {

				$queryJsonFormat = '{
						"query": {
							"multi_match": {
							"query": "' . $queryText . '",
							"fields": [
								"_all"
							],
							"operator": "and"
						}
					},
					"suggest": {
						"text": "' . $queryText . '",
						"film": {
							"phrase": {
								"analyzer": "searchAnalyzer",
								"field": "_all",
								"size": 6,
								"real_word_error_likelihood": 0.9,
								"max_errors": 0.5,
								"gram_size": 1
							}
						}
					},
					"from": 0,
					"size": 1,
					"sort": [],
					"facets": []
				}';

				$queryBuilder = new Elastica_Query_Builder($queryJsonFormat);
				$elasticaQuery = new Elastica_Query($queryBuilder->toArray());

				$elasticaClient = new Elastica_Client();

				$search = new Elastica_Search($elasticaClient);
				$phraseSuggestions = array();
				/** Salman Shaikh
				to stop the elastica execption for all strings who doesnt have search results
				**/
				try {
					$elasticaResultSet = $search
						->addIndex($productIndex)
						->addType($this->_productType)
						->search($elasticaQuery);

					$phraseSuggestions = $elasticaResultSet->getResponse()->getData();
					$phraseSuggestions = $phraseSuggestions['suggest']['film'][0]['options'];
				} catch (Exception $e) {

				}
				if(count($phraseSuggestions) > 0) {

					$closestMatch = $phraseSuggestions[0]['text'];

					$this->_searchResponseType = self::RESULT_TYPE_SUGGESTION;
					$this->_searchResponseSuggestion = $closestMatch;

				} else {

					$this->_searchResponseType = self::RESULT_TYPE_FAILED;

				}

			}
			return $this->_searchResponse;
		/*}
		catch (Exception $e){

			echo "Sorry no Match Found";
		}*/
	}
	/*
	* It will fech search results for infinite scroll on search page.
	*/
	/*public function fetchNextResults($query,$websiteCode,$fromIndex) {

		$productIndex = $websiteCode;// Assign website code to index.
		if(!$query) {
			return false;
		}

		$queryText = $query;
		$fields = array('name','sku');
		$resultSize = 10;
		$this->search($productIndex, $this->_productType, $queryText, $fields, $resultSize,$fromIndex);
		return $this->_searchResponse;
	}*/

	public function fetchNextResults($query, $searchType = self::BASIC_RESULTS,$websiteCode, $fromIndex) {

		$fields = array('name','us_brand_name', 'generic_name');
		$resultSize = 10;
		$productIndex = $websiteCode;

		if(!$query) {
			return false;
		}

		$queryText = $query;

		if($searchType == self::BASIC_RESULTS) {

			$this->search($productIndex, $this->_productType, $queryText, $fields, $resultSize, $fromIndex);

		} elseif($searchType == self::ADVANCED_RESULTS) {

			$this->search($productIndex, $this->_productType, $queryText, array($this->_primaryField), $resultSize, $fromIndex);

			if($this->_totalCount < $this->_autosuggestSearchLimit) {
				$this->search($productIndex, $this->_productType, $queryText, array($this->_secondaryField), $this->_autosuggestSearchLimit - $this->_totalCount, $resultSize, $fromIndex);

				if($this->_totalCount < $this->_autosuggestSearchLimit) {

					$this->search($productIndex, $this->_productType, $queryText, array($this->_tertiaryFiled), $this->_autosuggestSearchLimit - $this->_totalCount, $fromIndex);

				}

			}

		}

		if($this->_totalCount < $this->_autosuggestSearchLimit) {

			$this->search($productIndex, $this->_productType, $queryText . '*', array($this->_primaryField), $this->_autosuggestSearchLimit - $this->_totalCount, $fromIndex);

		}

		if($this->_totalCount == 0) {

			$queryJsonFormat = '{
					"query": {
						"multi_match": {
						"query": "' . $queryText . '",
						"fields": [
							"_all"
						],
						"operator": "and"
					}
				},
				"suggest": {
					"text": "' . $queryText . '",
					"film": {
						"phrase": {
							"analyzer": "searchAnalyzer",
							"field": "_all",
							"size": 6,
							"real_word_error_likelihood": 0.9,
							"max_errors": 0.5,
							"gram_size": 1
						}
					}
				},
				"from": 0,
				"size": 1,
				"sort": [],
				"facets": []
			}';

			$queryBuilder = new Elastica_Query_Builder($queryJsonFormat);
			$elasticaQuery = new Elastica_Query($queryBuilder->toArray());

			$elasticaClient = new Elastica_Client();

			$search = new Elastica_Search($elasticaClient);

			$elasticaResultSet = $search
				->addIndex($productIndex)
				->addType($this->_productType)
				->search($elasticaQuery);

			$phraseSuggestions = $elasticaResultSet->getResponse()->getData();
			$phraseSuggestions = $phraseSuggestions['suggest']['film'][0]['options'];
			if(count($phraseSuggestions) > 0) {

				$closestMatch = $phraseSuggestions[0]['text'];

				$this->_searchResponseType = self::RESULT_TYPE_SUGGESTION;
				$this->_searchResponseSuggestion = $closestMatch;

			} else {

				$this->_searchResponseType = self::RESULT_TYPE_FAILED;

			}

		}
		return $this->_searchResponse;
	}
}

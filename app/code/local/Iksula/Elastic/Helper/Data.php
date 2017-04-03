<?php

/*
 *
 * ES Helper rewrites and extends the CatalogSearch Helper
 *
 */
class Iksula_Elastic_Helper_Data extends Mage_CatalogSearch_Helper_Data {

	/*
	 *
	 * Returns URL which serves the ES result page
	 *
	 */
	public function getResultUrl($query = NULL) {
		return Mage::getUrl('elastic/search/result');
	}

	/*
	 *
	 * Returns URL which serves the ES auto-suggest results
	 *
	 */
	public function getFetchUrl() {
		return Mage::getUrl('elastic/search/fetch');
	}

	/*
	 *
	 * Returns URL which serves the ES result page, with a parameter
	 *
	 */
	public function getCorrectedUrl($string) {
		return Mage::getUrl('elastic/search/result') . '?q=' . $string;
	}

	public function getSuggestUrl()
    {
       return Mage::getUrl('elastic/search/fetch');
    }


    /**
	 *
	 *
	 * Called on product list page. Checks if it is search page and appends query string to product URL
	 *
	 *
	 */
	public function checkAndReturnSearchString() {

		$searchString = Mage::app()->getRequest()->getParam('q');
		if($searchString) {
			$searchString = '?search=' . urlencode($searchString);
			return $searchString;
		}

		$giftParams = Mage::app()->getRequest()->getParams();
		if(array_key_exists('gender', $giftParams) OR array_key_exists('occasion', $giftParams) OR array_key_exists('personality', $giftParams)) {
			$searchString = '?';
			foreach($giftParams as $giftParam => $value) {
				if($value) {
					$searchString .= $giftParam . '=' . $value . '&';
				}
			}
			$searchString = rtrim($searchString, '&');
			return $searchString;
		}
		return false;
	}

	/*
	* It fetches the current active website code.
	*
	*/
	public function getWebsiteCode(){

		//$websiteCode = Mage::app()->getWebsite()->getCode();
		$websiteCode = 'alldaychemist';
		return $websiteCode;
	}

	// Returns Option attribute label for code
	public function getOptionText($Attr,$code) {
		$text = $Attr->getSource()->getOptionText($code);
		return $text;
	}
}

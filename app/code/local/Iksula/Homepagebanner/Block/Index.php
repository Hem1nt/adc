<?php   
class Iksula_Homepagebanner_Block_Index extends Mage_Core_Block_Template{   
	public function __construct(){		
		$this->addData(array(
	     'cache_lifetime' => 36000, // Time to live
	      // Tags to describe your block
	     'cache_tags'     => array(Mage_Core_Model_Store::CACHE_TAG,Mage_Cms_Model_Block::CACHE_TAG) 
	     ));	
	}

	public function getCacheKeyInfo()
	{
			/// This is the unique identifier for your block cache file, used by magento when clearing the cache. 
			//  You can use any parameters to identify.
		return array(
			'homepagebanners',
			Mage::app()->getStore()->getId(),
			(int)Mage::app()->getStore()->isCurrentlySecure(),
			Mage::getDesign()->getPackageName(),
			Mage::getDesign()->getTheme('template')
			);
	}

	public function getBannerHtml()
	{
		
		$model = Mage::getSingleton('homepagebanner/homeslider')->getCollection()
		->addFieldToFilter('status', '1')
		->addFieldToFilter('website', '0')
		->setOrder('sortorder', 'asc');
		$mediaurl=Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
		$html ='<ul id="slides1">';
		foreach ($model->getData() as $banner) {
			$url = $banner['url'];
			if (!parse_url($url, PHP_URL_SCHEME)) {
				$url = 'http://' . $url;
			}
			$imagesrc = $mediaurl.''.$banner['image'];
			$html.='<li>	<div class="adc_banner_area"> ';
			$html.='<a href="'.$url.'">';
			$html.='<img src="'.$imagesrc.'" alt="'.$banner['name'].'" />';
			$html.='</a>';
			$html.='</div><div class="clear"></div></li>';
		}
		return $html;	

	}

	public function getMobileBannerHtml()
	{
		$model = Mage::getSingleton('homepagebanner/homeslider')->getCollection()
		->addFieldToFilter('status', '1')
		->addFieldToFilter('website', '1')
		->setOrder('sortorder', 'asc');
		$mediaurl=Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
		$html ='<ul class="rslides" id="slider2">';
		foreach ($model->getData() as $banner) {
			$url = $banner['url'];
			if (!parse_url($url, PHP_URL_SCHEME)) {
				$url = 'http://' . $url;
			}
			$imagesrc = $mediaurl.''.$banner['image'];
			$html.='<li>';
			$html.='<a href="'.$url.'">';
			$html.='<img src="'.$imagesrc.'" alt="'.$banner['name'].'" />';
			$html.='</a>';
			$html.='</li>';
		}
		$html.='</ul>';
		return $html;

	}
	public function getMobileBannerHtml2()
	{
		$model = Mage::getSingleton('homepagebanner/homeslider')->getCollection()
		->addFieldToFilter('status', '1')
		->addFieldToFilter('website', '1')
		->setOrder('sortorder', 'asc');
		$mediaurl=Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
		$html ='<ul class="bxslider slider1" id="slides2">';
		foreach ($model->getData() as $banner) {
			$url = $banner['url'];
			if (!parse_url($url, PHP_URL_SCHEME)) {
				$url = 'http://' . $url;
			}
			$imagesrc = $mediaurl.''.$banner['image'];
			$html.='<li>';
		// $html.='<div class="adc_banner_area">';
			$html.='<a href="'.$url.'">';
			$html.='<img src="'.$imagesrc.'" alt="'.$banner['name'].'" />';
			$html.='</a>';
		// $html.='</div>';
		// $html='<div class="clear"></div>';
			$html.='</li>';
		}
		$html.='</ul>';
		return $html;

	}

}
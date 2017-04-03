<?php

class Iksula_Overrides_Block_Catalog_Product_View extends Mage_Catalog_Block_Product_View {


    protected $_availableTemplates = array(
        'default' => 'review/helper/summary.phtml',
        'short'   => 'review/helper/summary_short.phtml'
    );
    
    protected function _prepareLayout()
    {
        $offerMetatagsStatus = Mage::getStoreConfig('dynamic_metatags/metatags_details/offer_metatags_status',Mage::app()->getStore());
        $dynamicMetatagsStatus = Mage::getStoreConfig('dynamic_metatags/metatags_details/dynamic_metatags_status',Mage::app()->getStore());
        
        $this->getLayout()->createBlock('catalog/breadcrumbs');
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $product = $this->getProduct();
            $title = $product->getMetaTitle();
            $description = $product->getMetaDescription();
            
            $productData = array('name'=>$product->getName(),'sku'=>$product->getSku(),'price'=>$product->getPrice());
            $keyCollection = array('{name}','{price}','{sku}');
            $valueCollection = array($product->getName(),$product->getFinalPrice(),$product->getSku());
           
            // get dynamic meta details if meta tags are empty
            if($dynamicMetatagsStatus==1){
                $dynamicMetatitle = Mage::getStoreConfig('dynamic_metatags/metatags_details/default_metatitle',Mage::app()->getStore());
                if((empty($title) || is_null($title)) && !empty($dynamicMetatitle)){                
                    $metaTitle =  str_replace($keyCollection,$valueCollection,$dynamicMetatitle);
                    $product->setMetaTitle(Mage::helper('core/string')->substr($metaTitle, 0, 255));
                }

                $description_template = Mage::getStoreConfig('dynamic_metatags/metatags_details/default_metadescription',Mage::app()->getStore());
                if(empty($description) && !empty($description_template)){
                    $metaDescription =  str_replace($keyCollection,$valueCollection,$description_template);
                    $product->setMetaDescription($metaDescription);
                }
            }

            // get offer meta details if offer is running
            if($offerMetatagsStatus==1){
                $offerTitleTemplate = Mage::getStoreConfig('dynamic_metatags/metatags_details/offer_metatitle',Mage::app()->getStore());
                $title = $product->getMetaTitle();
                $offerTitle = '';
                if(!empty($title) && !empty($offerTitleTemplate)){
                    if (strpos($title,$offerTitleTemplate) === false) {
                         $offerTitle = $offerTitleTemplate.'-'.$product->getMetaTitle();echo "<br/>";
                         $product->setMetaTitle($offerTitle);
                    }                    
                }

                $description = $product->getMetaDescription();
                $offerDescriptionTemplate = Mage::getStoreConfig('dynamic_metatags/metatags_details/offer_metadescription',Mage::app()->getStore());
                $offerDescription = '';
                if(!empty($description) && !empty($offerDescriptionTemplate)){
                    if (strpos($description,$offerDescriptionTemplate) === false) {
                        $offerDescription = $offerDescriptionTemplate.'-'.$product->getMetaDescription();                
                        $product->setMetaDescription($offerDescription);
                    }
                }
            }
            // echo $this->helper('catalog/product')->canUseCanonicalTag().'qqqq';
            // echo $product->getCanonicalUrl();
            if ($this->helper('catalog/product')->canUseCanonicalTag()) {
                $params = array('_ignore_category'=>true);
                // $headBlock->addLinkRel('canonical', $product->getUrlModel()->getUrl($product, $params));
                $CanonicalUrl = $product->getCanonicalUrl();
                if(isset($CanonicalUrl)){
                    $explodeUrl = explode(',',$CanonicalUrl);
                    if(count($explodeUrl)>1){
                        foreach ($explodeUrl as $key => $value) {
                           $headBlock->addLinkRel('canonical', $value); 
                        }
                    }else{
                        $headBlock->addLinkRel('canonical', $CanonicalUrl);
                    }
                }
            }       

        }

        return parent::_prepareLayout();
    }

    public function getSummaryHtml($product, $templateType, $displayIfNoReviews)
    {
        // pick template among available
        if (empty($this->_availableTemplates[$templateType])) {
            $templateType = 'default';
        }
        $this->setTemplate($this->_availableTemplates[$templateType]);

        $this->setDisplayIfEmpty($displayIfNoReviews);

        if (!$product->getRatingSummary()) {
            Mage::getModel('review/review')
               ->getEntitySummary($product, Mage::app()->getStore()->getId());
        }
        $this->setProduct($product);

        return $this->toHtml();
    }

    public function getRatingSummary()
    {
        $storeId    = Mage::app()->getStore()->getId();
        $summaryData = Mage::getModel('review/review_summary')
                       ->setStoreId($storeId)
                       ->load($this->getProduct()->getId());
        return $summaryData->getRatingSummary();               
    }


    public function getReviewsCount()
    {
        $storeId    = Mage::app()->getStore()->getId();
        $summaryData = Mage::getModel('review/review_summary')
                       ->setStoreId($storeId)
                       ->load($this->getProduct()->getId());
        return $summaryData->getReviewsCount();
    }

    public function getReviewsUrl()
    {
        return Mage::getUrl('review/product/list', array(
           'id'        => $this->getProduct()->getId(),
           'category'  => $this->getProduct()->getCategoryId()
        ));
    }

    /**
     * Add an available template by type
     *
     * It should be called before getSummaryHtml()
     *
     * @param string $type
     * @param string $template
     */
    public function addTemplate($type, $template)
    {
        $this->_availableTemplates[$type] = $template;
    }



}
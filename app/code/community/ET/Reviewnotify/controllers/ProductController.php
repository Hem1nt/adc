<?php
/**
 * NOTICE OF LICENSE
 *
 * You may not sell, sub-license, rent or lease
 * any portion of the Software or Documentation to anyone.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade to newer
 * versions in the future.
 *
 * @category   ET
 * @package    ET_Reviewnotify
 * @copyright  Copyright (c) 2012 ET Web Solutions (http://etwebsolutions.com)
 * @contacts   support@etwebsolutions.com
 * @license    http://shop.etwebsolutions.com/etws-license-free-v1/   ETWS Free License (EFL1)
 */

require_once 'Mage/Review/controllers/ProductController.php';
class ET_Reviewnotify_ProductController extends Mage_Review_ProductController
{
    const SECRET_KEY = "ET_Special_Codes_String";

    public function prepostAction()
    {
        $result = array("sequence"=>$this->_calculateCode($this->getRequest()->getPost()));
        $this->getResponse()->setBody(
            '<script>window.parent.postReviewRestoreData("'.$result["sequence"].'")</script>'
            );
    }

    public function postAction()
    {
        $data = $this->getRequest()->getPost();
        if (Mage::getStoreConfig('catalog/review/antispam')) {
            if (!isset($data["sequence"])) {
                $data["sequence"] = "";
            }
            if (strcmp($data["sequence"], $this->_calculateCode($data)) != 0) {
                $session = Mage::getSingleton('core/session');
                $session->setFormData($data);
                $session->addError($this->__('Unable to post the review.'));
                if ($redirectUrl = Mage::getSingleton('review/session')->getRedirectUrl(true)) {
                    $this->_redirectUrl($redirectUrl);
                    return;
                }
                $this->_redirectReferer();
                return;
            }
        }
        if ($data = Mage::getSingleton('review/session')->getFormData(true)) {
            $rating = array();
            if (isset($data['ratings']) && is_array($data['ratings'])) {
                $rating = $data['ratings'];
            }
        } else {
            $data   = $this->getRequest()->getPost();
            $rating = $this->getRequest()->getParam('ratings', array());
        }

        if (($product = $this->_initProduct()) && !empty($data)) {
            $session    = Mage::getSingleton('core/session');
            /* @var $session Mage_Core_Model_Session */
            $review     = Mage::getModel('review/review')->setData($data);
            /* @var $review Mage_Review_Model_Review */

            $validate = $review->validate();
            if ($validate === true) {
                try {
                    $review->setEntityId($review->getEntityIdByCode(Mage_Review_Model_Review::ENTITY_PRODUCT_CODE))
                    ->setEntityPkValue($product->getId())
                    ->setStatusId(Mage_Review_Model_Review::STATUS_PENDING)
                    ->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->setStores(array(Mage::app()->getStore()->getId()))
                    ->save();

                    foreach ($rating as $ratingId => $optionId) {
                        Mage::getModel('rating/rating')
                        ->setRatingId($ratingId)
                        ->setReviewId($review->getId())
                        ->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
                        ->addOptionVote($optionId, $product->getId());
                    }
                    $review->aggregate();
                    $session->addSuccess($this->__('Thank you for submitting your review. You review has been accepted for moderation.'));
                    $customerEmail = Mage::getSingleton('customer/session')->getCustomer()->getEmail();
                    $salesOrderCollection = Mage::getModel('sales/order')->getCollection()->addFieldtoFilter('customer_email',$customerEmail);
                    foreach ($salesOrderCollection as $order) {
                        foreach ($order->getAllVisibleItems() as $item) {
                            $id = $this->getParentId($item);
                            if($id == $product->getId()){
                                $item->setIsReviwed(1)->save();
                            }
                        }
                    }
                }
                catch (Exception $e) {
                    $session->setFormData($data);
                    $session->addError($this->__('Unable to post the review.'));
                }
            }
            else {
                $session->setFormData($data);
                if (is_array($validate)) {
                    foreach ($validate as $errorMessage) {
                        $session->addError($errorMessage);
                    }
                }
                else {
                    $session->addError($this->__('Unable to post the review.'));
                }
            }
        }

        if ($redirectUrl = Mage::getSingleton('review/session')->getRedirectUrl(true)) {
            $this->_redirectUrl($redirectUrl);
            return;
        }
        $this->_redirectReferer();
    }

    protected function _calculateCode($data)
    {
        $allKeys = array("title", "nickname", "detail");
        $allForGen = array(self::SECRET_KEY);
        foreach ($allKeys as $oneKey) {
            $allForGen[] = isset($data[$oneKey])?$data[$oneKey]:rand();
        }
        return md5(implode("|", $allForGen));
    }

    public function getParentId($item)  {
        if($item->getProductType() != 'bundle'){
            $parentId = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($item->getProductId());
            return $parentId[0];
        }else{
            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            return $product->getId();
        }
    }
}
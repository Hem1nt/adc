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
        return parent::postAction();
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
}
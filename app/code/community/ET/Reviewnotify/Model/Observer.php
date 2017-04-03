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

class ET_Reviewnotify_Model_Observer
{
    public function __construct()
    {
    }

    public function send_nofificatin_mail($observer)
    {
        $this->needSend = Mage::getStoreConfig('catalog/review/need_send');
        $this->eventEmail = Mage::getStoreConfig('catalog/review/email_to');
        $this->emailTemplate = Mage::getStoreConfig('catalog/review/email_template');
        $this->emailIdentity = Mage::getStoreConfig('catalog/review/email_identity');


        if (($this->needSend) & (trim($this->eventEmail))) {
            $product = Mage::getModel('catalog/product')->load($observer->object->getEntityPkValue());
            $emailTemplate = Mage::getModel('core/email_template');

            $recipients = explode(",", $this->eventEmail);
            foreach ($recipients as $k => $recipient) {
                $sendresult = $emailTemplate->setDesignConfig(array('area'  => 'backend'))
                    ->sendTransactional(
                        $this->emailTemplate,
                        $this->emailIdentity,
                        trim($recipient),
                        trim($recipient),
                        array(
                            "product"  => $product->getName()." (sku: ".$product->getsku().")",
                            "title"    => $observer->object->getTitle(),
                            "nickname" => $observer->object->getNickname(),
                            "details"  => $observer->object->getDetail(),
                            "id"       => $observer->object->getId(),
                            'date'     => Mage::app()->getLocale()->date(
                                date("Y-m-d H:i:s"), 
                                Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM),
                                null,
                                true
                            )
                        )
                    );
            }
        }
    }

}
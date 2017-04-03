<?php
/**
 * @copyright   Copyright (c) 2011 Amasty (http://www.amasty.com)
 */ 
class Amasty_Oaction_Model_Source_Transactional {

    public function toOptionArray(){        
        $options = array(); 
        $options[] = array(
                'value' => '',
                'label' => 'No Template'
            );
        $mailTemplate = Mage::getModel('core/email_template')->getCollection();
        foreach ($mailTemplate as $value) {
            $options[] = array(
                'value' => $value->getTemplateId(),
                'label' => $value->getTemplateId().' ==> '.$value->getTemplateCode()
            );
        }
       
        return $options;
    }



}

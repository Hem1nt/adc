<?php
class Iksula_Configfile_Model_System_Config_Source_Email_Template{

    public function toOptionArray(){        
    	$options = array(); 
    	$options[] = array(
                'value' => '',
                'label' => ''
            );
    	$mailTemplate = Mage::getModel('core/email_template')->getCollection();
        foreach ($mailTemplate as $value) {
         	$options[] = array(
                'value' => $value->getTemplateId(),
                'label' => $value->getTemplateCode()
            );
        }
        $options[] = array(
            'value' => 999,
            'label' => "No Template"
        );
        return $options;
    }
}

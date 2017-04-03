<?php
class Iksula_Trustedcompany_Model_Plateform
{ 

   /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(    
            array('value' => 1, 'label'=>Mage::helper('adminhtml')->__('Magento')),
            array('value' => 2, 'label'=>Mage::helper('adminhtml')->__('Cheetah Mail')),
        );
    }
 
}

?> 


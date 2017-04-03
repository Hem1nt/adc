<?php 

class Mage_Adminhtml_Block_Customer_Edit_Tab_Medical extends Mage_Adminhtml_Block_Widget_Form
{
 
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('customer/tab/medical.phtml');
    }
 
}
?>
<?php 
Class Iksula_Callforoffer_Block_Adminhtml_Render_Email extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        if(!is_null($row->getData($this->getColumn()->getIndex()))) {
            $data = '';
        } 
        
        return $data;
    }
}
<?php
class Iksula_Extendedreview_Block_Adminhtml_Extendedreview_Renderer_Comment extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract{
     
    public function render(Varien_Object $row)
    {
    	echo "comment";exit;
        // $html = '<img ';
        // $html .= 'id="' . $this->getColumn()->getId() . '" ';
        // $html .= 'src="' . $row->getData($this->getColumn()->getIndex()) . '"';
        // $html .= 'class="grid-image ' . $this->getColumn()->getInlineCss() . '"/>';
        // return $html;
    }
}
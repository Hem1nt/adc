<?php  
class TS_Reports_Block_List_Pricesmini extends TS_Reports_Block_List_Container { 

    public function __construct(){	
        $this->_controller = 'list_pricesmini';
        $this->_headerText = $this->__('Prices list (mini)');
		parent::__construct();
    }  
	
} 
<?php
class TS_Reports_Block_List_Prices extends TS_Reports_Block_List_Container { 
     
    public function __construct(){	
        $this->_controller = 'list_prices';
        $this->_headerText = $this->__('Prices list');
		parent::__construct();
    }  
	
}  

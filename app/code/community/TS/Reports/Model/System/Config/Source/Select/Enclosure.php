<?php
 
class TS_Reports_Model_System_Config_Source_Select_Enclosure {

    public function toOptionArray(){
        return array(
            array(
                'value' => '\'',
                'label' => '\'',
            ),
            array(
                'value' => '"',
                'label' => '"',
            ),
        );
    }
	
}
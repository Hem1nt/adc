<?php
class Iksula_Configfile_Model_System_Config_Source_Elastic_Template{

    public function toOptionArray()
            {
            return array
                (
                  array('value' => '0', 'label' =>'Yes'),
                  array('value' => '1', 'label' => 'No')
                );
            }
}

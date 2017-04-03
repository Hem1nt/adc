<?php
class Iksula_Configfile_Model_System_Config_Source_Elastic_Options{

    public function toOptionArray()
            {
            return array
                (
                  array('value' => 'elastic', 'label' =>'ElasticSearch'),
                  array('value' => 'mysql', 'label' => 'Kaanvan Search')
                );
            }
}

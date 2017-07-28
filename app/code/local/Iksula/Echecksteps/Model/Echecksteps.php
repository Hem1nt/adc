<?php

class Iksula_Echecksteps_Model_Echecksteps extends Mage_Core_Model_Abstract
{
    protected function _construct(){

       $this->_init("echecksteps/echecksteps");

    }
    public function getecheckstepsData(){
    	return Mage::getModel('echecksteps/echecksteps')
    				->getCollection()
    				->setOrder('steps_order', 'ASC')
    				->getData();
    }

}
	 
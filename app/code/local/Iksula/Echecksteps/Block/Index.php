<?php   
class Iksula_Echecksteps_Block_Index extends Mage_Core_Block_Template{   

	public function getecheckData(){
		return Mage::getModel('echecksteps/echecksteps')->getecheckstepsData();
	}
}
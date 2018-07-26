<?php

class Iksula_Clicktoform_Adminhtml_ClicktoformController extends Mage_Adminhtml_Controller_Action
{		


		protected function _isAllowed()
		{
		    //return Mage::getSingleton('admin/session')->isAllowed('admin/cartsection');  
		    return true;  
		}
}
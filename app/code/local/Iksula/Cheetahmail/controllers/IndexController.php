<?php
class Iksula_Cheetahmail_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction(){      
       Mage::getModel('cheetahmail/cron')->buildDatabase();
        exit;  
    }
}
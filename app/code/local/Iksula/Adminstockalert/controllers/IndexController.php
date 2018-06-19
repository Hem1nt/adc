<?php
class Iksula_Adminstockalert_IndexController extends Mage_Core_Controller_Front_Action{

    public function sendCsvAction(){
      Mage::getModel('adminstockalert/cron')->notifyAdmin();
    }
}

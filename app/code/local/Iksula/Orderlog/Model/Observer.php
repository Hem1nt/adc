<?php

class Iksula_Orderlog_Model_Observer
{
    public function adminLog()
    {	//echo "vertika";
	    $prevpage = Mage::getSingleton("core/session")->getPreviouspagename();
    	$current_url = $_SERVER["REQUEST_URI"];

	    if(Mage::app()->getRequest()->getActionName() != "login"){

	    	Mage::getSingleton("core/session")->setCurrentpagename($current_url);
	    	$currentpage = Mage::getSingleton("core/session")->getCurrentpagename();

	    	if($prevpage != $currentpage && $current_url != ""){
		    	$id = Mage::getSingleton("core/session")->getUsersessionid();
		    	if(Mage::getSingleton("core/session")->getDefaultid() == 2)
	    			Mage::getModel("orderlog/usertimelog")->load($id)->setOutTime(now())->save();
		    	$model  = Mage::getModel("orderlog/usertimelog")
		    			->setuserName(Mage::getSingleton('admin/session')->getUser()->getUsername())
		    			->setPageName($current_url)
		    			->setIpAddress($_SERVER['REMOTE_ADDR'])
						->setEntryTime(time())
		    			->save();

		    	$id = $model->getId();
		    	$prev_page_name = $model->getPageName();
		    	Mage::getSingleton("core/session")->setDefaultid(2);
		    	Mage::getSingleton("core/session")->setPreviouspagename($prev_page_name);
		    	Mage::getSingleton("core/session")->setUsersessionid($id);
		    }

		}



        
    }

    public function adminLogin(){
    	$model  = Mage::getModel("orderlog/usertimelog")->getCollection();
	    $date = strtotime('-2 day',strtotime(now()));
	    foreach ($model as $modelvalue) {
	    	$id = $modelvalue->getId();
	    	$prevdate = strtotime($modelvalue->getDate());
	    	if($date > $prevdate){
	    		Mage::getModel("orderlog/usertimelog")->load($id)->delete();
	    	}
	    }
    	Mage::getSingleton("core/session")->setDefaultid(1);
    }
}
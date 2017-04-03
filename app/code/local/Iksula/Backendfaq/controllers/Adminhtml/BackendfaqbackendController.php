<?php
class Iksula_Backendfaq_Adminhtml_BackendfaqbackendController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
      {

       $this->loadLayout();
       $this->_title($this->__("Backend FAQ"));
       $this->renderLayout();

     }

        public function sendemailAction()
      {
          echo 'manoj';
            $params = Mage::app()->getRequest()->getParams();
            print_r($params);exit;

     }

    public function searchAction()
    {  

       $keyward = Mage::app()->getRequest()->getParam('search');
       $collection = Mage::getModel('backendfaq/backendfaq')->getCollection();
       $searchkeywords = explode(' ', $keyward);
       //echo $collection->getSelect(); exit;

       foreach ($searchkeywords as $value) {
          if($value) {
            //$filter_array[] = array(array('attribute' => 'question', 'like' => '%'.$value.'%'),array('attribute' => 'answer', 'like' => '%'.$value.'%'));
          $filter_a = array('like'=>'%'.$value.'%');
          $filter_b = array('like'=>'%'.$value.'%');
          }
       }

       // echo $filter_array[0][0]['like'].'<br/>';
       // echo $filter_array[0][1]['attribute'];
       //exit();
       /*$collection->addFieldToFilter(array(
                array('attribute'=>$filter_array[0][1]['attribute'], 'eq'=>$filter_array[0][0]['like']),
                array('attribute'=>$filter_array[0][0]['attribute'],'eq'=>$filter_array[0][1]['like']),
       ));*/

      // $filter_a = array('like'=>'%'.$value.'%');
      // $filter_b = array('like'=>'%'.$value.'%');


       // $collection->addFieldToFilter('question',$filter_array[0][0]);
       // $collection->addFieldToFilter('answer',$filter_array[0][1]);

      /*foreach ($searchkeywords as $value) {
          if($value) {
            $filter_array[] = array('attribute' => 'question', 'like' => '%'.$value.'%');
          }
       }*/
      
      $collection->addFieldToFilter(array('question', 'answer'),array( $filter_a, $filter_b));       
        // echo '<pre>'; print_r($filter_array);
        // echo $collection->getSelect(); exit; 
        $html = '<table class="tablegrid">';
        $html.='<tr class="maincols">';
        $html.='<td> Question </td>';
        $html.='<td>Answer</td>';
        $html.='<td>Action</td>';
        $html.='</tr>';   
        foreach ($collection->getData() as $value) {
        $baseurl = $this->getUrl("*/adminhtml_backendfaq/edit", array("id" => $value['id']));
            
     			$html.='<tr class="othercols">';
    			$html.='<td>'.$value['question'].'</td>';
    			$html.='<td>'.$value['answer'].'</td>';
    			$html.='<td><a href="'.$baseurl.'">Edit</a></td>';
    			$html.='</tr>';
          
       }
       $html.='</table>';
       echo $html;
    }
}
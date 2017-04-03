<?php

class Iksula_Faqsection_Block_Adminhtml_Faqquestions_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("faqquestionsGrid");
				$this->setDefaultSort("question_id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("faqsection/faqquestions")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				// $this->addColumn("question_id", array(
				// "header" => Mage::helper("faqsection")->__("ID"),
				// "align" =>"right",
				// "width" => "20px",
			 //    "type" => "number",
				// "index" => "question_id",
				// ));
             
				$this->addColumn('sections_id', array(
				'header' => Mage::helper('faqsection')->__('Section Id'),
				'index' => 'sections_id',
				'type' => 'options',
				"width" => "100px",
				'options'=>Iksula_Faqsection_Block_Adminhtml_Faqquestions_Grid::getOptionArray5(),				
				));
				$this->addColumn("question", array(
				"header" => Mage::helper("faqsection")->__("question"),
				"align" =>"left",
				"width" => "100px",
				"index" => "question",
				));
				// $this->addColumn("answer", array(
				// "header" => Mage::helper("faqsection")->__("answer"),
				// "align" =>"right",
				// "width" => "50px",
				// "index" => "answer",
				// ));
			$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV')); 
			$this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

				return parent::_prepareColumns();
		}

		public function getRowUrl($row)
		{
			   return $this->getUrl("*/*/edit", array("id" => $row->getId()));
		}


		
		protected function _prepareMassaction()
		{
			$this->setMassactionIdField('question_id');
			$this->getMassactionBlock()->setFormFieldName('question_ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_faqquestions', array(
					 'label'=> Mage::helper('faqsection')->__('Remove Faqquestions'),
					 'url'  => $this->getUrl('*/adminhtml_faqquestions/massRemove'),
					 'confirm' => Mage::helper('faqsection')->__('Are you sure?')
				));
			return $this;
		}

		static public function getOptionArray5()
		{
            $data_array=array(); 
			$faqsectionCollection = Mage::getModel('faqsection/faqsection')->getCollection();
			foreach ($faqsectionCollection as $faqquestions_key => $faqquestions_val) {
				$data_array[$faqquestions_val->getsectionsTypeid()] = $faqquestions_val->gettypeTitle();
			}
            return($data_array);
		}
		static public function getValueArray5()
		{
            $data_array=array();
			foreach(Iksula_Faqsection_Block_Adminhtml_Faqquestions_Grid::getOptionArray5() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}

}
<?php

class Iksula_Trustedcompany_Block_Adminhtml_Reviews_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("reviewsGrid");
				$this->setDefaultSort("review_id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("trustedcompany/reviews")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("review_id", array(
				"header" => Mage::helper("trustedcompany")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "review_id",
				));
                
				$this->addColumn("review_made_by", array(
				"header" => Mage::helper("trustedcompany")->__("Reviewer Email "),
				"index" => "review_made_by",
				));
				$this->addColumn("rating", array(
				"header" => Mage::helper("trustedcompany")->__("Rating"),
				"index" => "rating",
				));
				$this->addColumn("reviewer", array(
				"header" => Mage::helper("trustedcompany")->__("Reviewer"),
				"index" => "reviewer",
				));
					$this->addColumn('date', array(
						'header'    => Mage::helper('trustedcompany')->__('Date'),
						'index'     => 'date',
						'type'      => 'datetime',
					));
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
			$this->setMassactionIdField('review_id');
			$this->getMassactionBlock()->setFormFieldName('review_ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_reviews', array(
					 'label'=> Mage::helper('trustedcompany')->__('Remove Reviews'),
					 'url'  => $this->getUrl('*/adminhtml_reviews/massRemove'),
					 'confirm' => Mage::helper('trustedcompany')->__('Are you sure?')
				));
			return $this;
		}
			

}
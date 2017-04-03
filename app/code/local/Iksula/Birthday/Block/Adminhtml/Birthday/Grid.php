<?php

class Iksula_Birthday_Block_Adminhtml_Birthday_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("birthdayGrid");
				$this->setDefaultSort("birthday_id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("birthday/birthday")->getCollection();
				$collection->getSelect()->join( array('customer_data'=> 'customer_entity'), 'customer_data.entity_id = main_table.customer_id', array('customer_data.created_at'));
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("birthday_id", array(
				"header" => Mage::helper("birthday")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "birthday_id",
				));
                
				$this->addColumn("customer_id", array(
				"header" => Mage::helper("birthday")->__("Customer Id"),
				"index" => "customer_id",
				));

				$this->addColumn("created_at", array(
				"header" => Mage::helper("birthday")->__("Customer Created Date"),
				"index" => "created_at",
				'type'      => 'datetime',
				));

				$this->addColumn("first_name", array(
				"header" => Mage::helper("birthday")->__("First Name"),
				"index" => "first_name",
				));
				$this->addColumn("last_name", array(
				"header" => Mage::helper("birthday")->__("Last Name"),
				"index" => "last_name",
				));
				// $this->addColumn("email", array(
				// "header" => Mage::helper("birthday")->__("Email"),
				// "index" => "email",
				// ));
				$admin_user_session = Mage::getSingleton('admin/session');
				$adminuserId = $admin_user_session->getUser()->getUserId();
				$role_data = Mage::getModel('admin/user')->load($adminuserId)->getRole()->getData();
				$role_name = $role_data['role_name'];

				if($role_name=='Administrators'){
					$this->addColumn('email', array(
						'header'    => Mage::helper('birthday')->__('Email'),
						'width'     => '100px',
						'index'     => 'email',
						'filter_index'=>'main_table.email'
						));
				}else{
					$this->addColumn('email', array(
						'header'    => Mage::helper('birthday')->__('Subscriber Email'),
						'width'     => '100px',
						'index'     => 'email',
						'filter_index'=>'main_table.email',
						'renderer'  => 'Iksula_Bithday_Block_Adminhtml_Render_Email',
						));
				}

				$this->addColumn("coupon", array(
				"header" => Mage::helper("birthday")->__("Coupon"),
				"index" => "coupon",
				));
					$this->addColumn('anniversary', array(
						'header'    => Mage::helper('birthday')->__('Anniversary'),
						'index'     => 'anniversary',
						'type'      => 'datetime',
					));
					
					// $this->addColumn("customer_group", array(
					// "header" => Mage::helper("birthday")->__("Customer Group"),
					// "index" => "customer_group",
					// ));

					$this->addColumn('coupon_created_date', array(
						'header'    => Mage::helper('birthday')->__('Coupon Created Date'),
						'index'     => 'coupon_created_date',
						'type'      => 'datetime',
					));
					$this->addColumn('coupon_expire_date', array(
						'header'    => Mage::helper('birthday')->__('Coupon Expire Date'),
						'index'     => 'coupon_expire_date',
						'type'      => 'datetime',
					));
						$this->addColumn('coupon_status', array(
						'header' => Mage::helper('birthday')->__('Coupon Status'),
						'index' => 'coupon_status',
						'type' => 'options',
						'options'=>Iksula_Birthday_Block_Adminhtml_Birthday_Grid::getOptionArray11(),				
						));
						
					$this->addColumn('date_mail_to_send', array(
						'header'    => Mage::helper('birthday')->__('Date Mail to be Send'),
						'index'     => 'date_mail_to_send',
						'type'      => 'datetime',
					));
				$this->addColumn("email_send", array(
				"header" => Mage::helper("birthday")->__("Email send Status"),
				"index" => "email_send",
				));
				$this->addColumn("no_of_email_send", array(
				"header" => Mage::helper("birthday")->__("No of Email Send"),
				"index" => "no_of_email_send",
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
			$this->setMassactionIdField('birthday_id');
			$this->getMassactionBlock()->setFormFieldName('birthday_ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_birthday', array(
					 'label'=> Mage::helper('birthday')->__('Remove Birthday'),
					 'url'  => $this->getUrl('*/adminhtml_birthday/massRemove'),
					 'confirm' => Mage::helper('birthday')->__('Are you sure?')
				));
			return $this;
		}
			
		static public function getOptionArray11()
		{
            $data_array=array(); 
			$data_array[0]='Disable';
			$data_array[1]='Enable';
            return($data_array);
		}
		static public function getValueArray11()
		{
            $data_array=array();
			foreach(Iksula_Birthday_Block_Adminhtml_Birthday_Grid::getOptionArray11() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		

}
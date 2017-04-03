<?php

class Iksula_OutofStockSubscription_Block_Adminhtml_Subcriber_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('subcribersGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    public function _prepareCollection()
    {
        $collection = Mage::getModel('outofstocksubscription/info')->getCollection();
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    public function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    => Mage::helper('outofstocksubscription')->__('ID'),
            'width'     => '50px',
            'index'     => 'id',
            'type'      => 'number'
        ));

        $admin_user_session = Mage::getSingleton('admin/session');
        $adminuserId = $admin_user_session->getUser()->getUserId();
        $role_data = Mage::getModel('admin/user')->load($adminuserId)->getRole()->getData();
        $role_name = $role_data['role_name'];
            
        if($role_name=='Administrators'){
            $this->addColumn('email', array(
                'header'    => Mage::helper('outofstocksubscription')->__('Subscriber Email'),
                'width'     => '100px',
                'index'     => 'email',
                // 'renderer'  => 'Iksula_OutofStockSubscription_Block_Adminhtml_Render_Email',
            ));
        }else{
            $this->addColumn('email', array(
                'header'    => Mage::helper('outofstocksubscription')->__('Subscriber Email'),
                'width'     => '100px',
                'index'     => 'email',
                'renderer'  => 'Iksula_OutofStockSubscription_Block_Adminhtml_Render_Email',
            ));
        }

        $this->addColumn('product_sku', array(
            'header'    => Mage::helper('outofstocksubscription')->__('SKU'),
            'width'     => '100px',
            'index'     => 'product_sku',
        ));

        $this->addColumn('product_name', array(
            'header'    => Mage::helper('outofstocksubscription')->__('Product Name'),
            'width'     => '100px',
            'index'     => 'product_name',
        ));

        $this->addColumn('notification_type', array(
            'header'    => Mage::helper('outofstocksubscription')->__('Notification Type'),
            'width'     => '100px',
            'index'     => 'notification_type',
        ));

         $this->addColumn('is_active', array(
            'header'    => Mage::helper('outofstocksubscription')->__('Status'),
            'width'     => '100px',
            'index'     => 'is_active',
        ));

        $this->addColumn('date', array(
            'header' => Mage::helper('outofstocksubscription')->__('Subscribed On'),
            'index' => 'date',
            'type' => 'datetime',
            'width' => '100px',
        ));

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('outofstocksubscription')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('outofstocksubscription')->__('Delete'),
                        'url'     => array(
                            'base'=>'outofstocksubscription/adminhtml_subscriber/delete'
                        ),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV')); 
        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));
        
        return parent::_prepareColumns();
    }


        
     protected function _prepareMassaction()
        {
            $this->setMassactionIdField('id');
            $this->getMassactionBlock()->setFormFieldName('ids');
            $this->getMassactionBlock()->setUseSelectAll(true);
            $this->getMassactionBlock()->addItem('remove_outofstocksubscription', array(
                     'label'=> Mage::helper('outofstocksubscription')->__('Remove Subscription'),
                     'url'  => $this->getUrl('*/adminhtml_subscriber/massRemove'),
                     'confirm' => Mage::helper('outofstocksubscription')->__('Are you sure?')
                ));
            return $this;
        }

}

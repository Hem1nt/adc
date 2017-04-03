<?php
$this->startSetup();
// Add column to grid table
$this->getConnection()->addColumn(
    $this->getTable('sales/order_grid'),
    'country_id',
    "varchar(255) not null default ''"
);
// Add key to table for this field,
// it will improve the speed of searching & sorting by the field
$this->getConnection()->addKey(
    $this->getTable('sales/order_grid'),
    'country_id',
    'country_id'
);
// Now you need to fullfill existing rows with data from address table
$select = $this->getConnection()->select();
$select->join(
    array('address'=>$this->getTable('sales/order_address')),
    $this->getConnection()->quoteInto(
        'address.parent_id = order_grid.entity_id AND address.address_type = ?',
        Mage_Sales_Model_Quote_Address::TYPE_BILLING
    ),
    array('country_id' => 'country_id')
);
$this->getConnection()->query(
    $select->crossUpdateFromSelect(
        array('order_grid' => $this->getTable('sales/order_grid'))
    )
);
$this->endSetup();

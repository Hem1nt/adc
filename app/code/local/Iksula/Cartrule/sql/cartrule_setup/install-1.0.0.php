<?php
$installer = $this;
$installer->startSetup();
$tableName = $installer->getTable('salesrule/rule');
$columnOptions = array(
    'TYPE'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
    'SCALE'     => 4,
    'PRECISION' => 12,
    'NULLABLE'  => false,
    'DEFAULT'   => '0.0000',
    'COMMENT'   => 'Discount Amount For maximum amount',
);
$installer->getConnection()->addColumn($tableName, 'max_discount_amount', $columnOptions);
$installer->endSetup();
<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();
$conn = $installer->getConnection();

$select = $conn
    ->select()
    ->from($this->getTable('core/config_data'), array('scope', 'scope_id', 'path', 'value'))
    ->where(new Zend_Db_Expr("path LIKE 'iksula/bitcoins%'"));
$data = $conn->fetchAll($select);

if (!empty($data)) {
    foreach ($data as $key => $value) {
        $data[$key]['path'] = preg_replace('/^bitcoins\/bitcoins/', 'payment/bitcoins', $value['path']);
    }
    $conn->insertOnDuplicate($this->getTable('core/config_data'), $data, array('path'));
    $conn->delete($this->getTable('core/config_data'), new Zend_Db_Expr("path LIKE 'iksula/bitcoins%'"));
}

$installer->endSetup();

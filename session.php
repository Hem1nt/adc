<?php 
ini_set("memory_limit","1000M");
require_once "app/Mage.php";
umask(0);
Mage::app();
echo 'manoj';
print_r($_SESSION);

?>
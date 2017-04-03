<?php
$installer = $this;
$installer->startSetup();
	$installer->run("
		ALTER TABLE simple_testimonial ADD rating int(11);
	");
$installer->endSetup(); 

?>

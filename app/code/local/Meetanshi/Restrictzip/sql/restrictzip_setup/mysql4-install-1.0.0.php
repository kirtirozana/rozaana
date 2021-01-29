<?php
$installer = $this;
$installer->startSetup();
$installer->run(
    "
        CREATE TABLE IF NOT EXISTS {$this->getTable('restrictzip_zip')} (
			`zip_code_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `zip_code` varchar(15) NOT NULL,
			`estimate_del_time` varchar(25) NOT NULL,
			PRIMARY KEY (`zip_code_id`)
        ) ENGINE = InnoDB DEFAULT CHARSET = utf8;
    "
);
$installer->endSetup();

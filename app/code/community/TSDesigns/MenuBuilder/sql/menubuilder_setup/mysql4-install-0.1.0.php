<?php

/**
 * MenuBuilder install
 *
 * @menu   		TSDesigns
 * @package    	TSDesigns_MenuBuilder
 * @author     	Tobias Schifftner <tobias@schifftner.de>
 */

$installer = $this;
/* @var $installer TSDesigns_MenuBuilder_Model_Entity_Setup */

$installer->startSetup();

if (!$installer->tableExists($installer->getTable('menubuilder_entity'))) {

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('menubuilder_entity')};
CREATE TABLE {$this->getTable('menubuilder_entity')} (
  `entity_id` int(10) unsigned NOT NULL auto_increment,
  `menu_code` varchar(255) NOT NULL default '',
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_set_id` smallint(5) unsigned NOT NULL default '0',
  `parent_id` int(10) unsigned NOT NULL default '0',
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `path` varchar(255) NOT NULL,
  `position` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `children_count` int(11) NOT NULL,
  PRIMARY KEY  (`entity_id`),
  KEY `IDX_LEVEL` (`level`),
  KEY `FK_MENUBUILDER_ENTITY_ENTITY_TYPE` (`entity_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Menu Entities';

-- DROP TABLE IF EXISTS {$this->getTable('menubuilder_entity_datetime')};
CREATE TABLE {$this->getTable('menubuilder_entity_datetime')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`value_id`),
  UNIQUE KEY `IDX_BASE` (`entity_type_id`,`entity_id`,`attribute_id`,`store_id`),
  KEY `FK_ATTRIBUTE_DATETIME_ENTITY` (`entity_id`),
  KEY `FK_MENUBUILDER_ENTITY_DATETIME_ATTRIBUTE` (`attribute_id`),
  KEY `FK_MENUBUILDER_ENTITY_DATETIME_STORE` (`store_id`),
  CONSTRAINT `FK_MENUBUILDER_ENTITY_DATETIME_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES {$this->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_MENUBUILDER_ENTITY_DATETIME_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES {$this->getTable('menubuilder_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_MENUBUILDER_ENTITY_DATETIME_STORE` FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('menubuilder_entity_decimal')};
CREATE TABLE {$this->getTable('menubuilder_entity_decimal')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`value_id`),
  UNIQUE KEY `IDX_BASE` (`entity_type_id`,`entity_id`,`attribute_id`,`store_id`),
  KEY `FK_ATTRIBUTE_DECIMAL_ENTITY` (`entity_id`),
  KEY `FK_MENUBUILDER_ENTITY_DECIMAL_ATTRIBUTE` (`attribute_id`),
  KEY `FK_MENUBUILDER_ENTITY_DECIMAL_STORE` (`store_id`),
  CONSTRAINT `FK_MENUBUILDER_ENTITY_DECIMAL_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES {$this->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_MENUBUILDER_ENTITY_DECIMAL_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES {$this->getTable('menubuilder_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_MENUBUILDER_ENTITY_DECIMAL_STORE` FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('menubuilder_entity_int')};
CREATE TABLE {$this->getTable('menubuilder_entity_int')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`value_id`),
  UNIQUE KEY `IDX_BASE` (`entity_type_id`,`entity_id`,`attribute_id`,`store_id`),
  KEY `FK_ATTRIBUTE_INT_ENTITY` (`entity_id`),
  KEY `FK_MENUBUILDER_EMTITY_INT_ATTRIBUTE` (`attribute_id`),
  KEY `FK_MENUBUILDER_EMTITY_INT_STORE` (`store_id`),
  CONSTRAINT `FK_MENUBUILDER_EMTITY_INT_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES {$this->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_MENUBUILDER_EMTITY_INT_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES {$this->getTable('menubuilder_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_MENUBUILDER_EMTITY_INT_STORE` FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('menubuilder_entity_text')};
CREATE TABLE {$this->getTable('menubuilder_entity_text')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`value_id`),
  UNIQUE KEY `IDX_BASE` (`entity_type_id`,`entity_id`,`attribute_id`,`store_id`),
  KEY `FK_ATTRIBUTE_TEXT_ENTITY` (`entity_id`),
  KEY `FK_MENUBUILDER_ENTITY_TEXT_ATTRIBUTE` (`attribute_id`),
  KEY `FK_MENUBUILDER_ENTITY_TEXT_STORE` (`store_id`),
  CONSTRAINT `FK_MENUBUILDER_ENTITY_TEXT_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES {$this->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_MENUBUILDER_ENTITY_TEXT_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES {$this->getTable('menubuilder_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_MENUBUILDER_ENTITY_TEXT_STORE` FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('menubuilder_entity_varchar')};
CREATE TABLE {$this->getTable('menubuilder_entity_varchar')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`value_id`),
  UNIQUE KEY `IDX_BASE` USING BTREE (`entity_type_id`,`entity_id`,`attribute_id`,`store_id`),
  KEY `FK_ATTRIBUTE_VARCHAR_ENTITY` (`entity_id`),
  KEY `FK_MENUBUILDER_ENTITY_VARCHAR_ATTRIBUTE` (`attribute_id`),
  KEY `FK_MENUBUILDER_ENTITY_VARCHAR_STORE` (`store_id`),
  CONSTRAINT `FK_MENUBUILDER_ENTITY_VARCHAR_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES {$this->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_MENUBUILDER_ENTITY_VARCHAR_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES {$this->getTable('menubuilder_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_MENUBUILDER_ENTITY_VARCHAR_STORE` FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");
}

$installer->endSetup();
$installer->installEntities();

$installer->startSetup();

$entityTypeId     = $installer->getEntityTypeId('menubuilder');
$attributeSetId   = $installer->getDefaultAttributeSetId($entityTypeId);

$query = <<<EOT
INSERT INTO {$this->getTable('menubuilder_entity')} 
(`entity_id`, `menu_code`, `entity_type_id`, `attribute_set_id`, `parent_id`, `created_at`, `updated_at`, `path`, `position`, `level`, `children_count`) VALUES 
(1, '', {$installer->getEntityTypeId('menubuilder')}, 0, 0, NOW(), NOW(), '1', 0, 0, 0);
EOT;

$installer->run($query);
$installer->endSetup();

/** @var TSDesigns_MenuBuilder_Model_Menu $menubuilder */
$menubuilder = Mage::getModel('menubuilder/menu');

$menubuilder->setData(array(
    'menu_code' => 'default',
    'parent_id' => 0,
    'path' => 1,
    'name' => 'Default Menu',
    'store_id' => 0,
));

$menubuilder->save();

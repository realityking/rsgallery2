# in some version prior to 1.12.2 #__rsgallery2_acl was hardcoded with the prefix jos.
# if Joomla! was installed using a different prefix then #__rsgallery2_acl will be missing.
#
# see includes/install.class.php:migrate_com_rsgallery::upgradeTo_1_12_2()

CREATE TABLE IF NOT EXISTS `#__rsgallery2_acl` (
  `id` int(11) NOT NULL auto_increment,
  `gallery_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL default '0',
  `public_view` tinyint(1) NOT NULL default '1',
  `public_up_mod_img` tinyint(1) NOT NULL default '0',
  `public_del_img` tinyint(1) NOT NULL default '0',
  `public_create_mod_gal` tinyint(1) NOT NULL default '0',
  `public_del_gal` tinyint(1) NOT NULL default '0',
  `registered_view` tinyint(1) NOT NULL default '1',
  `registered_up_mod_img` tinyint(1) NOT NULL default '1',
  `registered_del_img` tinyint(1) NOT NULL default '0',
  `registered_create_mod_gal` tinyint(1) NOT NULL default '1',
  `registered_del_gal` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;
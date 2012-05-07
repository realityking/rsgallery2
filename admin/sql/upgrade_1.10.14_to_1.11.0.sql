###
# upgrade db from RSGallery2 1.10.2 - 1.10.14 to 1.11.0
###

# some dev builds had this table, so we delete it first
DROP TABLE IF EXISTS `#__rsgallery2_galleries`;

# alter the table.  make it nice and shiny

ALTER TABLE `#__rsgallery2_cats` RENAME TO `#__rsgallery2_galleries`;
ALTER TABLE `#__rsgallery2_galleries` MODIFY `id` int(11) NOT NULL auto_increment;
ALTER TABLE `#__rsgallery2_galleries` MODIFY `parent` int(11) NOT NULL default 0 AFTER `id`;
ALTER TABLE `#__rsgallery2_galleries` CHANGE `catname` `name` varchar(255) NOT NULL default '' AFTER `parent`;
ALTER TABLE `#__rsgallery2_galleries` MODIFY `description` text NOT NULL AFTER `name`;
ALTER TABLE `#__rsgallery2_galleries` MODIFY `published` tinyint(1) NOT NULL default '0' AFTER `description`;
ALTER TABLE `#__rsgallery2_galleries` ADD `checked_out` int(11) unsigned NOT NULL default '0' AFTER `published`;
ALTER TABLE `#__rsgallery2_galleries` ADD `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00' AFTER `checked_out`;
ALTER TABLE `#__rsgallery2_galleries` MODIFY `ordering` int(11) signed NOT NULL default '0' AFTER `checked_out_time`;
ALTER TABLE `#__rsgallery2_galleries` ADD `date` datetime NOT NULL default '0000-00-00 00:00:00' AFTER `ordering`;
ALTER TABLE `#__rsgallery2_galleries` MODIFY `hits` int(11) NOT NULL default '0' AFTER `date`;
ALTER TABLE `#__rsgallery2_galleries` ADD `params` text NOT NULL AFTER `hits`;
ALTER TABLE `#__rsgallery2_galleries` MODIFY `user` tinyint(4) NOT NULL default '0' AFTER `params`;
ALTER TABLE `#__rsgallery2_galleries` MODIFY `uid` int(11) unsigned NOT NULL default '0' AFTER `user`;
ALTER TABLE `#__rsgallery2_galleries` MODIFY `allowed` varchar(100) NOT NULL default '0' AFTER `uid`;
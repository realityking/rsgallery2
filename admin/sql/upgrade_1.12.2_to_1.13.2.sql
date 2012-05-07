###
# upgrade db from RSGallery2 1.12.12 to 1.13.2
###

# in 1.13.2 a new comments system is introduced
# The #__rsgallery2_comments table is altered 

ALTER TABLE `#__rsgallery2_comments` MODIFY `id` int(11) NOT NULL auto_increment;
ALTER TABLE `#__rsgallery2_comments` ADD `user_id` int(11) NOT NULL AFTER `id`;
ALTER TABLE `#__rsgallery2_comments` CHANGE `name` `user_name` varchar(100) NOT NULL AFTER `user_id`;
ALTER TABLE `#__rsgallery2_comments` ADD `user_ip` varchar(50) NOT NULL default '0.0.0.0' AFTER `user_name`;
ALTER TABLE `#__rsgallery2_comments` ADD `parent_id` int(11) NOT NULL default '0' AFTER `user_ip`;
ALTER TABLE `#__rsgallery2_comments` CHANGE `picid` `item_id` int(11) NOT NULL AFTER `parent_id`;
ALTER TABLE `#__rsgallery2_comments` ADD `item_table` varchar(50) NULL AFTER `item_id`;
ALTER TABLE `#__rsgallery2_comments` CHANGE `date` `datetime` datetime NOT NULL default '0000-00-00 00:00:00' AFTER `item_table`;
ALTER TABLE `#__rsgallery2_comments` ADD `subject` varchar(100) NOT NULL default '' AFTER `datetime`;
ALTER TABLE `#__rsgallery2_comments` ADD `published` tinyint(1) NOT NULL default '1' AFTER `comment`;
ALTER TABLE `#__rsgallery2_comments` ADD `checked_out` int(11) NOT NULL default '0' AFTER `published`;
ALTER TABLE `#__rsgallery2_comments` ADD `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00' AFTER `checked_out`;
ALTER TABLE `#__rsgallery2_comments` ADD `ordering` int(11) NOT NULL AFTER `checked_out_time`;
ALTER TABLE `#__rsgallery2_comments` ADD `params` text AFTER `ordering`;
ALTER TABLE `#__rsgallery2_comments` ADD `hits` int(11) NOT NULL AFTER `params`;
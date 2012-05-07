###
# upgrade db from RSGallery2 1.11.11 to 1.12.0
###

#  in 1.12.0 a new image class is introduced
# this requires extra fields in the table


ALTER TABLE `#__rsgallery2_files` ADD `published` tinyint(1) NOT NULL default '1' AFTER `comments`;
ALTER TABLE `#__rsgallery2_files` ADD `checked_out` int(11) NOT NULL default '0' AFTER `published`;
ALTER TABLE `#__rsgallery2_files` ADD `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00' AFTER `checked_out`;
ALTER TABLE `#__rsgallery2_files` ADD `params` text;
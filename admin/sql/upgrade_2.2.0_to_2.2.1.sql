# In version 2.2.0 there was no field 'alias' for galleries and items
# We want to implement 'alias' in v3.0.0 and perhaps 2.2.1: alter the tables

ALTER TABLE `#__rsgallery2_files` ADD `alias` varchar(255) NOT NULL DEFAULT '' AFTER `title`;
ALTER TABLE `#__rsgallery2_galleries` ADD `alias` varchar(255) NOT NULL DEFAULT '' AFTER `name`;
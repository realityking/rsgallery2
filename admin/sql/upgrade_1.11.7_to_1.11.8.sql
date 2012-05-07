###
# upgrade db from RSGallery2 1.11.7 to 1.11.8
###

# Versions up to 1.11.7 did not have a field
# called 'thumb_id' in #__rsgallery2_galleries
# to store image id for selected thumbnail

ALTER TABLE `#__rsgallery2_galleries` ADD `thumb_id` int(11) unsigned NOT NULL default '0' AFTER `allowed`;
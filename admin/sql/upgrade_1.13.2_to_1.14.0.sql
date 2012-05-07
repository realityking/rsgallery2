###
# upgrade db from RSGallery2 1.13.2 to 1.14.0
###

# template Tables has been deprecated in favor of Semantic

UPDATE `#__rsgallery2_config` SET `value` = 'semantic' WHERE `name` = 'template';
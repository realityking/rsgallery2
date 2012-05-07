###
# upgrade db from RSGallery2 1.11.7 to 1.11.8
###

# 1.11.0 created #__rsgallery2_cats by mistake on new installs.  this removes it.

DROP TABLE IF EXISTS `#__rsgallery2_cats`;



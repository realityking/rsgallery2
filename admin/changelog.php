<?php
/**
* Changelog for RSGallery2
* @version $Id: changelog.php 1070 2012-03-25 11:40:51Z mirjam $
* @package RSGallery2
* @copyright (C) 2003 - 2011 RSGallery2
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* RSGallery is Free Software
**/

defined('_JEXEC') or die;
?>

Check for the latest version of RSGallery2 at http://www.rsgallery2.nl/

1. Changelog
------------
This is a non-exhaustive but informative changelog for
RSGallery2, including alpha, beta and stable releases.
Our thanks to all those people who've contributed bug reports and
code fixes.

Legend:

* -> Security Fix
# -> Bug Fix
+ -> Addition
^ -> Change
- -> Removed
! -> Note

---------------- Recent ----------------

2012-03-25 Mirjam - SVN 1070
# List limit was "All" by default, now takes the Global Configuration List Length
# Frontend My Galleries showed only two levels deep: now up to 20 levels.

---------------- 2.2.1 ------- svn 1044 -- 2011-09-19 -------------

2011-09-19 Mirjam - SVN 1042/1043
^ MySQL 5.5 doesn't support TYPE: changed TYPE=MyISAM to ENGINE=MyISAM 
+ Added upgrade_2.2.0_to_2.2.1.sql to allow for database table change (add columns 'alias')

2011-05-04 Mirjam - SVN 1024
# galleryUtils::showCategories: fixed 'global $database' ($database =& JFactory::getDBO();)
# Corrected path to galleries.item.xml file for gallery parameters. 

2011-01-28 Mirjam - SVN 1013
+ Added SEF option: advanced SEF with using aliases that don't need to be unique since the id's are used as well. There are three 'Advanced SEF' options now: no, yes, yes... where the third is the new option.
# Adjusted pathway so that item name is only added when item is shown
^ Bumped version number to 2.2.1 (to allow db update for alias)

2011-01-26 Mirjam
# Fixed "Notice: Array to string conversion in /.../administrator/components/com_rsgallery2/includes/config.class.php on line 182"
# Description now shows in Slideshow Parth

2011-01-21 Mirjam - SVN 1009
# Watermark display image was showing original image
+ Added "My galleries" breadcrumb to pathway
# SEF (not advancedSEF) showed error when deleting images in 'My galleries'

2011-01-21 Mirjam - SVN 1008
# System gave a fatal error if a user requested an item that did not exist: changed into JError::raiseError
+ Menu items translatable: menu language file included for english en-GB and dutch nl-NL
# Javascript alerts now translatable and has improved errorhandling
# Fixed breadcrumb pathway: showing correct gallery and item
^ MyGalleries message "User galleries was disabled by the administrator" changed into "You need to log on to access 'My galleries'"

2010-12-28 Mirjam - SVN 1003
# Corrected pathway: show correct gallery, depending on menulink, and added item to pathway.
# (De)installation now uses RSGallery2's language files: strings are translatable
^ A few language keys were changed: e.g. because of an _ at the start or a space (' ') at the start/end of the key.
^ Updated several dutch and a few english language strings
^ Updated all available language files to show what language/country the language/country code is for
- Removed 'hide root' option because it is not used

2010-11-18 Mirjam
+ Added license tag to manifest.

2010-10-25 Mirjam - SVN 995
# Backend list of items showed all items instead of number set in Joomla's Global Config. [#23214]
+ In backend view of all galleries an image is added that links to the backend view of images, filtered on that gallery.
# Instead of the fi-FI language fr-FR was installed: corrected.

---------------- 2.2.0 ------- svn 993 -- 2010-11-04 -------------

2010-10-25 Mirjam - SVN 992
! When using JoomFish and Joomla SEF the message that a gallery/image name is not unique is given even when the gallery/image does exist with a unique name. This problem is not solved, but the error messages are improved: JoomFish does not return which gallery/image is used so a 'gallery/image not found' message is shown instead of a 'gallery/image not unique' message.
# RSG2 now handles galleries with an ' in the name, when Joomla SEF is used.

2010-10-20 Mirjam
! Domain rsgallery2.net was lost to the RSGallery2 Team in september 2010.
  SVN and File Releases are still on http://joomlacode.org/gf/project/rsgallery2
  where packages now show j10 for Joomla!1.0 and j15 for Joomla!1.5.
^ New domain www.rsgallery2.nl will be used from now on, changed in the code.
^ New mailaddress will be used: team@rsgallery2.nl instead of info@rsgallery2.net.
  (Note: support goes through the forum, not by e-mail!)


2010-10-20 Mirjam
# Order/reorder in Backend Galleries now works correctly and reorder images show only when they can be used
^ Manifest rsgallery2.xml now used method="upgrade" so uninstalling RSG2 and revisiting its menu-items after installation is no longer neccessary. It writes over all existing files mentioned in the xml, so backing up of any file changes, e.g. to the existing template, is needed.

2010-08-22 Mirjam - SVN 989
^ Updated german and french language file (thanks to Günter Hoffman)

2010-05-20 Mirjam - SVN 986
+ Added setting useIPTCinformation (default off): choice of using image IPTC information for title and description when uploading images (single file/batch upload).

2010-04-09 Mirjam - SVN 985
# Changed all Help buttons to JToolBarHelper::help( 'screen.rsgallery2',true); and moved the screen.rsgallery2.html file to the correct folder /help/en-GB/ to be accessable: now all Help buttons are functional with updated information.

2010-03-30 Mirjam - SVN 983
+ Added menutree option to choose gallery tied to the menu item from list of galleries, including root gallery
# Changed router.php to correctly get gid when menuitem shows (sub)gallery and not rootgallery (otherwise resulting in hit() error)
+ Updated rsgallery2.xml to include views directory (thus now also installing Jonahs display-view from SVN955, but hidden through the metadata.xml settings)

2010-03-17 Mirjam - SVN 982
# Added if !defined('JPATH_RSGALLERY2_ADMIN'){} to avoid notice because when SEF is used this constant is defined in two places

2010-03-13 Mirjam - SVN 981
# Fix on resize of portrait images with DB: GD::resize portrait images now based on input $targetWidth, 
	not on display size (before svn 965) or thumb size (since 965). 
# Small fixes in function createImages() in maintenance.php.

---------------- 2.1.1 ------- svn 978 -- 2010-03-04 -------------

2010-03-02 Mirjam - SVN 976
^ Changed use of PEAR_Error to JError::raiseNotice and return false instead of new PEAR_Error (PEAR_Error is PHP4 not PHP5 and deprecated in Joomla 1.6)
# Backend display item moved inside 'case item->type' for image: was error for type mp3
+ Added JText language strings for error handling

2010-02-26 Mirjam - SVN 975
^ getSiteURL() and getPath() are deprecated in J1.5 (and removed from J1.6): changed to J1.5 native methods

2010-02-   Mirjam - SVN 974
# Fixed gallery and item names and descriptions in backend and frontend - last part
! Now special characters can by used in My Galleries 'new' and 'save'

2010-02-24 Mirjam - SVN 971
# Fixed gallery and item names and descriptions in backend and frontend, e.g. multiplying \ on save
! All names in database are not escaped, escaping is done just befor insert query
	or in store() of object
! Item and gallery descriptions are in JREQUEST_ALLOWRAW format

2010-02-18 Mirjam - SVN 968
# Ability to edit and delete galleries fixed in frontend My Galleries - use gid not catid
! gallery list to choose from is unaware of which user is logged on, function galleryUtils::galleriesSelectList is used for front- and backend

2010-02-16 Mirjam - SVN 967
# filenames with spaces were not allowed when regenerating proportional images with GD2
# portrait images were not resized according to thumb_width (it used image width) - fixed
! proportional resizing uses thumb_width for max width and height

2010-02-15 Mirjam
+ Added and changed language strings that were hardcoded instead of JText: en-GB and nl-NL
# upgrade now possible without legacy turned on - changed getting db-prefix

---------------- 2.1.0 beta -- svn 956 -- 2009-09-17 -------------

2008-12-14 John Caprez
+ added advanced SEF that uses category name and item title instead if its id (names and titles must ne unique!)

2008-12-12 John Caprez
+ video functionality 
  mov,flv and avi files can be uploaded. 
  To be able to convert video files a conversion application must be set up.
  The converter is not part of the RSGallery2 package.
  The predefined settings are for the ffmpeg converter (http://ffmpeg.mplayerhq.hu/)
  To display the videos the open source flv player is used (http://www.osflv.com/)
  

2008-11-24 John Caprez
^ using default meta page title instead of "RSGallery2"
# backend gallery ordering
# template manager deleting wrong templates
^ using SecurityImages 5.0 API
# fixed gallery pagination
+ SEF urls for gallery and item pagination (see site/router.php for details) 
- Lightbox popup
+ Mootools popup
! changes in the way the watermarked image url is retreived
+ many new translation

2008-07-15 Ronald Smit
+ Added slideshow phatfusion to distribution

2008-05-17 John Caprez
+ added router for SEF Url
+ lots of untranslated text
^ watermarked images are now stored and cached with different names to avoid collisions on high trafic sites
# fixed breadcrumb links
# fixed various voting/comenting bugs

2008-05-16 Jonah Braun
 # fixed path naming in template installer

2008-04-11 John Caprez 
 + added template manager to the new template installer
 # fixed voting lockup and configuration
 # fixed HTML matadata and pathway 
 - removed image/pagebreak/read-more buttons from WYSIWYG editor
 + added WYSIWYG editor for image upload
 ^ using J1.5 translation framework
 ! the old language files have been converted by a script.


2008-03-17 Jonah Braun
 + Clean port of J1.5.1 com_installer to be the new template installer. 
 * Allow public users to comment switch now works. 
 + Added cleanStart option to slideshowone
 # fixed display_thumbs_maxPerPage when set to 0 to show all thumbs
 + Made saving config way better.  This should improve installation time as well.

2008-02-22 Jonah Braun
 # fixing delete comment bug

2008-02-01 Jonah Braun
 # description at top of gallery is now gallery description and not always component text

---------------- 1.14.1 alpha -- svn 584 -- 2008-01-18 -------------

2008-01-14 Ronald Smit
 + Cleaned up maintenance section
 + Added language strings for maintenance section in english.php

2008-01-03 John Caprez
 + added WYSIWYG editor in MyGallery
 # fixed permissions for gallery creation in MyGallery

2007-12-26 John Caprez
 + added new template installer for J1.0 (template parameters are accessible through $this->params)
 # fixed some css issues
 + added voting permissions by gallery (see sql/upgrade_1.14.0_to_1.14.1.sql for neccesary database changes)

2007-12-20 Jonah Braun
 # Fixed uninstalling rsgTemplates in J1.5
 ^ Slideshow option only shows when more than one item is present.

2007-12-17 Ronald Smit
 + Added Zoom Gallery Migrator
 + Added description to lightbox++ popup
 + Added border and dropshadow to thumbs
 
2007-12-16 Ronald Smit
 + Added language strings
 + Added Lightbox++ popup to replace Highslide
 
2007-12-11 Ronald Smit
 + Started repairing Migration Option
 + Added Migration for Pony Gallery ML 2.4.1
 + Added Basic Search option for images
 ^ Cleaned up some code
 ! Only Pony Gallery Migration is working! Rest will follow

2007-12-07 Ronald Smit
 + Cleaned up admin.rsgallery2.php
 ^ Changed Control Panel to actually use the config class.
 + Removed batchupload from admin.rsgalelry2.php and moved to images.php
 ! I tagged a lot of functions to delete. After review they will be cleaned up.
 
2007-12-05 John Caprez
 # fixed bug in commenting
 ^ italian language file

2007-12-03 John Caprez
 # consistent pagination troughout all frontend 

2007-12-01 John Caprez
 # special char issue solved
 # improved PHP4 compatability

2007-11-26 Ronald Smit
 + Added new slideshow option, called slideshow_parth
 + Added switch in backend to select the slideshow option.
 ^ Cleaned out some obsolete backend stuff

2007-11-23 Ronald Smit
 + Added Maintenance section to the backend
 + Added Optimize tables, Regenerate thumbs and Consolidate database.
 ! Consolidate Database is not complete yet.
 ! Language strings not added yet.

2007-11-23 John Caprez
 # Gallery and item page navigation running parallely are handled propperly now
 
2007-11-20 John Caprez
 + New template manager for J1.5 (J1.0 will have same template manager as before)
 ^ Updated most links to have & replaced by &amp;
 + Added option to order thumbnails by date/name/rating/hits/default ascending or descending
 # unpublished items do not appear in random, latest and gallery thumb anymore
  
2007-11-18 Ronald Smit
 + Added EXIF configurable support to front- and backend, without need to have EXIF compiled into PHP

2007-11-15 Ronald Smit
 - Cleaned out display.class.php of both meta and semantic.
 ! Ready to remove tempDisplay from metatemplate. Last testing.

2007-11-15 Ronald Smit
 ^ Step one of the complete rewrite of My Galleries.
 ^ Moved all My Galleries related code into new library, called mygalleries
 ! Not finished yet. Marked all obsolete functions with X, deleting will follow after testing.

2007-11-15 John Caprez
 # Corrected SQL query to match data type when adding or editing gallery in front end.

2007-11-14 John Caprez
 # Recognition of file extension is now case insensitive. caused "wrong file format" error. 

---------------- 1.14.0 alpha -- svn 374 -- 2007-11-12 -------------

2007 Jonah Braun
 + Major updates to core functionality
 ^ Items are generic and no longer assumed as images.  Basic support for MP3 added.
 + Many objects are now cached resulting in a significant performance boost
 + Templating is more robust and powerful
 - Removed template Tables.  Use Semantic instead.
 - Removed or deprecated much legacy funcitonality
 ^ Moved J1.5 backport code to the J15Backort project
 ! Thanks to the many other contributers from the community

2007-10-30 John Caprez
 ^ Made semantic template XHTML compliant.
 ^ Modified j15backport to allow script and style inclusion to head section (XHTML compliance)
 ! Style inclusion is buggy in J1.5RC3 wil be fixed in J1.5RC4
 + Added mootools files to j15backport include folder
 ^ Using mootools instead of overlib for tooltips
 - Various PHP warnings

2007-10-29 Daniel Tulp
 ^ change script.js to script.php for language constant usage
 + added language constants
 
2007-10-20 John Caprez
 ^ process all url in semantic template with sefRelToAbs
 
2007-10-20 John Caprez
 # Fixed double sefRelToAbs on gallery name url

2007-10-19 John Caprez
 - removed border attribute from images for XHTML compliance
 # fixed access check for registered users
 
2007-09-22 Ronald Smit
 + Completed voting system
 + Added backend settings for voting
 + Added language strings for commenting and voting to front- and backend
 
2007-09-19 Ronald Smit
 + Added voting class to SVN
 
2007-07-05 Ronald Smit
 + Added fille joomla15.php and included this in init.rsgallery2.php
 - Removed all J15 mimicing out of init.rsgallery2.php
 
2007-06-23 Jonathan DeLaigle
 - Removed pattemplate usage from admin config

2007-06-17 Ronald Smit
 + Added delete option to frontend commenting system
 
2007-06-11 Ronald Smit
 + Added fullblown commenting system, based on BBCode.
 ! Requires database changes from file upgrade_1.12.2_to_1.13.2.sql
 
2007-06-06 Jonah Braun
 + added template photoBox
 # fixed meta tags.  rsgDisplay->metadata(); must be called from the templates index.php.
 - removed JPATH_RSGALLERY2_TEMPLATE, multiple templates can now be used on the same page.

---------------- 1.13.1 alpha -- svn 41 -- 2007-05-24 -------------

2007-05-24 Daniel Tulp
 ^ Added language definitions to english.php
 ^ Replaced hardcoded strings with language definitions
 
2007-05-23 Jonah Braun
 # Fixed undefined imageUploadError by moving the class to file.utils.php.
 ^ Added note to watermarking advising it is not to be used on production sites.

2007-05-21 Jonah Braun
 # Altered microMacro template to use image titles instead of filenames.

2007-05-20 Ronald Smit
 + Added move images function to backend image manager

---------------- 1.13.0 alpha -- svn 25 -- 2007-05-18 -------------

2007-05-04 Jonah Braun
 + added template Super Clean

2007-04-27 Ronald Smit
 # Fixed frontend ZIP-upload routine.

2007-04-02 Jonah Braun
 ^ templating system modified, approaching finished state
 + added templates: Tables, Simple Flash, debug_ListEverything
 
2007-03-24 Jonah Braun
 ^ moved config functions to new rsgOption config
 + added access check to certain functions
 ! only admin and super admin can access config, migration, uninstall and db debug features

2007-03-24 Daniel Tulp
 + Added new language definitions to english.php
 ^ Replaced hardcoded text strings in code with new language definitions

2007-03-24 Ronald Smit
 + Added allowed filetypes to config area
 - Removed some obsolete code
 
2007-03-23 Jonah Braun
 ^ db optimize: changed count(*) to count(1)
 + modified utils architecture for multiple file type handling

2007-03-22 Ronald Smit
 # Fixed presentation error not filling out gallery block in single view.
 
2007-03-20 Jonah Braun
 + gallery xml output and templating system
 + Todd Dominey's free flash slideshow

2007-03-17 Ronald Smit
 + Added Reset hits option in backend
 
2007-03-11 Ronald Smit
 ^ Rewritten FTP upload routine
 ^ Rewritten parts of template system to ease use of galleryblocl templating
 # Added core of customized galleryblock layout to templating system
 
2007-03-08 Ronald Smit
 # Fixed HTML tags in frontend thumbs and display image
 
2007-03-02 Ronald Smit
 # Fixed pathway support
 + Added download link
 # Fixed switch for status icons
 # Fixed clickable thumbnails in main gallery page
 
2007-03-01 Ronald Smit
 + Added better upload size checking.
 
2007-02-27 Ronald Smit
 + Added gallery details in frontend
 + Added owner name of gallery to frontend representation
 
2007-02-26 Ronald Smit
 + Added option to change owner of gallery in backend
 
2007-02-24 Ronald Smit
 # Fixed missing pagination in frontend
 ^ Moved footer out of template
  
2007-02-23 Ronald Smit
 + Added box option in template $tpl->showMainGalleries('box').
 + Added readable error statements when uploading files.
 
2007-02-21 Ronald Smit
 ^ Rewritten batch upload system. Should be more stable now.
 ! Error trapping is not complete yet!
 # Fixed show random/latest not switchable from backend
 # Fixed missing slideshow link.
 
2007-02-20 Ronald Smit
 # Fixed bug in template system when tabs are not activated.
 
2007-02-19 Ronald Smit
 + Added template file for display image
 
2007-02-18 Daniel Tulp
 + Added html editing functions dor templating system
 ^ Altered CSS editing functions to work with templating system
 - removed CSS edit button on cpanel

2007-02-18 Ronald Smit
 + Added transparency to Watermark text, including backend settings.
 
2007-02-17 Ronald Smit
 + Added templating system for frontend.

---------------- 1.12.2 alpha -- svn 587 -- 2007-02-13 -------------

2007-02-14 Ronald Smit
 # Removed shadow borders around thumbs due to issues in some browsers.

2007-02-12 Jonah Braun
 ^ fixed 1.11.0 to 1.11.1 upgrade bug
 ^ fixed slideshow bug, thanks Alex Boone
 ^ fixed gallery thumbnail list in Gallery: New/Edit when no images are present

2007-02-11 Ronald Smit
 + Added option to select watermarking font
 
2007-02-08 Ronald Smit
 + Fixed frontend problem in IE with new CSS borders.

2007-02-07 Ronald Smit
 + Added nice CSS border around thumbs and display image

2007-02-01 Jonah Braun
 # added fix for possible hardcoded table prefix for #__rsgallery2_acl
 ^ rewrote upgrade version detection because of existing logic flaw

2007-01-30 Jonah Braun
 - removed method calls in complex syntax to support older php versions

2007-01-29 Ronald Smit
 ^ Reformatted the Configuration tabs, using fieldsets to organize for cleaner look

2007-01-28 Daniel Tulp
 ^ Limitbox default value can now be set by administrator and limitbox can be turned off/if/on

2007-01-27 Ronald Smit
 # Fixed bug where uploading images with "Keep Original Images" set to No, goes wrong.

2007-01-26 Jonah Braun
 ^ Finished rsgConfig.  Ready for general use now.

2007-01-25 Jonah Braun
 # Fixed init.rsgallery2.php so that it can be used inside modules.

2007-01-21 Ronald Smit
 # Fixed faulty version detection upon install with multiple Joomla installation in one DB, causing upgrade on a non-existing installation.
 
2007-01-21 Daniel Tulp
 + Added missing language constants to english.php
 ^ Converted hardcoded text strings to new language constants
 # Removed not needed inclusion of the configuration.php file in rsgallery2.html.php
 
2007-01-20 Ronald Smit
 + Added filecount in frontend including subdirectories
 + Added showing thumbs from subgalleries also in random setting.
 
2007-01-14 Ronald Smit
 + Added possibility to select gallery thumbnail from top or subgalleries
 
2007-01-08 Ronald Smit
 + Added status feature for galleries. It shows a number of status icons.
 + Added backend switch for status icons
 + Added tooltips to the status icons
 
2007-01-07 Ronald Smit
 + Added "Create Database Entry" option within the Consolidate Database functionality.(Limited for now, one image at a time)
 # Fixed frontend upload bug.
 
2007-01-06 Ronald Smit
 # Fixed consolidate database bug, not creating missing images.
 # Fixed error in call to deleteImage in img.utils.php
 + Added watermarking to popup image.
 
2007-01-05 Ronald Smit
 # Fixed error when using FTP upload
 # Fixed not working Save button in frontend gallery creation.
 
---------------- 1.12.1 alpha -- svn 530-- 2007-01-04 ----------------

2007-01-04 Jonah Braun
 # fixed 1.12.0 to 1.12.0 upgrade bug
 ^ changed upgrade detect and migrate routines
 # fixed minor error in upgrade_1.11.10_to_1.11.11.sql
 # fixed pat-Error when viewing configuration

2007-01-04 Ronald Smit
 + Added error checking to FTP upload.
 + Added some global variables for the imagepaths in init.rsgallery2.php.

2007-01-03 Jonah Braun
 # fixed paging bug for old version of php

2007-01-03 Ronald Smit
 # Fixed upload bug with open_basedir restrictions.
 # Fixed bug in backend upload sequence where empty file fields generate errors.

---------------- 1.12.0 alpha -- svn 520-- 2007-01-03 ----------------

2007-01-02 Jonah Braun
 # fixed unpublished gallery showing bug
 + added missing pagenav for frontend gallery listing

2007-01-02 Ronald Smit
 + Added advanced ordering for View Images screen and View Galleries screen
 + Added more information in the mouseover within View Galleries (Title and description)
 # Fixed wrong reference to upgrade SQL in install class file.
 
2006-12-29 Ronald Smit
 # Fixed frontend delete gallery bug.
 # Finished ACL into the frontend gallery creation process.
 
2006-12-22 Ronald Smit
 + Implemented images class
 + Rewritten single file uploads
 
2006-12-20 Ronald Smit
 + Added new images class and upgrade script for DB changes

2006-12-18 Jonah Braun
 + added option to show image names under thumbnails
 + added gallery description to gallery view
 # fixed false db record entry on new image import fail

2006-12-16 Ronald Smit
 # Fixed bug not being able to delete image from the frontend.
 # Normalized tables a bit more.
 
2006-12-15 Ronald Smit
 # Fixed bug overwriting user ID when editing gallery details in backend
 
---------------- 1.11.11 alpha -- svn 489-- 2006-12-12 ----------------
2006-12-12 Jonah Braun
 + cleaned up initialization, created init.rsgallery2.php

2006-12-6 Ronald Smit
 + Added upgrade SQL for ACL support
 + Added upgrade option in install.class.php
 # Fixed some small bugs in Titleblock in frontend
 # Fixed array_combine() error for PHP 4.x
 
2006-12-5 Ronald Smit
 + Added ACL support for uploading, editing and deleting images from frontend
 + Added new icons for frontend filelist
 
2006-12-4 Ronald Smit
 + ACL enabled gallery select dropdown box for image upload in frontend.
 
2006-12-3 Ronald Smit
 # Fixed last JPATH_ issues
 # Fixed sefRelToAbs error in backend. As the class is used in both front- and backend, removed it from the class and introduced it into the frontend where needed.

2006-11-21 Ronald Smit
 + Defined RSGallery2 globals out of Joomla 1.5 globals (
 + JPATH_RSGALLERY2_SITE(frontend absolute path to component directory) and JPATH_RSGALLERY2_ADMIN(Backend absolute path to component directory)

2006-11-16 Ronald Smit
 # Fixed error using rsgAccess on accessing main gallery page
 # Fixed strange characters while unzipping a file in /media
 + Added switch options for My Galleries and Create Galleries in backend
 
2006-11-13 Jonah Braun
 # fixed big for multiple gallery delete

2006-11-13 Jonah Braun
 + recursive gallery deletion now works properlly; deletes all sub galleries and images
 # minor fix to ACL class

2006-11-12 Ronald Smit
 + Added some extra functionality to the ACL class.
 # Fixed some backend upload problems.
 + Added ACL switch to backend control panel and removed some unused user switches.
 
2006-11-10 Ronald Smit
 # Fixed bug in frontend layout, because of div problems
 # Fixed bug with display image not beeing generated on upload
 ^ Changed code for warningbox, making it easier and smaller.
 
2006-11-08 Ronald Smit
 + Option in backend to select No popup, normal popup or fancy popup. Defaults to normal to avoid errors in IE6
 + Added corresponding language variables to english.php
 # Fixed reordering bug as mentioned by Borislav
 
2006-11-06 Daniel Tulp
 ^ Simplyfied chinese, Greek language, French, Russian, Italian, Dutch, German, Polish language files have been updated through the course of weeks
 ^ Hopefully last hardcoded language strings have been removed 

2006-11-06 Ronald Smit
 ^ Started introdcuing Joomla 1.5 globals in backend to prepare for migration already
 ^ Cleaned up some unused code in backend.
 
2006-11-04 Ronald Smit
 + Added deletion of images not in the database in the Consolidate Database function.
 + Added checkboxes to Consolidate Database function (Not working yet!)
  
2006-11-09 Jonah Braun
 ^ Updated Czech language.  Thanks David Zirhut
 # partially cleaned up toolbars.

2006-10-31 Ronald Smit
 # Fixed upload error in backend, stating "invalid argument supplied in foreach()"
 + Added extra replace check for <BR> into <br /> for HTML gallery description.
 
2006-10-30 Ronald Smit
 + Added HTML support for the gallery description.
 
2006-10-29 Ronald Smit
 + Save and update routine for Access Control implemented
 + SQL statement for #__rsgallery2_acl added to rsgallery2.sql (commented out for now)
 
2006-10-28 Ronald Smit
 + Added permissions HTML to New/Edit Gallery Screen
 + Added some functions to access.class.php

---------------- 1.11.10 alpha -- svn 393-- 2006-10-26 ----------------
2006-10-26 Daniel Tulp
 ^+ Total language translation complete
 # Added a lot of Itemid's to links

2006-10-24 Jonah Braun
 + added classes rsgGalleryManager, rsgGallery and rsgAccess for improved galleries handling.  not yet complete.
 + added frontend function listEverything to test aformentioned features

2006-10-20 Daniel Tulp
 ^ Traditional Chinese language update by Sun Yu (Meto Sun)

2006-10-18 Daniel Tulp
 + added italian language file
 ^ changed francais.php to french.php and file is now completely translated
 + added Edit CSS feature
 
2006-10-15 Daniel Tulp
 + Greek language file added (by Charis)
 ^ Brazilian Portuguese language file updated

2006-10-15 Ronald Smit
 # Fixed frontend notice "Undefined property: showdownload" in frontend
 # Moved download button to bottom of display images, to prevent layout problems
 ^ Replaced download class with leaner script. Now works in all browsers.
 
2006-10-14 Ronald Smit
 # Fixed backend control panel toolbar buttons in upload and batchupload
 
---------------- 1.11.8 alpha -- svn 366-- 2006-10-14 ----------------
2006-10-11 Ronald Smit
 + Added download link to display image. User can set show/hide in Configuration Area
 ! Download option does not show correct filename in Opera.

2006-10-05 Daniel Tulp
 ^ Changed all links in frontend to SEF urls
 ^ Help content is now available (basic RSgallery2 use)

2006-10-05 Ronald Smit
 + Added download class to config.rsgallery2.php to facilicate downloads
 
2006-10-05 Jonah Braun
 ^ updated Polish and Norwegian.  Thanks: Zbyszek Rosiek and Ronny Tjelle

2006-10-04 Ronald Smit
 + Added page navigation to main gallery page.
 + Added Highslide image popup for display image 

2006-10-03 Jonah Braun
 + added Really Uninstall option.  only for *nix and default image directories at this point.

2006-10-02 Jonah Braun
 # fixed installer compatibility bug for MySQL 3.x and 4.0.x
 # fixed multiple sql file upgrading bug

2006-10-01 Ronald Smit
 # Fixed upload issues from backend and added field validation and MosToolbar support
 # Fixed My galleries link showing up when it was disabled in background. Thanks to Daniel!
 # Fixed the use of single and double quotes in Introduction text of RSGallery2.

2006-09-28 Daniel Tulp
 + added traditional Chinese language. thanks Sun
 ^ changed dutch translation credits

---------------- 1.11.8 alpha -- svn 331-- 2006-09-22 ----------------
2006-09-21 Ronald Smit
 # Fixed image description bug, thanks to "Thundernail".
 + Added upgrade routine for extra field in #__rsgallery2_galleries
 
2006-09-19 Ronald Smit
 # Fixed upload bug, refering to nonexistent isAllowedFileType function
 
2006-09-15 Ronald Smit
 + Added support for selectable thumb image for front end gallery view. Only possible in backend for now.
 + Added version.rsgallery2.php with version information class for better version checking and upgrading
 # Fixed Overlib error in backend
 
2006-09-13 Ronald Smit
 # Fixed single file upload bug in backend
 + Updated rsgallery2.xml to reflect addition of norwegian.php

2006-09-12 Jonah Braun
 + added Norwegian translation.  thanks: Steinar Vikholt

---------------- 1.11.7 alpha -- svn 317 -- 2006-09-12 ----------------

2006-09-11 Ronald Smit
 ^ Completely rewritten rsgallery2.php and rsgallery2.html.php
 + Created class galleryUtils in config.rsgallery2.php and moved all relevant functions in there
 ^ Cleaned up config.rsgallery2.php
 + Added page navigation to imagelist in My Galleries
 
2006-09-02 Ronald Smit
 + Added fileHandler class to clean up zip-handling and ftp-handling
 ! Upload system does not work through class yet, except ZIP-upload in backend.
 
2006-08-31 Ronald Smit
 * Fixed variable initialization to support Register Global Emulation to be set to Off
 
2006-08-29 Ronald Smit
 + Added the option to create square thumbnails in stead of proportional thumbs

2006-08-27 Ronald Smit
 # Fixed user uploads settings. My Galleries shows up now according to settings in background.

2006-08-26 Ronald Smit
 + Added Consolidate Database routine. Report generation, generate missing images and delete from database all work now
 # Watermarking works with gif now
 
2006-08-20 Ronald Smit
 # Fixed "font not found" error while using watermarking
 + Added font directory and arial.ttf file
 
2006-08-08 Jonah Braun
 * disabled commenting if commenting turned off

---------------- 1.11.6 alpha -- svn 299 -- 2006-07-21 ------------------

2006-07-21 Jonah Braun
 # fixed gallery add/edit toolbar bug

---------------- 1.11.5 alpha -- svn 298 -- 2006-07-20 ------------------

2006-07-20 Jonah Braun
 + added view changelog feature for frontend (must be in debug mode)
 ^ eliminated RSG2 path, Joomla path used now.  thanks: Jeckel
 # fixed gallery thumbnail + Joomfish bug.  thanks: Carsten Nikiel

2006-07-20 Jonah Braun
 + backend pathway hack when using $rsgOption
 # fixed category name display in pathway.  thanks: Brad Waite
 # fixed category name display in category listing.  thanks: TonyW

2006-07-09 Ronald Smit
 + Basic Watermarking added, only jpg for now and only on the display image

2006-07-04 Jonah Braun
 ^ updated spanish translation
 ^ updated french translation

---------------- 1.11.4 alpha -- svn 285 -- 2006-06-29 ------------------

2006-06-29 Jonah Braun
 * hardened language files
 + added Spanish translation

---------------- 1.11.3 alpha -- svn 280 -- 2006-06-29 ------------------

2006-06-29 Dani� Tulp
 * secured rsgallery2.html.php against possible execution of arbitrary code.

2006-06-16 Jonah Braun
 ^ default name for image is now filename minus extension

---------------- 1.11.2 alpha -- svn 0272 -- 2006-06-13 ------------------

2006-06-13 Jonah Braun
 # fixed gallery filter and move options in Manage Images
 # replaced config.rsgallery2.php:showCategories2() with galleriesSelectList().  this fixes various bugs when selecting a gallery

---------------- 1.11.1 alpha -- svn 0269 -- 2006-06-08 ------------------

2006-06-08 Jonah Braun
 # fixed bug where 1.11.0 created #__rsgallery2_cats by mistake on new installs

---------------- 1.11.0 alpha -- svn 0269 -- 2006-06-08 ------------------

2006-06-07 Jonah Braun
 + new galleries integrated.  sql migration script added.
 + added the new RSG2 logo.  Congrats to Cory "ccdog" Webb for winning the contest!

2006-06-02 Jonah Braun
 + CZECH language

2006-05-26 Jonah Braun
 + new galleries prototype now available.  this should not interfer with the old categories...yet.

2006-05-23 Jonah Braun
 # MacOSX zip files with resource fork information now work

---------------- 1.10.14 alpha -- svn 0244 -- 2006-05-12 ------------------

2006-05-12 Jonah Braun
 + install.class.php now can use sql files for install/imports.  admin:sql/rsgallery2.sql is the sql install file.

2006-05-10 Kaleb Stolee
 + akogallery migration now works.

---------------- 1.10.13 alpha -- svn 0234 -- 2006-05-04 ------------------

2006-05-04 Jonah Braun
 ^ streamlined install routine.  upgrades are now done without asking.

2006-04-28 Dani� Tulp
 + Itemid variable added, Itemid of component is now used throughout the gallery

2006-04-20 Jonah Braun
 ^ backend gallery listing now unlimited.  this is a kludge, overall still buggy

---------------- 1.10.11 alpha -- svn 0215 -- 2006-04-17 ------------------

2006-04-17 Themba Mdakane
 ^ made frontend branding optional

2006-04-17 Jonah Braun
 + added german and brazilian portuguese

---------------- 1.10.10 alpha -- svn 0211 -- 2006-04-15 ------------------

2006-04-15 Jonah Braun
 + added raw config editor
 + added class rsgGallery along with improved gallery editing, WYSIWYG support to come soon
 + new options for thumbnail display: float and table
 ^ made Purge Everything more thorough

2006-04-10 Jonah Braun
 + added new credits tab to the control panel

---------------- 1.10.9 alpha -- svn 0201 -- 2006-04-08 ------------------

2006-04-08 Jonah Braun
 # many small bug fixes
 # version number is accurate after upgrading
 ^ frontend gallery thumbnail display made better, not finished yet though...
 ^ backend config updated

---------------- 1.10.8 alpha -- svn 0195 -- 2006-04-04 ------------------

2006-04-04 Jonah Braun
 ^ changed control panel to be more consistant with Joomla!s.
 + added admin.rsgallery2.css
 + added View rsgConfig, this will become a raw configuration editor for debuging
 # version number now accurate after an upgrade

2006-04-01 Jonah Braun
 # added missing quotes to russian translation
 # fix for ftp upload from http://rsgallery2.net/component/option,com_simpleboard/func,view/id,487/catid,3/

---------------- 1.10.7 alpha -- svn 0185 -- 2006-03-24 ------------------

2006-03-23 Jonah Braun
 + thumbnail and other frontend bits rewritten in semantic html and css

2006-03-23 Jonah Braun
 + gallery listing rewritten in semantic html and css

---------------- 1.10.6 alpha -- svn 0174 -- 2006-03-04 ------------------

2006-03-03 Jonah Braun
 + Netpbm rewrite complete.  works for all supported image types.
 ^ fixed artf3775: category reording fails

---------------- 1.10.5 alpha -- svn 0171 -- 2006-03-04 ------------------

2006-03-02 Ronald Smit
 # ZIP-upload for frontend fixed
 # Category creation in frontend is fixed
 ^ Voting and commenting is now possible anonymously
 ^ Thumbnail in gallery main screen is now showing with right dimensions

2006-02-28 Jonah Braun
 ^ fixed artf3053: saving config says fails but actually saves
 + added view changelog feature

2006-02-24 Jonah Braun
 ^ streamlined installation, there is now one less step.
 + migration plugin arch is now functional

2006-02-23 Jonah Braun
 ^ instances of "settings" renamed "config[uration]"

---------------- 1.10.2 alpha -- svn 0146 -- 2006-02-15 ------------------

2006-02-15 Jonah Braun
 + GD2 and ImageMagick now support all common web image formats.

2006-02-12 Jonah Braun
 + added changelog.php, imported previous changelog.txt
 - removed changelog.txt

---------------- 1.10.1 alpha -- svn 0136 -- 2006-02-11 ------------------

2006-01 Ronald Smit
 + rewrote installation and migration routines
 ^ component, files and database tables renamed to rsgallery2

2006-01 Jonah Braun
 + rewrote all image utility functions.  currently only JPEG with the GD2 library supported

---------------- 1.9.5 alpha -- 2005-10-13 ------------------

2005-10 Jonah Braun
 ^ cleaned img.utils.php
 + rewrote imgUtils::makeThumb()

2005-10 Ronald Smit
 + Rewritten and completed FTP upload code
 + Rewritten saveConfig to match new values in settings.rsgallery.php
 + Added extra field to match $thumbpath in settings.rsgallery.php
 + Populated latest images and latest categories in Control Panel

---------------- 1.9.4 alhpa -- 2005-10-08 ------------------

2005-10 Jonah Braun
 ^ moved is_uploaded_file() check from importImage() to calling function

2005-10 Ronald Smit
 + ZIP upload rewrite complete
 + Added recent images and recent categories overview in Control Panel
 ^ Rewritten some toolbar layouts

2005-10 Tomislav Ribicic
 + added changelog.txt

---------------- 1.9.0-4 alphas -- 2005-09 ------------------

2005 Jonah Braun
 + major reorganization of code

----- RSGallery2 for Joomla! started by Tomislav Ribicic, Jonah Braun circa. 2005-08-17 -----

----- Maintaince and enhancements by Andy "Troozers" Stewart, Richard Foster -----

----- Original RSGallery for Mambo created by Ronald Smit circa. 2004-03-01 -----


2. Copyright and disclaimer
---------------------------
This application is opensource software released under the GPL.  Please
see source code and http://www.gnu.org/copyleft/gpl.html

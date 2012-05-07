<?php
/**
* This file handles the initialization required for core functionality.
* @version $Id: init.rsgallery2.php 1010 2011-01-26 15:26:17Z mirjam $
* @package RSGallery2
* @copyright (C) 2003 - 2010 RSGallery2
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* RSGallery is Free Software
*/
defined( '_JEXEC' ) or die( 'Access Denied.' );

// create global variables in case we are not in the global scope.
global $rsgConfig, $rsgAccess, $rsgVersion, $rsgOption, $mainframe ;


//Set path globals for RSGallery2
define('JPATH_RSGALLERY2_SITE', JPATH_ROOT. DS .'components'. DS . 'com_rsgallery2');
if (!defined('JPATH_RSGALLERY2_ADMIN')){	//might also be defined in router.php is SEF is used
	define('JPATH_RSGALLERY2_ADMIN', JPATH_ROOT. DS .'administrator' . DS . 'components' . DS . 'com_rsgallery2');
}
define('JPATH_RSGALLERY2_LIBS',JPATH_ROOT. DS . 'components' . DS . 'com_rsgallery2' . DS . 'lib');

$app =JFactory::getApplication();
define('JURI_SITE', $app->isSite() ? JURI::base() : JURI::root());

// check if this file has been included yet.
if( isset( $rsgConfig )) return;

// initialize the rsg config file
require_once(JPATH_RSGALLERY2_ADMIN . DS . 'includes' . DS . 'config.class.php');
$rsgConfig = new rsgConfig();

//Set image paths for RSGallery2
define('JPATH_ORIGINAL', JPATH_ROOT . str_replace('/', DS , $rsgConfig->get('imgPath_original')) );
define('JPATH_DISPLAY', JPATH_ROOT. str_replace('/', DS , $rsgConfig->get('imgPath_display')) );
define('JPATH_THUMB', JPATH_ROOT. str_replace('/', DS , $rsgConfig->get('imgPath_thumb')) );
define('JPATH_WATERMARKED', JPATH_ROOT. str_replace('/', DS , $rsgConfig->get('imgPath_watermarked')) );

$rsgOptions_path = JPATH_RSGALLERY2_ADMIN .DS. 'options' .DS;
$rsgClasses_path = JPATH_RSGALLERY2_ADMIN .DS. 'includes' . DS;
    
require_once(JPATH_RSGALLERY2_ADMIN . DS . 'includes' . DS . 'version.rsgallery2.php');
$rsgVersion = new rsgalleryVersion();

//include ACL class
require_once(JPATH_RSGALLERY2_ADMIN . DS . 'includes' . DS . 'access.class.php');
$rsgAccess = new rsgAccess();

// include rsgInstance
require_once(JPATH_RSGALLERY2_ADMIN . DS . 'includes' . DS . 'instance.class.php');

// require file utilities
require_once( JPATH_RSGALLERY2_ADMIN . DS . 'includes' . DS . 'file.utils.php' );
require_once( JPATH_RSGALLERY2_ADMIN . DS . 'includes' . DS . 'img.utils.php' );
require_once( JPATH_RSGALLERY2_ADMIN . DS . 'includes' . DS . 'audio.utils.php' );
require_once( JPATH_RSGALLERY2_ADMIN . DS . 'includes' . DS . 'items' . DS .'item.php' );

// contains misc. utility functions
require_once(JPATH_RSGALLERY2_ADMIN . DS . 'config.rsgallery2.php');
require_once(JPATH_RSGALLERY2_ADMIN . DS . 'includes' . DS . 'gallery.manager.php');
require_once(JPATH_RSGALLERY2_ADMIN . DS . 'includes' . DS . 'gallery.class.php');
require_once(JPATH_RSGALLERY2_LIBS . DS . 'rsgcomments' . DS . 'rsgcomments.class.php');
require_once(JPATH_RSGALLERY2_LIBS . DS . 'rsgvoting' . DS . 'rsgvoting.class.php');

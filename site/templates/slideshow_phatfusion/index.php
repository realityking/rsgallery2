<?php
/**
* Prep for slideshow_mootools
* @package RSGallery2
* @copyright (C) 2003 - 2008 RSGallery2
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* RSGallery2 is Free Software
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );

// bring in display code
$templatePath = JPATH_RSGALLERY2_SITE . DS . 'templates' . DS . 'slideshow_phatfusion';
require_once( $templatePath . DS . 'display.class.php');

$rsgDisplay = new rsgDisplay_slideshow_phatfusion();

$rsgDisplay->showSlideShow();
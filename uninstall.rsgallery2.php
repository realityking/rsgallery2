<?php
/**
* This file handles the uninstall processing for RSGallery.
* @version $Id: uninstall.rsgallery2.php 1010 2011-01-26 15:26:17Z mirjam $
* @package RSGallery2
* @copyright (C) 2003 - 2006 RSGallery2
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* RSGallery is Free Software
**/
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

function com_uninstall(){
	$lang = &JFactory::getLanguage();
	$lang->load('com_rsgallery2');

	echo JText::_('Uninstalled succesfully');
	}
?>

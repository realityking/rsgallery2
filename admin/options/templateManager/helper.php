<?php
/**
 * @version		$Id: helper.php 1010 2011-01-26 15:26:17Z mirjam $
 * @package		Joomla
 * @subpackage	Menus
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

/**
 * RSGallery2 Template Manager Helper
 *
 * @static
 * @package		Joomla
 * @subpackage	Installer
 * @since		1.5
 */
class InstallerHelper
{
	/**
	 * Get HTML string for writable state of a folder
	 *
	 * @param string $folder
	 * @return string
	 */
	function writable( $folder )
	{
		return is_writable( JPATH_ROOT . '/' . $folder )
			? '<strong><span class="writable">'.JText::_( 'Writable' ).'</span></strong>'
			: '<strong><span class="unwritable">'.JText::_( 'Unwritable' ).'</span></strong>';
	}
}

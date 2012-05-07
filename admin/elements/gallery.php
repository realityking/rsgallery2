<?php
/*
* @version $Id: gallery.php 1010 2011-01-26 15:26:17Z mirjam $
* @package RSGallery2
* @copyright (C) 2005 - 2010 RSGallery2
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* RSGallery2 is Free Software
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//Class to create contets of dropdown box for gallery selection in RSGallery2
class JElementGallery extends JElement
{
	function fetchElement($name, $value, &$node, $control_name)
	{
		$db =& JFactory::getDBO();
		$name='gid';	// URL variable to add is 'gid', which is id in #__rsgallery2_galleries

		//Get galleries for optionlist from database
		$query = 'SELECT id as gid, name'
		. ' FROM #__rsgallery2_galleries'
		. ' WHERE published = 1'
		. ' ORDER BY name'
		;
		$db->setQuery( $query );
		$options = $db->loadObjectList();

		//Add default option (no value)
		$options[] = JHTMLSelect::option('',JText::_('ROOT GALLERY'),'gid','name');

		return JHTML::_('select.genericlist',  $options, ''.$control_name.'['.$name.']', 'class="inputbox"', 'gid', 'name', $value, $control_name.$name );
	}
}
<?php
/**
 * @version		$Id: view.php 1010 2011-01-26 15:26:17Z mirjam $
 * @package		RSGallery2
 * @subpackage	Template installer
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * RSGallery2 Template Manager Templates View
 *
 * @package		RSGallery2
 * @subpackage	Template installer
 * @since		1.5
 */

include_once(dirname(__FILE__).DS.'..'.DS.'default'.DS.'view.php');

class InstallerViewTemplate extends InstallerViewDefault
{
	function display($tpl=null)
	{
		/*
		 * Set toolbar items for the page
		 */
		JToolBarHelper::save('saveTemplate');
		JToolBarHelper::apply('applyTemplate');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel( 'manage' );
		JToolBarHelper::help( 'screen.installerTemplate' );
		
		// Get data from the model
		$item = &$this->get('Item');
		
		$this->assignRef('item', $item);
		
		parent::showTemplateHeader();
		parent::display($tpl);
		
	}
	
	function isParamWriteable(){
		
		$templatefile = JPATH_RSGALLERY2_SITE .DS. 'templates' .DS. $this->item->template .DS. 'params.ini';
		return is_writable($templatefile) ? JText::_( 'Writable' ):JText::_( 'Unwritable' );
				
	}
}

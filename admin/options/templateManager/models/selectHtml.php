<?php
/**
 * @package		RSGallery2
 * @subpackage	TemplateManager
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

// Import library dependencies
require_once(dirname(__FILE__).DS.'extension.php');
jimport( 'joomla.filesystem.folder' );

/**
 * RSGallery2 Template Manager Template Model
 *
 * @package		RSGallery2
 * @subpackage	TemplateManager
 * @since		1.5
 */
class InstallerModelSelectHtml extends InstallerModel
{
	/**
	 * Extension Type
	 * @var	string
	 */
	var $_type = 'SelectHtml';
	
	/**
	 * Overridden constructor
	 * @access	protected
	 */
	function __construct()
	{
		global $mainframe;
		
		// Call the parent constructor
		parent::__construct();
		
		// Set state variables from the request
		$this->setState('filter.string', $mainframe->getUserStateFromRequest( "com_rsgallery2_com_installer.templates.string", 'filter', '', 'string' ));
	}
	
	function getItems()
	{
		// Determine template CSS directory
		$dir = JPATH_RSGALLERY2_SITE .DS. 'templates'.DS.$this->template.DS.'html';
		
		// List template .css files
		jimport('joomla.filesystem.folder');
		$files = JFolder::files($dir, '\.php$', false, false);
		
		$this->_items = new stdClass();
		$this->_items->dir = $dir;
		$this->_items->template = $this->template;
		$this->_items->files = $files;
		
		return $this->_items;
	}
}

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
class InstallerModelEditCss extends InstallerModel
{
	/**
	 * Extension Type
	 * @var	string
	 */
	var $_type = 'EditCss';
	
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
	
	function getItem()
	{
		jimport('joomla.filesystem.file');

		// Determine template CSS directory
		$dir = JPATH_RSGALLERY2_SITE .DS. 'templates'.DS.$this->template.DS.'css';
		$file = $dir .DS. $this->filename;

		$content = JFile::read($file);

		if ($content == false)
		{
			JError::raiseWarning( 500, JText::sprintf('Operation Failed Could not open', $client->path.$filename) );
		}
		
		$item = new stdClass();
		$this->item = $item;
		$item->filename = $this->filename;
		$item->content = $content;
		$item->path = $file;
		$item->template = $this->template;
		
		return $item;
	}
	
	function save(){
		
		$app = & JFactory::getApplication();

		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');
		$ftp = JClientHelper::getCredentials('ftp');
		
		$file = JPATH_RSGALLERY2_SITE .DS. 'templates'.DS.$this->template.DS.'css'.DS.$this->filename;
		
		// Try to make the css file writeable
		if (!$ftp['enabled'] && JPath::isOwner($file) && !JPath::setPermissions($file, '0755')) {
			JError::raiseNotice('SOME_ERROR_CODE', 'Could not make the css file writable');
		}
		
		jimport('joomla.filesystem.file');
		$return = JFile::write($file, $this->content);
		
		// Try to make the css file unwriteable
		if (!$ftp['enabled'] && JPath::isOwner($file) && !JPath::setPermissions($file, '0555')) {
			JError::raiseNotice('SOME_ERROR_CODE', 'Could not make the css file unwritable');
		}
		
		if($return)
			$app->enqueueMessage( 'File saved');
		
		return $return;
	}
}







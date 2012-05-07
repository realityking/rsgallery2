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
class InstallerModelTemplate extends InstallerModel
{
	/**
	 * Extension Type
	 * @var	string
	 */
	var $_type = 'template';
	var $template = '';
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
		global $mainframe;
		
		jimport('joomla.filesystem.path');
		if (!$this->template) {
			return JError::raiseWarning( 500, 'Template not specified' );
		}

		$tBaseDir	= JPath::clean(JPATH_RSGALLERY2_SITE .DS. 'templates');

		if (!is_dir( $tBaseDir . DS . $this->template )) {
			return JError::raiseWarning( 500, 'Template not found' );
		}
		$lang =& JFactory::getLanguage();
		$lang->load( 'tpl_'.$this->template, JPATH_RSGALLERY2_SITE );

		$ini	= JPATH_RSGALLERY2_SITE .DS. 'templates'.DS.$this->template.DS.'params.ini';
		$xml	= JPATH_RSGALLERY2_SITE .DS. 'templates'.DS.$this->template.DS.'templateDetails.xml';
		$row	= TemplatesHelper::parseXMLTemplateFile($tBaseDir, $this->template);

		jimport('joomla.filesystem.file');
		// Read the ini file
		if (JFile::exists($ini)) {
			$content = JFile::read($ini);
		} else {
			$content = null;
		}

		$params = new JParameter($content, $xml, 'template');

		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		$ftp =& JClientHelper::setCredentialsFromRequest('ftp');

		$item = new stdClass();
		$item->params = $params;
		$item->row = $row;
		$item->type = $this->_type;
		$item->template = $this->template;

		return $item;

	}
	
	/**
	 * Updates the template parameter file
	 * @access protected
	 */
	function update(){
		
		global $rsgConfig;
		
		$app = & JFactory::getApplication();
		
		if (!$this->template) {
			JError::raiseError(500, "No template specified");
			return;
		}
		
		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');
		$ftp = JClientHelper::getCredentials('ftp');
		
		$file = JPATH_RSGALLERY2_SITE.DS.'templates'.DS.$this->template.DS.'params.ini';
		
		jimport('joomla.filesystem.file');
		if (JFile::exists($file) && count($this->params))
		{
			$txt = null;
			foreach ($this->params as $k => $v) {
				$txt .= "$k=$v\n";
			}
			
			// Try to make the params file writeable
			if (!$ftp['enabled'] && JPath::isOwner($file) && !JPath::setPermissions($file, '0755')) {
				JError::raiseNotice('SOME_ERROR_CODE', JText::_('Could not make the template parameter file writable'));
				return;
			}
			
			$return = JFile::write($file, $txt);
			
			// Try to make the params file unwriteable
			if (!$ftp['enabled'] && JPath::isOwner($file) && !JPath::setPermissions($file, '0555')) {
				JError::raiseNotice('SOME_ERROR_CODE', JText::_('Could not make the template parameter file unwritable'));
				return;
			}
			
		}
		
		$app->enqueueMessage( 'Template saved');
		
	}
}

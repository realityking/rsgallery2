<?php
/**
 * @version		$Id: view.php 1010 2011-01-26 15:26:17Z mirjam $
 * @package		Joomla
 * @subpackage	Menus
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.view');

/**
 * RSGallery2 Template Manager Default View
 *
 * @package		Joomla
 * @subpackage	Installer
 * @since		1.5
 */
class InstallerViewDefault extends JView
{
	function __construct($config = null)
	{
		parent::__construct($config);
		$this->_addPath('template', $this->_basePath.DS.'views'.DS.'default'.DS.'tmpl');
	}

	function display($tpl=null)
	{
		/*
		 * Set toolbar items for the page
		 */
		JToolBarHelper::title( JText::_('RSGallery2 Template Manager'), 'install.png' );

		// Document
		$document = & JFactory::getDocument();
		$document->setTitle(JText::_('RSGallery2 Template Manager').' : '.$this->getName());

		// Get data from the model
		$state		= &$this->get('State');

		// Are there messages to display ?
		$showMessage	= false;
		if ( is_object($state) )
		{
			$message1		= $state->get('message');
			$message2		= $state->get('extension.message');
			$showMessage	= ( $message1 || $message2 );
		}

		$this->assign('showMessage',	$showMessage);
		$this->assignRef('state',		$state);

		JHTML::_('behavior.tooltip');
		parent::display($tpl);
	}

	/**
	 * Should be overloaded by extending view
	 *
	 * @param	int $index
	 */
	function loadItem($index=0)
	{
	}
	
	function showHeader(){
		
		$ext	= JRequest::getWord('type');
		
		$subMenus = array(
				JText::_('Manage') => 'templates'
				);
		
		JSubMenuHelper::addEntry(JText::_('RSG2 Control Panel'), 'index2.php?option=com_rsgallery2', false);
		JSubMenuHelper::addEntry(JText::_('Install'), '#" onclick="javascript:document.adminForm.type.value=\'\';submitbutton(\'installer\');', !in_array( $ext, $subMenus));
		foreach ($subMenus as $name => $extension) {
			JSubMenuHelper::addEntry($name , '#" onclick="javascript:document.adminForm.type.value=\''.$extension.'\';submitbutton(\'manage\');', ($extension == $ext));
		}
		
	}

	function showTemplateHeader(){
		
		$ext	= JRequest::getWord('type', 'templateGeneral');
		if($ext =='templates') $ext = 'templateGeneral';
		
		$subMenus = array(
				JText::_('General') => 'templateGeneral',
				JText::_('CSS') => 'templateCSS',
				JText::_('HTML') => 'templateHTML'
				);

		foreach ($subMenus as $name => $extension) {
			JSubMenuHelper::addEntry($name , '#" onclick="javascript:document.adminForm.type.value=\''.$extension.'\';submitbutton(\'template\');', ($extension == $ext));
		}
		
	}
}
